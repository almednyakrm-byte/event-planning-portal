<?php
// Import database connection
require_once 'db.php';

// Initialize database connection
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);

// Function to validate user role
function validateUserRole($role) {
    // For this example, assume we have a session variable 'user_role'
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
}

// Function to validate user login
function validateUserLogin() {
    // For this example, assume we have a session variable 'logged_in'
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    validateUserLogin();
    $stmt = $pdo->prepare('SELECT * FROM venues');
    $stmt->execute();
    $venues = $stmt->fetchAll();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($venues);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateUserLogin();
    validateUserRole('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    // Validate input data
    if (!isset($data['name']) || !isset($data['address'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }
    $stmt = $pdo->prepare('INSERT INTO venues (name, address) VALUES (:name, :address)');
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':address', $data['address']);
    $stmt->execute();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Venue created successfully']);
}

// Handle PUT request
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    validateUserLogin();
    validateUserRole('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    // Validate input data
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['address'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }
    $stmt = $pdo->prepare('UPDATE venues SET name = :name, address = :address WHERE id = :id');
    $stmt->bindParam(':id', $data['id']);
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':address', $data['address']);
    $stmt->execute();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Venue updated successfully']);
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    validateUserLogin();
    validateUserRole('admin');
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    // Validate input data
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }
    $stmt = $pdo->prepare('DELETE FROM venues WHERE id = :id');
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Venue deleted successfully']);
}