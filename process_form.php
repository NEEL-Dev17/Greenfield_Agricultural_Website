<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $product = htmlspecialchars($_POST['product']);
    $farm_size = htmlspecialchars($_POST['farm_size']);
    $crop_type = htmlspecialchars($_POST['crop_type']);
    $message = htmlspecialchars($_POST['message']);
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone) || !preg_match('/^(\+91[\-\s]?)?[6789]\d{9}$/', $phone)) {
        $errors[] = "Valid Indian phone number is required";
    }
    
    if (empty($product)) {
        $errors[] = "Please select a product";
    }
    
    $recommended_tools = [];
    
    if ($farm_size > 0) {
        if ($farm_size < 5) {
            $farm_category = "Small Farm";
            $recommended_tools = ["Hand Tiller", "Wheelbarrow", "Water Sprinkler"];
        } elseif ($farm_size >= 5 && $farm_size <= 20) {
            $farm_category = "Medium Farm";
            $recommended_tools = ["Seed Planter", "Fertilizer Spreader", "Water Sprinkler"];
        } else {
            $farm_category = "Large Farm";
            $recommended_tools = ["Tractor Plough", "Seed Planter", "Fertilizer Spreader"];
        }
    }
    
    $crop_specific_tools = [];
    
    switch ($crop_type) {
        case 'wheat':
            $crop_specific_tools = ["Seed Planter", "Harvester", "Fertilizer Spreader"];
            break;
        case 'rice':
            $crop_specific_tools = ["Water Sprinkler", "Transplanter", "Harvester"];
            break;
        case 'corn':
            $crop_specific_tools = ["Seed Planter", "Cultivator", "Harvester"];
            break;
        case 'vegetables':
            $crop_specific_tools = ["Hand Tiller", "Water Sprinkler", "Wheelbarrow"];
            break;
        case 'fruits':
            $crop_specific_tools = ["Pruner", "Sprayer", "Harvester"];
            break;
        default:
            $crop_specific_tools = ["Basic Tool Set"];
    }
    
    $all_products = [
        "hand_tiller" => ["name" => "Hand Tiller", "price" => 850],
        "seed_planter" => ["name" => "Seed Planter", "price" => 1200],
        "tractor_plough" => ["name" => "Tractor Plough", "price" => 45000],
        "water_sprinkler" => ["name" => "Water Sprinkler", "price" => 600],
        "wheelbarrow" => ["name" => "Wheelbarrow", "price" => 1800],
        "fertilizer_spreader" => ["name" => "Fertilizer Spreader", "price" => 2500]
    ];
    
    $selected_product_info = $all_products[$product] ?? ["name" => "Unknown Product", "price" => 0];
    
    if (empty($errors)) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Form Submission Result</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #f0f8f0; }
                .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .success { color: #388e3c; background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; }
                .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
                .tool-list { background: #f1f8e9; padding: 15px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Thank You for Your Inquiry!</h1>
                <div class='success'>
                    <h2>Form Submitted Successfully</h2>
                    <p>We have received your inquiry and will contact you shortly.</p>
                </div>
                
                <div class='info'>
                    <h3>Your Information:</h3>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Phone:</strong> $phone</p>
                    <p><strong>Selected Product:</strong> {$selected_product_info['name']} (₹{$selected_product_info['price']})</p>
                    <p><strong>Farm Size:</strong> $farm_size acres</p>
                    <p><strong>Crop Type:</strong> $crop_type</p>
                    <p><strong>Message:</strong> $message</p>
                </div>";
        
        if (!empty($recommended_tools)) {
            echo "<div class='tool-list'>
                    <h3>Recommended Tools for Your $farm_category:</h3>
                    <ul>";
            foreach ($recommended_tools as $tool) {
                echo "<li>$tool</li>";
            }
            echo "</ul></div>";
        }
        
        if (!empty($crop_specific_tools)) {
            echo "<div class='tool-list'>
                    <h3>Recommended Tools for $crop_type Cultivation:</h3>
                    <ul>";
            foreach ($crop_specific_tools as $tool) {
                echo "<li>$tool</li>";
            }
            echo "</ul></div>";
        }
        
        $total_price = $selected_product_info['price'];
        $discount = 0;
        
        if ($farm_size > 50) {
            $discount = 0.15;
            $discount_amount = $total_price * $discount;
            $final_price = $total_price - $discount_amount;
            echo "<div class='info'>
                    <h3>Special Offer!</h3>
                    <p>You qualify for a 15% bulk discount for large farms!</p>
                    <p>Original Price: ₹$total_price</p>
                    <p>Discount: ₹$discount_amount</p>
                    <p><strong>Final Price: ₹$final_price</strong></p>
                  </div>";
        } elseif ($farm_size > 20) {
            $discount = 0.10;
            $discount_amount = $total_price * $discount;
            $final_price = $total_price - $discount_amount;
            echo "<div class='info'>
                    <h3>Special Offer!</h3>
                    <p>You qualify for a 10% discount for medium-large farms!</p>
                    <p>Original Price: ₹$total_price</p>
                    <p>Discount: ₹$discount_amount</p>
                    <p><strong>Final Price: ₹$final_price</strong></p>
                  </div>";
        }
        
        echo "<p><a href='javascript:history.back()'>Go Back</a> | <a href='index.html'>Home</a></p>
            </div>
        </body>
        </html>";
    } else {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Form Submission Error</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #ffebee; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .error { color: #e53935; background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Form Submission Error</h1>
                <div class='error'>
                    <h2>Please correct the following errors:</h2>
                    <ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>
                </div>
                <p><a href='javascript:history.back()'>Go Back and Correct Errors</a></p>
            </div>
        </body>
        </html>";
    }
} else {
    header("Location: index.html");
    exit();
}
?>