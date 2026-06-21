<?php
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'You must be logged in to access this resource']);
    exit;
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Initialize the database connection
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle GET requests
if ($method == 'GET') {
    // Validate and sanitize the input
    $eventId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the event ID is provided
    if ($eventId) {
        // SQL query to select a single event
        $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->bindParam(':id', $eventId);
        $stmt->execute();
        $event = $stmt->fetch();

        // Check if the event exists
        if ($event) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($event);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Event not found']);
        }
    } else {
        // SQL query to select all events
        $stmt = $pdo->prepare('SELECT * FROM events');
        $stmt->execute();
        $events = $stmt->fetchAll();

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($events);
    }
}

// Handle POST requests
if ($method == 'POST') {
    // Check if the user is an admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Only admins can create events']);
        exit;
    }

    // Get the input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize the input
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $date = filter_var($data['date'], FILTER_SANITIZE_STRING);

    // Check if the input is valid
    if (!$name || !$description || !$date) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // SQL query to insert a new event
    $stmt = $pdo->prepare('INSERT INTO events (name, description, date) VALUES (:name, :description, :date)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Event created successfully']);
}

// Handle PUT requests
if ($method == 'PUT') {
    // Check if the user is an admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Only admins can update events']);
        exit;
    }

    // Get the input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize the input
    $eventId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $date = filter_var($data['date'], FILTER_SANITIZE_STRING);

    // Check if the input is valid
    if (!$eventId || !$name || !$description || !$date) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // SQL query to update an event
    $stmt = $pdo->prepare('UPDATE events SET name = :name, description = :description, date = :date WHERE id = :id');
    $stmt->bindParam(':id', $eventId);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Event updated successfully']);
}

// Handle DELETE requests
if ($method == 'DELETE') {
    // Check if the user is an admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Only admins can delete events']);
        exit;
    }

    // Get the input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize the input
    $eventId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the input is valid
    if (!$eventId) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // SQL query to delete an event
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
    $stmt->bindParam(':id', $eventId);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Event deleted successfully']);
}