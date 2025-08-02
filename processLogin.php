<?php
session_start();
header('Content-Type: application/json');

// Include database connection
require_once 'connection.php';

// Get POST data
$role = $_POST['role'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if (empty($role) || empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    if ($role === 'admin') {
        // Admin login - using username
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = 'admin'");
        $stmt->bind_param("s", $username);
    } else {
        // Caregiver login - using NIC
        $stmt = $conn->prepare("SELECT id, nic, password, role FROM caregivers WHERE nic = ? AND role = 'caregiver'");
        $stmt->bind_param("s", $username);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (assuming passwords are hashed)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $role === 'admin' ? $user['username'] : $user['nic'];
            $_SESSION['logged_in'] = true;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'role' => $user['role']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 