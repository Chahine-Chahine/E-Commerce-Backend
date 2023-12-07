<?php
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
include("../../connection.php");
require '../../../vendor/autoload.php';

use \Firebase\JWT\JWT;

$email = $_POST['email'];
$password = $_POST['password'];

$query = $mysqli->prepare('SELECT user_id, role, password FROM users WHERE email = ?');
$query->bind_param('s', $email);

$response = [];

if ($query->execute()) {
    $query->store_result();

    if ($query->num_rows == 0) {
        $response['status'] = 'user not found';
    } else {
        $query->bind_result($user_id, $role, $hashed_password);
        $query->fetch();

        if (password_verify($password, $hashed_password)) {
            $key = "your_secret";
            $payload_array = [];
            $payload_array["user_id"] = $user_id;
            $payload_array["usertype"] = $role;
            $payload_array["exp"] = time() + 3600;
            $payload = $payload_array;
            $response['status'] = 'logged in';
            $response['jwt'] = JWT::encode($payload, $key, 'HS256');
            $response['role'] = $role;
            $response['user_id'] = $user_id;
            $response['email'] = $email;
        } else {
            $response['status'] = 'wrong credentials';
        }
    }
} else {
    $response['status'] = 'false';
    $response['error'] = $mysqli->error;
}

echo json_encode($response);

// Close the statement
$query->close();
// Close the database connection
$mysqli->close();
?>
