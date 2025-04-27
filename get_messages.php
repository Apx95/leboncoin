<?php
session_start();
require 'db.php';

// Configuration des headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Non autorisé']));
}

// Validation de l'ID de conversation
if (!isset($_GET['conversation_id']) || !is_numeric($_GET['conversation_id'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'ID de conversation invalide']));
}

try {
    $db->beginTransaction();

    // Vérification des droits d'accès à la conversation
    $stmt = $db->prepare("
        SELECT DISTINCT 
            c.conversation_id,
            c.annonce_id,
            a.titre as annonce_titre,
            a.prix,
            CASE 
                WHEN m.expediteur_id = ? THEN u_dest.pseudo
                ELSE u_exp.pseudo
            END as autre_utilisateur,
            CASE 
                WHEN m.expediteur_id = ? THEN u_dest.utilisateur_id
                ELSE u_exp.utilisateur_id
            END as autre_utilisateur_id
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

    // Récupération des messages avec gestion des nouveaux messages
    $conditions = ['m.conversation_id = ?'];
    $params = [$_GET['conversation_id']];

    if (isset($_GET['after']) && is_numeric($_GET['after'])) {
        $conditions[] = 'm.message_id > ?';
        $params[] = $_GET['after'];
    }

    $stmt = $db->prepare("
        SELECT 
            m.message_id,
            m.expediteur_id,
            m.destinataire_id,
            m.message,
            m.date_envoi,
            m.lu,
            u.pseudo as expediteur_pseudo
        FROM messages m
        JOIN utilisateurs u ON m.expediteur_id = u.utilisateur_id
        WHERE " . implode(' AND ', $conditions) . "
        ORDER BY m.date_envoi ASC
    ");
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Marquer les messages comme lus
    $stmt = $db->prepare("
        UPDATE messages 
        SET lu = 1 
        WHERE conversation_id = ? 
        AND destinataire_id = ? 
        AND lu = 0
    ");
    $stmt->execute([$_GET['conversation_id'], $_SESSION['user_id']]);

    // Compter les messages non lus dans les autres conversations
    $stmt = $db->prepare("
        SELECT COUNT(*) as total_non_lus
        FROM messages
        WHERE destinataire_id = ?
        AND lu = 0
        AND conversation_id != ?
    ");
    $stmt->execute([$_SESSION['user_id'], $_GET['conversation_id']]);
    $unreadCount = $stmt->fetch(PDO::FETCH_ASSOC);

    $db->commit();

    echo json_encode([
        'success' => true,
        'conversation' => [
            'id' => $conversation['conversation_id'],
            'annonce_id' => $conversation['annonce_id'],
            'annonce_titre' => $conversation['annonce_titre'],
            'prix' => $conversation['prix'],
            'autre_utilisateur' => $conversation['autre_utilisateur'],
            'autre_utilisateur_id' => $conversation['autre_utilisateur_id']
        ],
        'messages' => array_map(function($msg) {
            return [
                'message_id' => $msg['message_id'],
                'expediteur_id' => $msg['expediteur_id'],
                'destinataire_id' => $msg['destinataire_id'],
                'message' => htmlspecialchars($msg['message']),
                'date_envoi' => $msg['date_envoi'],
                'lu' => $msg['lu'],
                'expediteur_pseudo' => $msg['expediteur_pseudo']
            ];
        }, $messages),
        'unread_count' => $unreadCount['total_non_lus']
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log('Erreur get_messages.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des messages',
        'details' => $e->getMessage()
    ]);
}