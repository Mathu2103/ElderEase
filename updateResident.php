<?php
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['residentId'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $medical_condition = $_POST['medical_condition'] ?? '';
    $emergency_contact = $_POST['emergency_contact'] ?? '';
    $caregiver_id = $_POST['caregiver_id'] ?? '';

    if (empty($id) || empty($full_name) || empty($age) || empty($gender)) {
        echo "Missing required fields.";
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE residents SET full_name = ?, age = ?, gender = ?, medical_condition = ?, emergency_contact = ?, caregiver_id = ? WHERE id = ?");
        $stmt->execute([$full_name, $age, $gender, $medical_condition, $emergency_contact, $caregiver_id, $id]);
        echo "success";
    } catch (Exception $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
