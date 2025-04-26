<?php
session_start();
require 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log incoming request data
error_log('Debug - POST: ' . print_r($_POST, true));
error_log('Debug - RAW: ' . file_get_contents('php://input'));

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Non autorisé']));
}

// Get input data (support both POST and JSON)
$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $_POST;
    }
}

// Validate input
if (empty($data)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Aucune donnée reçue']));
}

if (empty($data['message'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Le message ne peut pas être vide']));
}

try {
    $db->beginTransaction();

    $conversationId = null;
    $destinataireId = null;

    // Handle new conversation
    if (isset($data['annonce_id']) && isset($data['destinataire_id'])) {
        // Check if conversation already exists
        $stmt = $db->prepare("
            SELECT conversation_id 
            FROM conversations 
            WHERE annonce_id = ? AND EXISTS (
                SELECT 1 FROM messages 
                WHERE messages.conversation_id = conversations.conversation_id
                AND ((expediteur_id = ? AND destinataire_id = ?) 
                OR (expediteur_id = ? AND destinataire_id = ?))
            )
        ");
        
        $stmt->execute([
            $data['annonce_id'],
            $_SESSION['user_id'],
            $data['destinataire_id'],
            $data['destinataire_id'],
            $_SESSION['user_id']
        ]);
        
        $existingConv = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingConv) {
            $conversationId = $existingConv['conversation_id'];
        } else {
            // Create new conversation
            $stmt = $db->prepare("
                INSERT INTO conversations (annonce_id, date_creation) 
                VALUES (?, NOW())
            ");
            $stmt->execute([$data['annonce_id']]);
            $conversationId = $db->lastInsertId();
        }
        $destinataireId = $data['destinataire_id'];
    }
    // Handle existing conversation
    elseif (isset($data['conversation_id'])) {
        // Verify conversation access and get recipient
        $stmt = $db->prepare("
            SELECT DISTINCT
                c.conversation_id,
                CASE 
                    WHEN m.expediteur_id = ? THEN m.destinataire_id
                    ELSE m.expediteur_id
                END as destinataire_id
            FROM conversations c
            JOIN messages m ON c.conversation_id = m.conversation_id
            WHERE c.conversation_id = ?
            AND (m.expediteur_id = ? OR m.destinataire_id = ?)
            LIMIT 1
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $data['conversation_id'],
            $_SESSION['user_id'],
            $_SESSION['user_id']
        ]);
        
        $conv = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$conv) {
            throw new Exception('Conversation non autorisée');
        }
        
        $conversationId = $conv['conversation_id'];
        $destinataireId = $conv['destinataire_id'];
    } else {
        throw new Exception('Paramètres manquants pour la conversation');
    }

    // Insert message
    $stmt = $db->prepare("
        INSERT INTO messages (
            conversation_id,
            expediteur_id,
            destinataire_id,
            message,
            date_envoi,
            lu
        ) VALUES (?, ?, ?, ?, NOW(), 0)
    ");
    
    $stmt->execute([
        $conversationId,
        $_SESSION['user_id'],
        $destinataireId,
        trim($data['message'])
    ]);

    $messageId = $db->lastInsertId();

    // Get message details for response
    $stmt = $db->prepare("
        SELECT 
            m.*,
            u.pseudo as expediteur_pseudo,
            c.annonce_id
        FROM messages m
        JOIN utilisateurs u ON m.expediteur_id = u.utilisateur_id
        JOIN conversations c ON m.conversation_id = c.conversation_id
        WHERE m.message_id = ?
    ");
    
    $stmt->execute([$messageId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => [
            'id' => $message['message_id'],
            'conversation_id' => $message['conversation_id'],
            'expediteur_id' => $message['expediteur_id'],
            'expediteur_pseudo' => $message['expediteur_pseudo'],
            'message' => htmlspecialchars($message['message']),
            'date_envoi' => $message['date_envoi'],
            'annonce_id' => $message['annonce_id']
        ]
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log('Erreur send_message.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de l\'envoi du message',
        'details' => $e->getMessage()
    ]);
}