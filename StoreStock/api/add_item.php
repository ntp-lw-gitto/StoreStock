<?php

require "db.php";
require "session_check.php";

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) { $input = $_POST; }


$item_name = trim($input["item_name"] ?? "");
$category_name = trim($input["category"] ?? "");
$quantity = intval($input["quantity"] ?? 0);
$reorder = intval($input["reorder_level"] ?? 0);
$unit_price = floatval($input["unit_price"] ?? 0.00);

if ($item_name === "") {
    echo json_encode(["success" => false, "error" => "Item name required"]);
    exit;
}

//
// CATEGORY HANDLING
//
$cat_id = null;
if ($category_name !== "") {
    $stmt = $conn->prepare("SELECT category_id FROM category WHERE category_name=? LIMIT 1");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $stmt->bind_result($cid);
    if ($stmt->fetch()) {
        $cat_id = $cid;
    }
    $stmt->close();

    // create category if missing
    if ($cat_id === null) {
        $stmt = $conn->prepare("INSERT INTO category (category_name, description) VALUES (?, '')");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $cat_id = $stmt->insert_id;
        $stmt->close();
    }
}

//
// INSERT ITEM
//
$stmt = $conn->prepare("
    INSERT INTO item (item_name, category_id, quantity, unit_price, reorder_level)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param("siidi", $item_name, $cat_id, $quantity, $unit_price, $reorder);
$ok = $stmt->execute();
$id = $stmt->insert_id;
$error = $stmt->error;
$stmt->close();

if ($ok) {
    echo json_encode([
        "success" => true, 
        "item_id" => $id
    ]);
} else {
    echo json_encode([
        "success" => false,
        "sql_error" => $error,
        "received_data" => $input,
        "cat_id" => $cat_id
    ]);
}

?>