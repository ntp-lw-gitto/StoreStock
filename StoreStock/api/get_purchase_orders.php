<?php
header("Content-Type: application/json; charset=utf-8");
require "db.php";
require "session_check.php";

$sql = "
SELECT 
    po.po_id,
    po.supplier_id,              -- ADDED supplier_id
    po.order_date,
    po.status,
    s.supplier_name,
    (
        SELECT GROUP_CONCAT(
            CONCAT(i.item_name, ' (', poi.quantity, ')')
            SEPARATOR ', '
        )
        FROM purchaseorderitem poi
        JOIN item i ON poi.item_id = i.item_id
        WHERE poi.po_id = po.po_id
    ) AS items
FROM purchaseorder po
LEFT JOIN supplier s ON po.supplier_id = s.supplier_id
ORDER BY po.po_id DESC
";

$res = $conn->query($sql);
if (!$res) {
    echo json_encode(["error" => "DB error", "detail" => $conn->error]);
    exit;
}

$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);

