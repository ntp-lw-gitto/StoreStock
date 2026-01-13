<?php
header("Content-Type: application/json; charset=utf-8");
require "db.php";
require "session_check.php";

$data = json_decode(file_get_contents("php://input"), true);
$po_id = intval($data["po_id"] ?? 0);

if (!$po_id) {
    echo json_encode(["success" => false, "error" => "Missing po_id"]);
    exit;
}

// Delete PO items first (FK constraint)
$conn->query("DELETE FROM purchaseorderitem WHERE po_id = $po_id");

$ok = $conn->query("DELETE FROM purchaseorder WHERE po_id = $po_id");

echo json_encode([
    "success" => $ok ? true : false,
    "error" => $ok ? null : $conn->error
]);
?>
