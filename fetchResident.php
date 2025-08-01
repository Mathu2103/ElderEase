<?php
require_once 'connection.php';

try {
    $stmt = $pdo->query("SELECT * FROM residents ORDER BY full_name ASC");
    $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($residents);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
