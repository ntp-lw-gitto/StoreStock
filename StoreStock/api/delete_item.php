<?php

require "session_check.php";
require "db.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

$id = intval($input["item_id"] ?? 0);

if (!$id) {
    echo json_encode(["success"=>false,"error"=>"Missing ID"]);
    exit;
}

$sql = "DELETE FROM item WHERE item_id = $id";
$ok = $conn->query($sql);

echo json_encode(["success"=>$ok, "error"=>$conn->error]);

