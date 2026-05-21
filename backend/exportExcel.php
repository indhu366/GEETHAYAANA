<?php
session_start();

$isSuperAdmin = $_SESSION['isSuperAdmin'] ?? false;
$loggedAdmin = $_SESSION['adminEmail'] ?? '';
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
$eventRes = $conn->query("SELECT title, faculty, students FROM events WHERE id='$eventId'");
$event = $eventRes->fetch_assoc();

if($isSuperAdmin){
    // ✅ superadmin → all events
    $result = $conn->query("
        SELECT r.* 
        FROM registrations r
        WHERE r.eventId='$eventId'
        ORDER BY r.teamId
    ");
} else {
    // ✅ faculty → only their event
    $result = $conn->query("
        SELECT r.* 
        FROM registrations r
        JOIN events e ON r.eventId = e.id
        WHERE r.eventId='$eventId' AND e.createdBy='$loggedAdmin'
        ORDER BY r.teamId
    ");
}

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
$row++;

$sheet->setCellValue('A'.$row, 'Faculty Coordinators: ' . $event['faculty']);
$row++;

$sheet->setCellValue('A'.$row, 'Student Coordinators: ' . $event['students']);
$row += 2;// space before table

$sheet->mergeCells('A'.$row.':E'.$row);

$row += 1; 

/* HEADERS */
$headers = ["Name","Phone","Department","Role"];

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
$teams = [];

while($r = $result->fetch_assoc()){

    $key = $r['teamId'] ? $r['teamId'] : $r['usn'];

    if(!isset($teams[$key])){
        $teams[$key] = [
            "team_name" => $r['team_name'] ?? $r['teamId'],
            "names" => [],
            "usns" => [],
            "depts" => [],
            "phones" => [],
            "role" => ($r['teamId']) ? "Team" : "Individual"
        ];
    }

    $teams[$key]["names"][] = $r['name'];
    $teams[$key]["usns"][] = $r['usn'];
    $teams[$key]["depts"][] = $r['department'];
    $teams[$key]["phones"][] = $r['phone'];
}
foreach($teams as $team){

    $sheet->setCellValue('A'.$row, implode("\n", $team['names']));
    $sheet->setCellValue('D'.$row, implode("\n", $team['phones']));
    $sheet->setCellValue('C'.$row, implode("\n", $team['depts']));
    $sheet->setCellValue('E'.$row, $team['role']);


    $sheet->getStyle('A'.$row.':D'.$row)->getAlignment()->setWrapText(true);

    // Wrap text (IMPORTANT for newline)
    $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);

    /* EXTRA COLUMNS */
    $colLetter = 'F';
    foreach($extraCols as $col){
        $sheet->setCellValue($colLetter.$row, "");
        $colLetter++;
    }

    $row++;
}
/* DOWNLOAD */
$fileName = ($event['title'] ?? 'event') . "_participants.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>