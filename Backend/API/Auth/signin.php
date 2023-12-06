<?php
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
include("../../connection.php");

$email = $_POST['email'];
$password = $_POST['password'];

$query = $mysqli->prepare('SELECT user_id, role, password FROM users WHERE email = ?');
$query->bind_param('s', $email);

$response = [];

if ($query->execute()) {
    $query->store_result();

    if ($query->num_rows == 0) {
        $response['status'] = 'user not found';
        echo json_encode($response);
    } else {
        $query->bind_result($user_id, $role, $hashed_password);
        $query->fetch();

        if (password_verify($password, $hashed_password)) {
            $response['status'] = 'logged in';
            $response['role'] = $role;
            $response['user_id'] = $user_id;
            $response['email'] = $email;
        } else {
            $response['status'] = 'wrong credentials';
        }

        echo json_encode($response);
    }
} else {
    $response['status'] = 'false';
    $response['error'] = $mysqli->error;
    echo json_encode($response);
}

// Close the statement
$query->close();
// Close the database connection
$mysqli->close();
?>
