<?php
require "db.php";
require "session_check.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["supplier_id"] ?? 0);

$stmt = $conn->prepare("DELETE FROM supplier WHERE supplier_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>
