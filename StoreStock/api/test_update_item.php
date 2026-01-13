<?php
require "db.php";
require "session_check.php";

$input = json_decode(file_get_contents("php://input"), true);

$id     = intval($input["item_id"]);
$name   = trim($input["item_name"]);
$cat    = trim($input["category"]);
$qty    = intval($input["quantity"]);
$reorder= intval($input["reorder_level"]);
$price  = floatval($input["unit_price"]);

// 1. Get or create category
$stmt = $conn->prepare("SELECT category_id FROM category WHERE category_name=? LIMIT 1");
$stmt->bind_param("s", $cat);
$stmt->execute();
$stmt->bind_result($cat_id);
if (!$stmt->fetch()) {
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO category(category_name) VALUES (?)");
    $stmt->bind_param("s", $cat);
    $stmt->execute();
    $cat_id = $stmt->insert_id;
}
$stmt->close();

// 2. Update item
$stmt = $conn->prepare("
    UPDATE item 
    SET item_name=?, category_id=?, quantity=?, unit_price=?, reorder_level=?
    WHERE item_id=?
");
$stmt->bind_param("siidii", $name, $cat_id, $qty, $price, $reorder, $id);
$ok = $stmt->execute();

echo json_encode(["success" => $ok]);
