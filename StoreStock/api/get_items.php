<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "
    SELECT 
        i.item_id,
        i.item_name,
        i.quantity,
        i.reorder_level,
        i.unit_price,
        COALESCE(c.category_name, '') AS category
    FROM item i
    LEFT JOIN category c ON i.category_id = c.category_id
    ORDER BY i.item_id DESC
";

$result = $conn->query($sql);

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
?>