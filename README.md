# GreenField Agricultural Tools - Website Documentation

## Project Overview
A comprehensive agricultural tools e-commerce website with modern web development features including PHP backend, MySQL database integration, JavaScript functionalities, and interactive components.

## Features Implemented

### 1. **Frontend Components**
- **Responsive Design**: Mobile-friendly layout with CSS media queries
- **Interactive Navigation**: Smooth scrolling navigation menu
- **Product Catalog**: Table displaying agricultural tools with prices
- **Image Gallery**: Responsive image display for services and benefits
- **Embedded Media**: YouTube videos for product demonstrations

### 2. **JavaScript Functionalities**
- **Shopping Cart System**: Add/remove items, quantity control, cart total calculation
- **Interactive Calculator**: Full-featured calculator with keyboard support
- **Popup Demonstrations**: Alert, confirm, and prompt boxes
- **Form Validation**: Phone number validation with real-time feedback
- **AJAX Product Loading**: Dynamic content loading without page refresh

### 3. **Backend Integration (PHP/MySQL)**
- **Contact Form Processing**: Saves inquiries to database
- **Product Management**: Add/view products in database
- **Order Processing**: Save customer orders to database
- **Customer Inquiry System**: View and manage customer inquiries

### 4. **Interactive Plugins**
- **Weather Widget**: Simulated farm weather forecast
- **File Upload**: Document upload functionality with validation
- **Equipment Status Tracker**: Simulated farm equipment monitoring
- **Farm Management System**: Link to comprehensive farm management portal

### 5. **Database Features**
- **MySQL Integration**: Connected database for storing:
  - Customer inquiries
  - Product information
  - Order details
  - User data
- **PHP Sessions**: User session management
- **Data Retrieval**: Dynamic loading of database content

## File Structure
```
project/
│
├── index.html # Main website file
├── farm_management.php # Farm management system
├── save_inquiry.php # PHP script to save inquiries
├── save_product.php # PHP script to save products
├── save_order.php # PHP script to save orders
├── get_inquiries.php # PHP script to fetch inquiries
│
├── assets/
│ ├── images/
│ │ ├── Agricultural_machinery.jpg
│ │ ├── pic1.jpeg
│ │ ├── pic2.jpeg
│ │ ├── pic3.jpeg
│ │ └── pic4.jpeg
│ └── css/
│ └── styles.css # External styles (optional)
│
└── database/
├── database.sql # Database schema
└── connection.php # Database connection file
```
## Prerequisites

### Software Requirements
- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Browser**: Chrome, Firefox, Safari (modern browsers)

### PHP Extensions Required
- mysqli
- json
- session
- fileinfo (for file uploads)

## Installation Steps

### 1. **Database Setup**
```sql
CREATE DATABASE greenfield_agricultural;
USE greenfield_agricultural;

-- Create inquiries table
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    product VARCHAR(100),
    farm_size INT,
    crop_type VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'pending'
);

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_name VARCHAR(100),
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```
## 2. **Configuration**

Create `config.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'greenfield_agricultural');
?>
```
## 3. **PHP Scripts Setup**

### `save_inquiry.php`
```php
<?php
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, product, farm_size, crop_type, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiss", 
    $data['name'], 
    $data['email'], 
    $data['phone'], 
    $data['product'], 
    $data['farm_size'], 
    $data['crop_type'], 
    $data['message']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
```
### Similar scripts needed for:
- `save_product.php`
- `save_order.php`
- `get_inquiries.php`
- `farm_management.php`


## Features Usage Guide

### 1. **Shopping Cart**
- Click "Add to Cart" on any product
- Adjust quantities using +/- buttons
- View cart summary and proceed to checkout
- Database integration for order saving

### 2. **Calculator**
- Perform basic arithmetic operations
- Keyboard input supported
- Error handling for division by zero

### 3. **Contact Form**
- Validates Indian phone numbers in real-time
- Stores submissions in MySQL database
- Requires all mandatory fields

### 4. **Popup Demonstrations**
- Alert, Confirm, and Prompt boxes
- Product information display
- Store details showcase

### 5. **Database Operations**
- Add products via form (requires admin)
- View customer inquiries
- Process and save orders

## Testing Procedures

### Functional Testing
1. **Form Validation**
   - Test invalid phone numbers
   - Test required field validation
   - Test email format validation

2. **Shopping Cart**
   - Add multiple items
   - Change quantities
   - Remove items
   - Checkout process

3. **Calculator**
   - Basic operations (+, -, ×, ÷)
   - Decimal calculations
   - Error handling

4. **Database Operations**
   - Submit contact form
   - Add products via admin form
   - View stored inquiries

### Browser Compatibility
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Security Considerations

### Implemented
- **Input Validation**: Client-side validation for forms
- **SQL Injection Prevention**: Prepared statements in PHP
- **XSS Prevention**: Output escaping in PHP scripts
- **File Upload Restrictions**: Size and type validation

### Recommended Enhancements
1. **User Authentication**: Login system for admin access
2. **Password Hashing**: For user credentials
3. **CSRF Protection**: Tokens for forms
4. **HTTPS Enforcement**: For production deployment

## Performance Optimization

### Done
- **Image Optimization**: Properly sized images
- **CSS Minification**: Inline CSS for faster loading
- **Lazy Loading**: For images and videos
- **Responsive Design**: Mobile-first approach

### Recommended
1. **Caching**: Implement browser caching
2. **CDN**: Use CDN for static assets
3. **Database Indexing**: Index frequently queried columns
4. **GZIP Compression**: Enable on server

## Deployment Instructions

### Local Development
1. Install XAMPP/WAMP/MAMP
2. Place files in `htdocs` or `www` directory
3. Import database schema
4. Update database credentials in `config.php`
5. Access via `http://localhost/project`

### Production Deployment
1. Choose hosting with PHP/MySQL support
2. Upload all files to server
3. Create database and import schema
4. Update `config.php` with production credentials
5. Set proper file permissions (755 for directories, 644 for files)
6. Enable SSL certificate

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials
   - Verify MySQL service is running
   - Ensure database exists

2. **Form Not Submitting**
   - Check JavaScript console for errors
   - Verify PHP file paths are correct
   - Check file permissions

3. **Images Not Loading**
   - Verify image file paths
   - Check file permissions
   - Ensure correct file extensions

4. **PHP Scripts Returning Errors**
   - Check PHP error logs
   - Verify PHP version compatibility
   - Check for syntax errors

## Future Enhancements

### Planned Features
1. **User Registration/Login System**
2. **Payment Gateway Integration**
3. **Product Reviews and Ratings**
4. **Order Tracking System**
5. **Admin Dashboard**
6. **Email Notification System**
7. **Multi-language Support**
8. **Advanced Search and Filtering**
9. **Wishlist Feature**
10. **Social Media Integration**

## Contact Information

**Developer**: Neelabja Nag  
**Student ID**: 24BCE10255  
**Course**: Internet and Web Programming  
**Class**: BL2025260400070 - B11+B12+B13+E11+E12+E14  
**Institution**: VIT Bhopal University

## License
This project is for educational purposes as part of the Internet and Web Programming course requirements.

## Acknowledgments
- Professor Ankur Jain for guidance
- VIT Bhopal University for resources
- All contributors and testers

---

**Last Updated**: December 2024  
**Version**: 1.0  
**Status**: Active Development
