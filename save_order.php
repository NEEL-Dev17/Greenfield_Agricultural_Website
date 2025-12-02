<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = '127.0.0.1:4306';
$dbname = 'agricultural_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, products, total_amount) VALUES (?, ?, ?, ?)");
    $products_json = json_encode($input['products']);
    $stmt->execute([$input['customer_name'], $input['customer_email'], $products_json, $input['total_amount']]);
    
    $order_id = $pdo->lastInsertId();
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>