<?php
// Start the session to handle user authentication
session_start();

// Import the database connection
require_once 'db.php';

// Check if the request method is GET or POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check the current session status
    if (isset($_SESSION['user_id'])) {
        // User is logged in, return the user ID
        echo json_encode(['status' => 'logged_in', 'user_id' => $_SESSION['user_id']]);
    } else {
        // User is not logged in, return a not logged in status
        echo json_encode(['status' => 'not_logged_in']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST requests for login and registration
    if (isset($_POST['action'])) {
        // Check the action type
        if ($_POST['action'] === 'login') {
            // Login action
            // Check if the username and password fields are set
            if (isset($_POST['username']) && isset($_POST['password'])) {
                // Securely check the input fields
                $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
                $password = $_POST['password'];

                // Prepare a statement to select the user data
                $stmt = $conn->prepare('SELECT user_id, password FROM users WHERE username = ?');
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if the user exists
                if ($result->num_rows === 1) {
                    // Get the user data
                    $userData = $result->fetch_assoc();

                    // Verify the password using password_verify()
                    if (password_verify($password, $userData['password'])) {
                        // Password is correct, start a new session
                        $_SESSION['user_id'] = $userData['user_id'];
                        echo json_encode(['status' => 'login_success']);
                    } else {
                        // Password is incorrect, return an error
                        echo json_encode(['status' => 'login_error', 'message' => 'Invalid password']);
                    }
                } else {
                    // User does not exist, return an error
                    echo json_encode(['status' => 'login_error', 'message' => 'User not found']);
                }
            } else {
                // Input fields are not set, return an error
                echo json_encode(['status' => 'login_error', 'message' => 'Please fill in all fields']);
            }
        } elseif ($_POST['action'] === 'register') {
            // Register action
            // Check if the username, email, and password fields are set
            if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
                // Securely check the input fields
                $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];

                // Check if the username and email are valid
                if (strlen($username) < 3 || strlen($username) > 32) {
                    echo json_encode(['status' => 'register_error', 'message' => 'Username must be between 3 and 32 characters']);
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['status' => 'register_error', 'message' => 'Invalid email address']);
                } else {
                    // Prepare a statement to check if the username or email already exists
                    $stmt = $conn->prepare('SELECT user_id FROM users WHERE username = ? OR email = ?');
                    $stmt->bind_param('ss', $username, $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Check if the username or email already exists
                    if ($result->num_rows === 0) {
                        // Hash the password using password_hash()
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                        // Prepare a statement to insert the new user data
                        $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                        $stmt->bind_param('sss', $username, $email, $hashedPassword);
                        $stmt->execute();

                        // Check if the insertion was successful
                        if ($stmt->affected_rows === 1) {
                            // Start a new session for the newly registered user
                            $_SESSION['user_id'] = $conn->insert_id;
                            echo json_encode(['status' => 'register_success']);
                        } else {
                            // Insertion failed, return an error
                            echo json_encode(['status' => 'register_error', 'message' => 'Failed to register user']);
                        }
                    } else {
                        // Username or email already exists, return an error
                        echo json_encode(['status' => 'register_error', 'message' => 'Username or email already taken']);
                    }
                }
            } else {
                // Input fields are not set, return an error
                echo json_encode(['status' => 'register_error', 'message' => 'Please fill in all fields']);
            }
        } elseif ($_POST['action'] === 'logout') {
            // Logout action
            // Unset the user ID from the session
            unset($_SESSION['user_id']);
            echo json_encode(['status' => 'logout_success']);
        }
    }
} else {
    // Invalid request method, return an error
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}