<?php
require '../vendor/autoload.php';
include "db.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$eventId = $_GET['eventId'] ?? "";

$extra = $_GET['extra'] ?? "";
$extraCols = array_filter(array_map('trim', explode(",", $extra)));

if(!$eventId){
    die("No event selected");
}

/* FETCH DATA */
$eventRes = $conn->query("SELECT title FROM events WHERE id='$eventId'");
$event = $eventRes->fetch_assoc();

$result = $conn->query("SELECT * FROM registrations WHERE eventId='$eventId'");

/* CREATE EXCEL */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

/* ✅ ADD IMAGE */
$drawing = new Drawing();
$drawing->setName('Header');
$drawing->setDescription('College Header');

/* 👉 PUT YOUR IMAGE PATH HERE */
$drawing->setPath(__DIR__ . '/../frontend-clean/common/images/header.png');  




$drawing->setHeight(120);
$drawing->setCoordinates('A1');
$drawing->setWorksheet($sheet);

/* START DATA AFTER IMAGE */
$row = 9;

/* EVENT NAME */
$sheet->setCellValue('A'.$row, 'Event: ' . $event['title']);
$row += 2; // space before table

$sheet->mergeCells('A'.$row.':E'.$row);

$row += 1; 

/* HEADERS */
$headers = ["Name","USN","Department","Phone","Role"];

foreach($extraCols as $col){
    $headers[] = $col;
}

$colLetter = 'A';
foreach($headers as $h){
    $sheet->setCellValue($colLetter.$row, $h);
    $colLetter++;
}

$row++;

/* DATA */
while($r = $result->fetch_assoc()){
    $role = ($r['teamId']) ? "Team" : "Individual";

    $sheet->setCellValue('A'.$row, $r['name']);
    $sheet->setCellValue('B'.$row, $r['usn']);
    $sheet->setCellValue('C'.$row, $r['department']);

    $sheet->setCellValueExplicit(
        'D'.$row,
        $r['phone'],
        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
    );

    $sheet->setCellValue('E'.$row, $role);

    /* EXTRA COLUMNS */
    $colLetter = 'F';
    foreach($extraCols as $col){
        $sheet->setCellValue($colLetter.$row, "");
        $colLetter++;
    }

    $row++; // 🔥 VERY IMPORTANT
}

/* DOWNLOAD */
$fileName = ($event['title'] ?? 'event') . "_participants.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>