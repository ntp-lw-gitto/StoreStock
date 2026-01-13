<?php
header("Content-Type: application/json");
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

require "db.php";
require "session_check.php";

// Read JSON
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "error" => "Invalid JSON"]);
    exit;
}

$item_id  = intval($input["item_id"] ?? 0);
$type     = strtolower($input["type"] ?? "");
$quantity = intval($input["quantity"] ?? 0);
$note     = $conn->real_escape_string($input["note"] ?? "");

if (!$item_id || !$type || !$quantity) {
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

// Insert transaction
$sql = "INSERT INTO inventorytransaction (item_id, type, quantity, remarks)
        VALUES ($item_id, '$type', $quantity, '$note')";

$ok = $conn->query($sql);

echo json_encode([
    "success" => $ok,
    "error" => $conn->error
]);
