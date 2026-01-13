<?php
require "db.php";
require "session_check.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["supplier_id"] ?? 0);
$name = trim($data["supplier_name"] ?? "");
$phone = trim($data["phone"] ?? "");
$email = trim($data["email"] ?? "");
$address = trim($data["address"] ?? "");

$stmt = $conn->prepare("
    UPDATE supplier
    SET supplier_name = ?, phone = ?, email = ?, address = ?
    WHERE supplier_id = ?
");

$stmt->bind_param("ssssi", $name, $phone, $email, $address, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>
