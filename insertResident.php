<?php
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $medical_condition = $_POST['medical_condition'] ?? '';
    $emergency_contact = $_POST['emergency_contact'] ?? '';
    $caregiver_id = $_POST['caregiver_id'] ?? '';

    // If you want to allow optional fields, remove them from this check
    if (
        empty($full_name) || empty($age) || empty($gender)
    ) {
        echo json_encode(["status" => "error", "message" => "Full name, age, and gender are required."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO residents (full_name, age, gender, medical_condition, emergency_contact, caregiver_id)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $age, $gender, $medical_condition, $emergency_contact, $caregiver_id]);
        echo "success";
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
