<?php
require "db.php";
require "session_check.php";

$id = intval($_GET["id"] ?? 0);

$stmt = $conn->prepare("
    SELECT item_id, item_name, quantity, reorder_level, unit_price,
           COALESCE(category.category_name, '') AS category
    FROM item
    LEFT JOIN category ON item.category_id = category.category_id
    WHERE item_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

$res = $stmt->get_result();
echo json_encode($res->fetch_assoc());
