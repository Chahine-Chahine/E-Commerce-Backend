<?php
header('Access-Control-Allow-Origin:*');
include("../../connection.php");

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$description = $_POST['description'];
$price = $_POST['price'];
$seller_id = $_POST['seller_id'];

$query = $mysqli->prepare('
    UPDATE products
    SET
        product_name = ?,
        description = ?,
        price = ?,
        seller_id = ?
    WHERE
        product_id = ?
');

$query->bind_param('ssdsi', $product_name, $description, $price, $seller_id, $product_id);
$query->execute();

$response = [];

if ($mysqli->affected_rows > 0) {
    $response['status'] = 'Success';
    $response['message'] = 'Product updated successfully';
} else {
    $response['status'] = 'Error';
    $response['message'] = 'Product not found or no changes made';
}

echo json_encode($response);

// Close the statement
$query->close();
// Close the database connection
$mysqli->close();
?>
