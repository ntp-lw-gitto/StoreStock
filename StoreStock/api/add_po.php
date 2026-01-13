<?php
// StoreStock/api/add_po.php
header("Content-Type: application/json; charset=utf-8");

// avoid PHP warnings printed to the response
ini_set("display_errors", 0);
ini_set("log_errors", 1);
error_reporting(E_ALL);
require "db.php"; // ensure this is the correct db.php for MAMP (root/root, port 8889) and located at StoreStock/api/db.php
require "session_check.php";

$raw = file_get_contents("php://input");
if ($raw === false || $raw === "") {
    echo json_encode(["success" => false, "error" => "Empty request body"]);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode(["success" => false, "error" => "Invalid JSON input", "raw" => substr($raw, 0, 400)]);
    exit;
}

// Accept either:
// 1) { supplier_id, order_date, items: [ { item_id, quantity, price? }, ... ] }
// 2) { supplier_id, order_date, item_id, quantity }  (single-line quick PO)
$supplier_id = intval($data['supplier_id'] ?? 0);
$order_date  = trim($data['order_date'] ?? ""); // must use order_date (your SQL)
$items = [];

// Normalize items
if (!empty($data['items']) && is_array($data['items'])) {
    foreach ($data['items'] as $it) {
        $iid = intval($it['item_id'] ?? 0);
        $qty = intval($it['quantity'] ?? 0);
        $price = isset($it['price']) ? floatval($it['price']) : 0.0;
        if ($iid > 0 && $qty > 0) $items[] = ['item_id'=>$iid, 'quantity'=>$qty, 'price'=>$price];
    }
} elseif (!empty($data['item_id']) && !empty($data['quantity'])) {
    $iid = intval($data['item_id']);
    $qty = intval($data['quantity']);
    $price = isset($data['price']) ? floatval($data['price']) : 0.0;
    if ($iid > 0 && $qty > 0) $items[] = ['item_id'=>$iid, 'quantity'=>$qty, 'price'=>$price];
}

// Validate
$errors = [];
if ($supplier_id <= 0) $errors[] = "supplier_id is required and must be > 0";
if ($order_date === "") $errors[] = "order_date is required (use YYYY-MM-DD)";
if (count($items) === 0) $errors[] = "At least one valid item (item_id + quantity) is required";

if ($errors) {
    echo json_encode(["success" => false, "error" => "Missing data", "details" => $errors]);
    exit;
}

// Start DB transaction
$conn->begin_transaction();

try {
    // Insert purchaseorder (order_date column exists in your SQL)
    $poStmt = $conn->prepare("INSERT INTO purchaseorder (supplier_id, order_date, status) VALUES (?, ?, 'Pending')");
    if (!$poStmt) throw new Exception("Prepare PO failed: " . $conn->error);
    $poStmt->bind_param("is", $supplier_id, $order_date);
    if (!$poStmt->execute()) throw new Exception("Insert PO failed: " . $poStmt->error);
    $po_id = $poStmt->insert_id;
    $poStmt->close();

    // Insert PO items
    $poiStmt = $conn->prepare("INSERT INTO purchaseorderitem (po_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$poiStmt) throw new Exception("Prepare PO item failed: " . $conn->error);

    foreach ($items as $it) {
        $iid = intval($it['item_id']);
        $qty = intval($it['quantity']);
        $price = floatval($it['price'] ?? 0.0);
        if ($iid <= 0 || $qty <= 0) throw new Exception("Invalid PO item data");
        $poiStmt->bind_param("iiid", $po_id, $iid, $qty, $price);
        if (!$poiStmt->execute()) throw new Exception("Insert PO item failed: " . $poiStmt->error);
    }
    $poiStmt->close();

    // Commit
    $conn->commit();
    echo json_encode(["success" => true, "po_id" => $po_id]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("add_po.php error: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Server error while creating PO", "detail" => $e->getMessage()]);
}


