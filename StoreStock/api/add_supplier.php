<?php
require "db.php";
require "session_check.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["supplier_name"] ?? "");
$phone = $data["phone"] ?? "";
$email = $data["email"] ?? "";
$address = $data["address"] ?? "";

if ($name === "") {
    echo json_encode(["success" => false, "error" => "Supplier name required"]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO supplier (supplier_name, phone, email, address)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("ssss", $name, $phone, $email, $address);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "supplier_id" => $stmt->insert_id]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>
