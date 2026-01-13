<?php
header("Content-Type: application/json");
require "db.php";
require "session_check.php";

$sql = "
SELECT i.item_name, i.quantity, i.reorder_level,
       c.category_name
FROM item i
LEFT JOIN category c ON i.category_id = c.category_id
WHERE i.quantity < i.reorder_level
ORDER BY i.quantity ASC
";

$res = $conn->query($sql);

$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}

echo json_encode($out);
