<?php
require "db.php";
require "session_check.php";

$input = json_decode(file_get_contents("php://input"), true);

$username = trim($input["username"] ?? "");
$password = trim($input["password"] ?? "");
$role = trim($input["role"] ?? "Staff");

// Validate fields
if ($username === "" || $password === "") {
    echo json_encode(["success" => false, "error" => "Username and password required"]);
    exit;
}

// Prevent duplicate usernames
$check = $conn->prepare("SELECT user_id FROM user WHERE username = ? LIMIT 1");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Username already exists"]);
    exit;
}
$check->close();

// Hash password
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Insert user
$stmt = $conn->prepare("
    INSERT INTO user (username, password, role)
    VALUES (?, ?, ?)
");
$stmt->bind_param("sss", $username, $hashed, $role);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "user_id" => $stmt->insert_id]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
