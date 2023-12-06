<?php

header('Access-Control-Allow-Origin:*');
include("../../connection.php");

$product_name = $_POST['product_name'];
$description = $_POST['description'];
$price = $_POST['price'];
$seller_id = $_POST['seller_id'];

$insertProductQuery = $mysqli->prepare('INSERT INTO products (product_name, description, price, seller_id) VALUES (?, ?, ?, ?)');
$insertProductQuery->bind_param('ssdi', $product_name, $description, $price, $seller_id);
$insertProductQuery->execute();

// Check if the insertion was successful
if ($insertProductQuery->affected_rows > 0) {
    $response['status'] = 'Success';
    $response['message'] = 'Product added successfully';
} else {
    $response['status'] = 'Error';
    $response['message'] = 'Error adding product: ' . $mysqli->error;
}

echo json_encode($response);

$insertProductQuery->close();
$mysqli->close();
?>
