<?php
require "db.php";
require "session_check.php";

$input = json_decode(file_get_contents("php://input"), true);

$id   = intval($input["category_id"]);
$name = trim($input["category_name"]);
$desc = trim($input["description"]);

$stmt = $conn->prepare("UPDATE category SET category_name=?, description=? WHERE category_id=?");
$stmt->bind_param("ssi", $name, $desc, $id);
$ok = $stmt->execute();

echo json_encode(["success" => $ok]);
