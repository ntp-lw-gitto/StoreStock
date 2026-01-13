<?php
header("Content-Type: application/json; charset=utf-8");
require "db.php";
require "session_check.php";

$data = json_decode(file_get_contents("php://input"), true);

$po_id = intval($data["po_id"] ?? 0);
$supplier_id = intval($data["supplier_id"] ?? 0);
$order_date = trim($data["order_date"] ?? "");
$status = trim($data["status"] ?? "Pending");

if (!$po_id || !$supplier_id || $order_date === "") {
    echo json_encode(["success" => false, "error" => "Missing data"]);
    exit;
}

$stmt = $conn->prepare("UPDATE purchaseorder SET supplier_id = ?, order_date = ?, status = ? WHERE po_id = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]);
    exit;
}
$stmt->bind_param("issi", $supplier_id, $order_date, $status, $po_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}
