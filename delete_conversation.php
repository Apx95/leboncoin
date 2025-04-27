<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Non autorisé']));
}

// Récupération des données
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['conversation_id']) || !is_numeric($data['conversation_id'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'ID de conversation invalide']));
}

try {
    $db->beginTransaction();

    // Vérifier que l'utilisateur participe à la conversation
    $stmt = $db->prepare("
        SELECT DISTINCT conversation_id 
        FROM messages 
        WHERE conversation_id = ? 
        AND (expediteur_id = ? OR destinataire_id = ?)
        LIMIT 1
    ");
    $stmt->execute([
        $data['conversation_id'], 
        $_SESSION['user_id'], 
        $_SESSION['user_id']
    ]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Conversation non autorisée');
    }

    // Supprimer tous les messages de la conversation
    $stmt = $db->prepare("DELETE FROM messages WHERE conversation_id = ?");
    $stmt->execute([$data['conversation_id']]);

    // Supprimer la conversation
    $stmt = $db->prepare("DELETE FROM conversations WHERE conversation_id = ?");
    $stmt->execute([$data['conversation_id']]);

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}