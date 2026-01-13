<?php
require "db.php";
require "session_check.php";

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input["category_id"]);

$stmt = $conn->prepare("DELETE FROM category WHERE category_id=?");
$stmt->bind_param("i", $id);
$ok = $stmt->execute();

echo json_encode(["success" => $ok]);
