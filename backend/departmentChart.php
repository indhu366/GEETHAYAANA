<?php
include "db.php";

$result = $conn->query("
SELECT department, COUNT(*) as total
FROM registrations
GROUP BY department
");

$data = [];

/* DEFAULT VALUES (IMPORTANT) */
$departments = [
    "ECE" => 0,
    "CSE" => 0,
    "CSE(AI&ML)" => 0,
    "EEE" => 0,
    "ISE" => 0,
    "AI&DS" => 0,
    "MBA" => 0   // ✅ ADDED
];

while($row = $result->fetch_assoc()){
    $departments[$row['department']] = $row['total'];
}

/* CONVERT TO ARRAY FORMAT */
foreach($departments as $dept => $count){
    $data[] = [
        "department" => $dept,
        "total" => $count
    ];
}

echo json_encode($data);
?>