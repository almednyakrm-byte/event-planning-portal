<?php
// Import database connection
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Initialize database connection
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle GET requests
if ($method == 'GET') {
    // Validate and sanitize input
    $supplier_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if supplier ID is provided
    if ($supplier_id) {
        // SQL query to retrieve supplier by ID
        $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE id = :id');
        $stmt->bindParam(':id', $supplier_id);
        $stmt->execute();

        // Fetch supplier data
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if supplier exists
        if ($supplier) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($supplier);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Supplier not found']);
        }
    } else {
        // SQL query to retrieve all suppliers
        $stmt = $pdo->prepare('SELECT * FROM suppliers');
        $stmt->execute();

        // Fetch all suppliers
        $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($suppliers);
    }
}

// Handle POST requests
if ($method == 'POST') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden access']);
        exit;
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($input['phone'], FILTER_SANITIZE_NUMBER_INT);
    $address = filter_var($input['address'], FILTER_SANITIZE_STRING);

    // Check if input data is valid
    if (!$name || !$email || !$phone || !$address) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // SQL query to insert new supplier
    $stmt = $pdo->prepare('INSERT INTO suppliers (name, email, phone, address) VALUES (:name, :email, :phone, :address)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->execute();

    // Get inserted supplier ID
    $supplier_id = $pdo->lastInsertId();

    // SQL query to retrieve inserted supplier
    $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE id = :id');
    $stmt->bindParam(':id', $supplier_id);
    $stmt->execute();

    // Fetch inserted supplier data
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($supplier);
}

// Handle PUT requests
if ($method == 'PUT') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden access']);
        exit;
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $supplier_id = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($input['phone'], FILTER_SANITIZE_NUMBER_INT);
    $address = filter_var($input['address'], FILTER_SANITIZE_STRING);

    // Check if input data is valid
    if (!$supplier_id || !$name || !$email || !$phone || !$address) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // SQL query to update supplier
    $stmt = $pdo->prepare('UPDATE suppliers SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id');
    $stmt->bindParam(':id', $supplier_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->execute();

    // SQL query to retrieve updated supplier
    $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE id = :id');
    $stmt->bindParam(':id', $supplier_id);
    $stmt->execute();

    // Fetch updated supplier data
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($supplier);
}

// Handle DELETE requests
if ($method == 'DELETE') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden access']);
        exit;
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $supplier_id = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if input data is valid
    if (!$supplier_id) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // SQL query to delete supplier
    $stmt = $pdo->prepare('DELETE FROM suppliers WHERE id = :id');
    $stmt->bindParam(':id', $supplier_id);
    $stmt->execute();

    http_response_code(204);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Supplier deleted successfully']);
}