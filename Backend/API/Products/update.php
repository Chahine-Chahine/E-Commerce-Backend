<?php
header('Access-Control-Allow-Origin: *');
include("../../connection.php");
require '../../../vendor/autoload.php';

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

if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}

try {
    $key = "your_secret"; 
    $decoded = JWT::decode($token, new Key($key, 'HS256'));

    if ($decoded->usertype === "seller" && isset($decoded->user_id)) {
        $seller_id = $decoded->user_id;

        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        $query = $mysqli->prepare('
            UPDATE products
            SET
                product_name = ?,
                description = ?,
                price = ?
            WHERE
                product_id = ? AND seller_id = ?
        ');

        $query->bind_param('ssiii', $product_name, $description, $price, $product_id, $seller_id);
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
        
        $query->close();
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
