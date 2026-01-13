<?php
session_start();

header("Content-Type: application/json");

// If not logged in → respond with JSON 401
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "error" => "Unauthorized"
    ]);
    exit;
}
?>
