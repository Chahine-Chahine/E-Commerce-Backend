<?php
header('Access-Control-Allow-Origin:*');
include("../../connection.php");

$product_id = $_POST['product_id'];

$query = $mysqli->prepare('
    SELECT
        users.user_id,
        users.username,
        users.password,
        users.role,
        users.email,
        products.product_id AS product_id,
        products.product_name AS product_name,
        products.description AS product_description,
        products.price AS product_price
    FROM
        users
    INNER JOIN
        products ON users.user_id = products.seller_id
    WHERE
        products.product_id = ?
');

// Check if the query preparation was successful
if (!$query) {
    $response['status'] = 'Error';
    $response['message'] = 'Query preparation failed: ' . $mysqli->error;
    echo json_encode($response);
    exit;
}

$query->bind_param('i', $product_id);
$query->execute();
$query->store_result();

// Bind the results to variables
$query->bind_result(
    $user_id,
    $username,
    $password,
    $role,
    $email,
    $product_id,
    $product_name,
    $product_description,
    $product_price
);

$response = [];

if ($query->num_rows > 0) {
    // Fetch the results
    $query->fetch();

    $response['status'] = 'Success';
    $response['user_id'] = $user_id;
    $response['username'] = $username;
    $response['password'] = $password;
    $response['role'] = $role;
    $response['email'] = $email;
    $response['product_id'] = $product_id;
    $response['product_name'] = $product_name;
    $response['product_description'] = $product_description;
    $response['product_price'] = $product_price;
} else {
    $response['status'] = 'Error';
    $response['message'] = 'Product not found';
}

echo json_encode($response);

// Close the statement
$query->close();
// Close the database connection
$mysqli->close();
?>
