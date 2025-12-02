<?php
session_start();

if (!isset($_SESSION['farm_data'])) {
    die("No farm data found.");
}

$export_data = [
    'export_time' => date('Y-m-d H:i:s'),
    'session_id' => session_id(),
    'farm_statistics' => [
        'total_farms' => count($_SESSION['farm_data']['farms']),
        'total_employees' => count($_SESSION['farm_data']['employees']),
        'total_crop_cycles' => count($_SESSION['farm_data']['crop_cycles']),
        'total_yield' => array_sum(array_column($_SESSION['farm_data']['crop_cycles'], 'yield'))
    ],
    'farm_data' => $_SESSION['farm_data']
];

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="farm_management_export.json"');

echo json_encode($export_data, JSON_PRETTY_PRINT);
exit;