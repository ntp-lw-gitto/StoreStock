<?php
header("Content-Type: application/json");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require "db.php";
require "session_check.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "error" => "Invalid JSON"]);
    exit;
}

$name = $conn->real_escape_string($input["category_name"] ?? "");
$desc = $conn->real_escape_string($input["description"] ?? "");

if (!$name) {
    echo json_encode(["success" => false, "error" => "Missing name"]);
    exit;
}

$sql = "INSERT INTO category (category_name, description)
        VALUES ('$name', '$desc')";

$ok = $conn->query($sql);

echo json_encode([
    "success" => $ok,
    "error" => $conn->error
]);
