<?php
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
include("../../connection.php");

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = $mysqli->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
$query->bind_param('ssss', $username, $email, $hashed_password, $role);

$response = [];

if ($query->execute()) {
    $response["status"] = "true";
} else {
    $response["status"] = "false";
    $response["error"] = $mysqli->error;
}

echo json_encode($response);

// Close the statement
$query->close();
// Close the database connection
$mysqli->close();
?>
