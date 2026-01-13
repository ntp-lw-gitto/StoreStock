<?php
require "db.php";
require "session_check.php";

$result = $conn->query("
    SELECT supplier_id, supplier_name, phone, email, address 
    FROM supplier
    ORDER BY supplier_id DESC
");

$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

echo json_encode($suppliers);
?>
