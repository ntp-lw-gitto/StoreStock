<?php
header("Content-Type: application/json");
require "db.php";
require "session_check.php";

$data = json_decode(file_get_contents("php://input"), true);

$start = $data["start"] ?? null;
$end   = $data["end"] ?? null;

$sql = "
SELECT 
    t.date,
    i.item_name,
    t.type,
    t.quantity,
    t.remarks
FROM inventorytransaction t
LEFT JOIN item i ON i.item_id = t.item_id
WHERE 1
";

if ($start) $sql .= " AND t.date >= '$start'";
if ($end)   $sql .= " AND t.date <= '$end 23:59:59'";

$sql .= " ORDER BY t.date DESC";

$res = $conn->query($sql);

$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}

echo json_encode($out);
