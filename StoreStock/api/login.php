<?php
session_start();
header("Content-Type: application/json");

require "db.php";

// Debug mode ON (shows errors)
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Read JSON
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!$input) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid JSON",
        "raw" => $raw
    ]);
    exit;
}

$username = $input["username"] ?? "";
$password = $input["password"] ?? "";

if (!$username || !$password) {
    echo json_encode([
        "success" => false,
        "error" => "Missing username or password"
    ]);
    exit;
}

// IMPORTANT: your table is named `user` (singular)
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error" => "SQL prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "error" => "User not found"
    ]);
    exit;
}

$user = $res->fetch_assoc();

// Support *both* hashed and plaintext passwords
$valid =
    password_verify($password, $user["password"]) ||
    $password === $user["password"];

if (!$valid) {
    echo json_encode([
        "success" => false,
        "error" => "Incorrect password"
    ]);
    exit;
}

// Save session
$_SESSION["user_id"] = $user["user_id"];
$_SESSION["username"] = $user["username"];
$_SESSION["role"] = $user["role"];

echo json_encode([
    "success" => true,
    "username" => $user["username"],
    "role" => $user["role"]
]);
