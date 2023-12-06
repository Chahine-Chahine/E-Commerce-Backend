<?php
header('Access-Control-Allow-Origin:*');
include("../../connection.php");

$product_id = $_POST['product_id'];

$query = $mysqli->prepare('DELETE FROM products WHERE product_id = ?');
$query->bind_param('i', $product_id);
$query->execute();

$response = [];

if ($mysqli->affected_rows > 0) {
    $response['status'] = 'Success';
    $response['message'] = 'Product deleted successfully';
} else {
    $response['status'] = 'Error';
    $response['message'] = 'Product not found or deletion failed';
}

echo json_encode($response);

$query->close();
$mysqli->close();
?>
