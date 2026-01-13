<?php
header("Content-Type: application/json");
require "db.php";
require "session_check.php";

$sql = "
SELECT 
    c.category_name,
    AVG(i.unit_price) AS avg_price,
    COUNT(i.item_id) AS total_items
FROM item i
LEFT JOIN category c ON i.category_id = c.category_id
GROUP BY c.category_id
ORDER BY c.category_name
";

$res = $conn->query($sql);

$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}

echo json_encode($out);
