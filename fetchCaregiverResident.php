<?php
require 'connection.php';

try {
    // Join caregivers and residents to get caregiver name
    $query = "
        SELECT 
            r.full_name AS resident_name,
            r.age,
            r.gender,
            r.medical_condition,
            r.emergency_contact,
            c.full_name AS caregiver_name
        FROM residents r
        LEFT JOIN caregivers c ON r.caregiver_id = c.id
    ";

    $stmt = $pdo->query($query);
    $residents = $stmt->fetchAll();

    // Return JSON
    header('Content-Type: application/json');
    echo json_encode($residents);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve residents: ' . $e->getMessage()]);
}
?>
