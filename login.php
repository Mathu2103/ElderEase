<?php
session_start();
header('Content-Type: application/json');

// Include database connection
require_once 'connection.php';

// Get POST data
$role = $_POST['role'] ?? '';
$nic = $_POST['nic'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if (empty($role) || empty($nic) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Role, NIC and password are required']);
    exit;
}

// Sanitize inputs
$role = trim($role);
$nic = trim($nic);
$password = trim($password);

try {
    if ($role === 'admin') {
        // Admin login - using NIC
        $stmt = $conn->prepare("SELECT id, nic, password, name, email, role FROM admins WHERE nic = ? AND status = 'active'");
        $stmt->bind_param("s", $nic);
    } else {
        // Caregiver login - using NIC
        $stmt = $conn->prepare("SELECT id, nic, password, name, email, role FROM caregivers WHERE nic = ? AND status = 'active'");
        $stmt->bind_param("s", $nic);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (supports both hashed and plain text passwords)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nic'] = $user['nic'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Log successful login (optional)
            logLoginAttempt($conn, $user['id'], $nic, $role, 'success');
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'nic' => $user['nic'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            // Log failed login attempt
            logLoginAttempt($conn, $user['id'], $nic, $role, 'failed_password');
            
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        // Log failed login attempt for non-existent user
        logLoginAttempt($conn, null, $nic, $role, 'failed_user_not_found');
        
        $userType = ($role === 'admin') ? 'Admin' : 'Caregiver';
        echo json_encode(['success' => false, 'message' => $userType . ' not found or account inactive']);
    }
    
} catch (Exception $e) {
    // Log database error
    error_log("Login database error: " . $e->getMessage());
    
    echo json_encode(['success' => false, 'message' => 'Database error occurred. Please try again later.']);
}

$stmt->close();
$conn->close();

// Function to log login attempts
function logLoginAttempt($conn, $user_id, $nic, $role, $status) {
    try {
        $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, nic, role, status, ip_address, timestamp) VALUES (?, ?, ?, ?, ?, NOW())");
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $log_stmt->bind_param("issss", $user_id, $nic, $role, $status, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
    } catch (Exception $e) {
        // Silently fail logging - don't break the main login process
        error_log("Failed to log login attempt: " . $e->getMessage());
    }
}
?> 