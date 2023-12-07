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
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$authorizationHeader = $headers['Authorization'];
$token = null;

$token = trim(str_replace("Bearer", '', $authorizationHeader));

if (!$token) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

try {
    $key = "your_secret"; 
    $decoded = JWT::decode($token, new Key($key, 'HS256'));

    if ($decoded->usertype === "seller" && $decoded->user_id) {
        $seller_id = $decoded->user_id;

        $product_id = $_POST['product_id'];

        $query = $mysqli->prepare('DELETE FROM products WHERE product_id = ? and seller_id = ?');
        $query->bind_param('ii', $product_id, $seller_id);
        $query->execute();

        $response = [];

        if ($mysqli->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Product deleted successfully';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Product not found or deletion failed';
        }

        echo json_encode($response);
    } else {
        $response = ["status" => "error", "message" => "Insufficient permissions"];
        echo json_encode($response);
    }
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Token expired"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid token"]);
} finally {
    $query->close();
    $mysqli->close();
}
?>
