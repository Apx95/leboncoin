<?php
require 'db.php';

if (!isset($_GET['categorie_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'categorie_id manquant']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT id, nom FROM marques WHERE categorie_id = ?");
    $stmt->execute([$_GET['categorie_id']]);
    $marques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($marques);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}