<?php
require 'db.php';

header('Content-Type: application/json');

$categorie_id = isset($_GET['categorie_id']) ? intval($_GET['categorie_id']) : 0;

try {
    $stmt = $db->prepare("
        SELECT categorie_id, nom 
        FROM categories 
        WHERE categorie_parent_id = ?
        ORDER BY nom
    ");
    $stmt->execute([$categorie_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}