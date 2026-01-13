<?php
header("Content-Type: application/json");
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

require "db.php";
require "session_check.php";

$sql = "
    SELECT 
        t.transaction_id,
        t.item_id,
        t.type,
        t.quantity,
        t.`date` AS tx_date,   -- FIXED HERE
        t.remarks,
        i.item_name
    FROM inventorytransaction t
    LEFT JOIN item i ON t.item_id = i.item_id
    ORDER BY t.transaction_id DESC
";


$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
