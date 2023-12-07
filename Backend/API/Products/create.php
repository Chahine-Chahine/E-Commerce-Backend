<?php

header('Access-Control-Allow-Origin:*');
include("../../connection.php");
require  '../../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

$headers = getallheaders();
if (!isset($headers['Authorization']) || empty($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}

$authorizationHeader = $headers['Authorization'];
$token = null;

$token = trim(str_replace("Bearer", '', $authorizationHeader));
print_r($authorizationHeader);
if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}

try {
    $key = "your_secret"; 
    $decoded = JWT::decode($token, new Key($key, 'HS256'));

    if ($decoded->usertype === "seller") {
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
    } else {
        $response = [];
        $response["permissions"] = false;
        echo json_encode($response);
    }
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(["error" => "expired"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}
?>
