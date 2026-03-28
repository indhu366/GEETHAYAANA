<?php
include "db.php";

$result = $conn->query("
SELECT department AS department, COUNT(*) as total 
FROM registrations 
GROUP BY department
");

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>