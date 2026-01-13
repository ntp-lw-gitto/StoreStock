<?php
header("Content-Type: application/json");
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

require "db.php";
require "session_check.php";

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "error" => "Invalid JSON"]);
    exit;
}

$item_id = intval($input["item_id"] ?? 0);
$name    = $conn->real_escape_string($input["item_name"] ?? "");
$catName = $conn->real_escape_string($input["category"] ?? "");
$qty     = intval($input["quantity"] ?? 0);
$reorder = intval($input["reorder_level"] ?? 0);
$price   = floatval($input["unit_price"] ?? 0);

if (!$item_id || $name === "") {
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

// -----------------------------------------
// CATEGORY HANDLING: find or create
// -----------------------------------------
$cat_id = null;

if ($catName !== "") {
    $sql = "SELECT category_id FROM category WHERE category_name='$catName' LIMIT 1";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $cat_id = $row["category_id"];
    } else {
        // Create category if missing
        $conn->query("INSERT INTO category (category_name, description) VALUES ('$catName', '')");
        $cat_id = $conn->insert_id;
    }
}

// -----------------------------------------
// UPDATE ITEM
// -----------------------------------------

$sql = "
    UPDATE item SET
        item_name     = '$name',
        category_id   = $cat_id,
        quantity      = $qty,
        unit_price    = $price,
        reorder_level = $reorder
    WHERE item_id = $item_id
";

$ok = $conn->query($sql);

echo json_encode([
    "success" => $ok,
    "error"   => $conn->error
]);
