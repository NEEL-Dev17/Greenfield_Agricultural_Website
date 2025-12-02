<?php
session_start();

if (!isset($_SESSION['farm_data'])) {
    $_SESSION['farm_data'] = [
        'farms' => [],
        'inventory' => [],
        'employees' => [],
        'sales_records' => [],
        'crop_cycles' => []
    ];
}

$farm_data = &$_SESSION['farm_data'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_farm':
            $farm_name = trim($_POST['farm_name']);
            $farm_size = floatval($_POST['farm_size']);
            $farm_location = trim($_POST['farm_location']);
            $soil_type = trim($_POST['soil_type']);
            
            if (!empty($farm_name)) {
                $farm_id = 'farm_' . uniqid();
                $farm_data['farms'][$farm_id] = [
                    'name' => $farm_name,
                    'size' => $farm_size,
                    'location' => $farm_location,
                    'soil_type' => $soil_type,
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 'active'
                ];
                $message = "Farm '$farm_name' added successfully!";
            }
            break;
            
        case 'add_crop_cycle':
            $farm_id = $_POST['farm_id'];
            $crop_name = trim($_POST['crop_name']);
            $planting_date = $_POST['planting_date'];
            $expected_harvest = $_POST['expected_harvest'];
            $area_used = floatval($_POST['area_used']);
            
            if (!empty($crop_name) && isset($farm_data['farms'][$farm_id])) {
                $cycle_id = 'cycle_' . uniqid();
                $farm_data['crop_cycles'][$cycle_id] = [
                    'farm_id' => $farm_id,
                    'crop_name' => $crop_name,
                    'planting_date' => $planting_date,
                    'expected_harvest' => $expected_harvest,
                    'area_used' => $area_used,
                    'status' => 'growing',
                    'yield' => 0
                ];
                $message = "Crop cycle for '$crop_name' started!";
            }
            break;
            
        case 'record_harvest':
            $cycle_id = $_POST['cycle_id'];
            $yield_amount = floatval($_POST['yield_amount']);
            $harvest_date = $_POST['harvest_date'];
            
            if (isset($farm_data['crop_cycles'][$cycle_id])) {
                $farm_data['crop_cycles'][$cycle_id]['yield'] = $yield_amount;
                $farm_data['crop_cycles'][$cycle_id]['harvest_date'] = $harvest_date;
                $farm_data['crop_cycles'][$cycle_id]['status'] = 'harvested';
                
                $crop_name = $farm_data['crop_cycles'][$cycle_id]['crop_name'];
                $message = "Harvest recorded for $crop_name: $yield_amount kg";
            }
            break;
            
        case 'add_employee':
            $emp_name = trim($_POST['emp_name']);
            $emp_role = trim($_POST['emp_role']);
            $emp_salary = floatval($_POST['emp_salary']);
            $assigned_farm = $_POST['assigned_farm'];
            
            if (!empty($emp_name)) {
                $emp_id = 'emp_' . uniqid();
                $farm_data['employees'][$emp_id] = [
                    'name' => $emp_name,
                    'role' => $emp_role,
                    'salary' => $emp_salary,
                    'assigned_farm' => $assigned_farm,
                    'hire_date' => date('Y-m-d')
                ];
                $message = "Employee '$emp_name' added successfully!";
            }
            break;
            
        case 'clear_data':
            $_SESSION['farm_data'] = [
                'farms' => [],
                'inventory' => [],
                'employees' => [],
                'sales_records' => [],
                'crop_cycles' => []
            ];
            $message = "All farm data cleared!";
            break;
    }
}

$farms = $farm_data['farms'];
$employees = $farm_data['employees'];
$crop_cycles = $farm_data['crop_cycles'];

$total_farms = count($farms);
$total_employees = count($employees);
$active_crops = array_filter($crop_cycles, function($cycle) {
    return $cycle['status'] === 'growing';
});
$harvested_crops = array_filter($crop_cycles, function($cycle) {
    return $cycle['status'] === 'harvested';
});

$farm_names = array_column($farms, 'name');
$employee_names = array_column($employees, 'name');
$crop_names = array_column($crop_cycles, 'crop_name');

$total_yield = array_sum(array_column($crop_cycles, 'yield'));
$total_salary = array_sum(array_column($employees, 'salary'));

$crop_yields = [];
foreach ($crop_cycles as $cycle) {
    $crop = $cycle['crop_name'];
    if (!isset($crop_yields[$crop])) {
        $crop_yields[$crop] = 0;
    }
    $crop_yields[$crop] += $cycle['yield'];
}

arsort($crop_yields);

$farm_utilization = [];
foreach ($farms as $farm_id => $farm) {
    $farm_crops = array_filter($crop_cycles, function($cycle) use ($farm_id) {
        return $cycle['farm_id'] === $farm_id;
    });
    $utilized_area = array_sum(array_column($farm_crops, 'area_used'));
    $utilization_rate = ($utilized_area / $farm['size']) * 100;
    $farm_utilization[$farm['name']] = round($utilization_rate, 2);
}

$employee_roles = array_count_values(array_column($employees, 'role'));
$soil_types = array_count_values(array_column($farms, 'soil_type'));

$recent_farms = array_slice($farms, -3, 3, true);
$recent_employees = array_slice($employees, -2, 2, true);
$recent_crops = array_slice($crop_cycles, -4, 4, true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Farm Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f8f0; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, button, textarea { padding: 8px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        button { background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #387c35; padding: 8px; text-align: left; }
        th { background-color: #e8f5e8; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0; }
        .stat-card { background: #e8f5e8; padding: 15px; border-radius: 8px; text-align: center; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .warning { background: #fff3cd; color: #856404; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚜 Farm Management System</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>🏞️ Total Farms</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $total_farms; ?></p>
            </div>
            <div class="stat-card">
                <h3>👥 Employees</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $total_employees; ?></p>
            </div>
            <div class="stat-card">
                <h3>🌱 Active Crops</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo count($active_crops); ?></p>
            </div>
            <div class="stat-card">
                <h3>📊 Total Yield</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo number_format($total_yield, 2); ?> kg</p>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h2>🏞️ Add New Farm</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_farm">
                    <div class="form-group">
                        <label>Farm Name:</label>
                        <input type="text" name="farm_name" required>
                    </div>
                    <div class="form-group">
                        <label>Farm Size (acres):</label>
                        <input type="number" name="farm_size" step="0.1" required min="0.1">
                    </div>
                    <div class="form-group">
                        <label>Location:</label>
                        <input type="text" name="farm_location" required>
                    </div>
                    <div class="form-group">
                        <label>Soil Type:</label>
                        <select name="soil_type" required>
                            <option value="clay">Clay</option>
                            <option value="sandy">Sandy</option>
                            <option value="loamy">Loamy</option>
                            <option value="silt">Silt</option>
                            <option value="peaty">Peaty</option>
                        </select>
                    </div>
                    <button type="submit">Add Farm</button>
                </form>
            </div>

            <div class="card">
                <h2>🌱 Start Crop Cycle</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_crop_cycle">
                    <div class="form-group">
                        <label>Select Farm:</label>
                        <select name="farm_id" required>
                            <?php foreach ($farms as $id => $farm): ?>
                                <option value="<?php echo $id; ?>"><?php echo $farm['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Crop Name:</label>
                        <input type="text" name="crop_name" required>
                    </div>
                    <div class="form-group">
                        <label>Planting Date:</label>
                        <input type="date" name="planting_date" required>
                    </div>
                    <div class="form-group">
                        <label>Expected Harvest:</label>
                        <input type="date" name="expected_harvest" required>
                    </div>
                    <div class="form-group">
                        <label>Area Used (acres):</label>
                        <input type="number" name="area_used" step="0.1" required min="0.1">
                    </div>
                    <button type="submit">Start Crop Cycle</button>
                </form>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h2>👥 Add Employee</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_employee">
                    <div class="form-group">
                        <label>Employee Name:</label>
                        <input type="text" name="emp_name" required>
                    </div>
                    <div class="form-group">
                        <label>Role:</label>
                        <select name="emp_role" required>
                            <option value="farmer">Farmer</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="harvester">Harvester</option>
                            <option value="irrigation_specialist">Irrigation Specialist</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Salary (₹):</label>
                        <input type="number" name="emp_salary" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Assigned Farm:</label>
                        <select name="assigned_farm" required>
                            <?php foreach ($farms as $id => $farm): ?>
                                <option value="<?php echo $id; ?>"><?php echo $farm['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">Add Employee</button>
                </form>
            </div>

            <div class="card">
                <h2>📦 Record Harvest</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="record_harvest">
                    <div class="form-group">
                        <label>Select Crop Cycle:</label>
                        <select name="cycle_id" required>
                            <?php foreach ($crop_cycles as $id => $cycle): ?>
                                <?php if ($cycle['status'] === 'growing'): ?>
                                    <option value="<?php echo $id; ?>">
                                        <?php echo $cycle['crop_name']; ?> - <?php echo $farms[$cycle['farm_id']]['name']; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Yield Amount (kg):</label>
                        <input type="number" name="yield_amount" step="0.1" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Harvest Date:</label>
                        <input type="date" name="harvest_date" required>
                    </div>
                    <button type="submit">Record Harvest</button>
                </form>
            </div>
        </div>

        <div class="card">
            <h2>📊 Farm Analytics</h2>
            
            <div class="grid">
                <div>
                    <h3>🏆 Top Performing Crops</h3>
                    <table>
                        <tr><th>Crop</th><th>Total Yield (kg)</th></tr>
                        <?php foreach ($crop_yields as $crop => $yield): ?>
                            <tr><td><?php echo ucfirst($crop); ?></td><td><?php echo number_format($yield, 2); ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                
                <div>
                    <h3>📈 Farm Utilization</h3>
                    <table>
                        <tr><th>Farm</th><th>Utilization %</th></tr>
                        <?php foreach ($farm_utilization as $farm => $utilization): ?>
                            <tr><td><?php echo $farm; ?></td><td><?php echo $utilization; ?>%</td></tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            
            <div class="grid">
                <div>
                    <h3>👥 Employee Roles</h3>
                    <table>
                        <tr><th>Role</th><th>Count</th></tr>
                        <?php foreach ($employee_roles as $role => $count): ?>
                            <tr><td><?php echo ucfirst(str_replace('_', ' ', $role)); ?></td><td><?php echo $count; ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                
                <div>
                    <h3>🌱 Soil Types</h3>
                    <table>
                        <tr><th>Soil Type</th><th>Count</th></tr>
                        <?php foreach ($soil_types as $soil => $count): ?>
                            <tr><td><?php echo ucfirst($soil); ?></td><td><?php echo $count; ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h2>🏞️ Farm List</h2>
                <?php if (empty($farms)): ?>
                    <p>No farms added yet.</p>
                <?php else: ?>
                    <table>
                        <tr><th>Name</th><th>Size</th><th>Location</th><th>Soil Type</th></tr>
                        <?php foreach ($farms as $farm): ?>
                            <tr>
                                <td><?php echo $farm['name']; ?></td>
                                <td><?php echo $farm['size']; ?> acres</td>
                                <td><?php echo $farm['location']; ?></td>
                                <td><?php echo ucfirst($farm['soil_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>🌱 Crop Cycles</h2>
                <?php if (empty($crop_cycles)): ?>
                    <p>No crop cycles started yet.</p>
                <?php else: ?>
                    <table>
                        <tr><th>Crop</th><th>Farm</th><th>Status</th><th>Yield</th></tr>
                        <?php foreach ($crop_cycles as $cycle): ?>
                            <tr>
                                <td><?php echo $cycle['crop_name']; ?></td>
                                <td><?php echo $farms[$cycle['farm_id']]['name']; ?></td>
                                <td><?php echo ucfirst($cycle['status']); ?></td>
                                <td><?php echo $cycle['yield']; ?> kg</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h2>💾 Session Management</h2>
            <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
            <p><strong>Total Data Entries:</strong> <?php echo $total_farms + $total_employees + count($crop_cycles); ?></p>
            <p><strong>Session Size:</strong> <?php echo strlen(serialize($_SESSION['farm_data'])); ?> bytes</p>
            
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="clear_data">
                <button type="submit" onclick="return confirm('Clear all farm data?')" 
                        style="background: #dc3545; width: auto;">Clear All Data</button>
            </form>
            
            <a href="farm_export.php" style="background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-left: 10px;">Export Data</a>
        </div>
    </div>
</body>
</html>