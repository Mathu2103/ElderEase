<?php
require_once 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM residents WHERE id = ?");
        $stmt->execute([$id]);
        $resident = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resident) {
            echo json_encode($resident);
        } else {
            echo json_encode(["error" => "Resident not found."]);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Missing resident ID."]);
}
?>
