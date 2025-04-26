<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Verify authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Non autorisé']));
}

// Validate conversation ID
if (!isset($_GET['conversation_id']) || !is_numeric($_GET['conversation_id'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'ID de conversation invalide']));
}

try {
    $db->beginTransaction();

    // Verify user is part of the conversation
    $stmt = $db->prepare("
        SELECT DISTINCT 
            c.conversation_id,
            a.titre as annonce_titre,
            CASE 
                WHEN m.expediteur_id = ? THEN u_dest.pseudo
                ELSE u_exp.pseudo
            END as autre_utilisateur
        FROM conversations c
        JOIN messages m ON c.conversation_id = m.conversation_id
        JOIN annonces a ON c.annonce_id = a.annonce_id
        JOIN utilisateurs u_exp ON m.expediteur_id = u_exp.utilisateur_id
        JOIN utilisateurs u_dest ON m.destinataire_id = u_dest.utilisateur_id
        WHERE c.conversation_id = ?
        AND (m.expediteur_id = ? OR m.destinataire_id = ?)
        LIMIT 1
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_GET['conversation_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id']
    ]);
    
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$conversation) {
        $db->rollBack();
        http_response_code(403);
        die(json_encode(['success' => false, 'error' => 'Conversation non autorisée']));
    }

    // Get all messages
    $stmt = $db->prepare("
        SELECT 
            m.message_id,
            m.expediteur_id,
            m.message,
            m.date_envoi,
            m.lu,
            u.pseudo as expediteur_pseudo
        FROM messages m
        JOIN utilisateurs u ON m.expediteur_id = u.utilisateur_id
        WHERE m.conversation_id = ?
        ORDER BY m.date_envoi ASC
    ");
    $stmt->execute([$_GET['conversation_id']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark unread messages as read
    $stmt = $db->prepare("
        UPDATE messages 
        SET lu = 1 
        WHERE conversation_id = ? 
        AND destinataire_id = ? 
        AND lu = 0
    ");
    $stmt->execute([$_GET['conversation_id'], $_SESSION['user_id']]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation['conversation_id'],
        'annonce_titre' => $conversation['annonce_titre'],
        'autre_utilisateur' => $conversation['autre_utilisateur'],
        'messages' => array_map(function($msg) {
            return [
                'message_id' => $msg['message_id'],
                'expediteur_id' => $msg['expediteur_id'],
                'message' => htmlspecialchars($msg['message']),
                'date_envoi' => $msg['date_envoi'],
                'lu' => $msg['lu'],
                'expediteur_pseudo' => $msg['expediteur_pseudo']
            ];
        }, $messages)
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log('Erreur get_messages.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des messages'
    ]);
}