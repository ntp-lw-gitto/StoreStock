<?php
require "db.php";
require "session_check.php";

$sql = "SELECT category_id, category_name, description FROM category ORDER BY category_id DESC";
$result = $conn->query($sql);

$cats = [];
while ($row = $result->fetch_assoc()) {
    $cats[] = $row;
}

echo json_encode($cats);
