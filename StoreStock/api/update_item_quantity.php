<?php
header("Content-Type: application/json");
require "db.php";
require "session_check.php";

$input = json_decode(file_get_contents("php://input"), true);

$id  = intval($input["item_id"]);
$new = intval($input["new_qty"]);

$sql = "UPDATE item SET quantity = $new WHERE item_id = $id";

echo json_encode(["success" => $conn->query($sql)]);
