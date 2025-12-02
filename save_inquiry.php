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
    
    $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, phone, product, farm_size, crop_type, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$input['name'], $input['email'], $input['phone'], $input['product'], $input['farm_size'], $input['crop_type'], $input['message']]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>