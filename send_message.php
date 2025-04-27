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

// Récupération des données (POST ou JSON)
$data = [];
$rawInput = file_get_contents('php://input');
error_log('Debug - Raw input: ' . $rawInput);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $data = json_decode($rawInput, true);
    } else {
        $data = $_POST;
    }
}

error_log('Debug - Processed data: ' . print_r($data, true));

// Validation des données
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

    // Gestion d'une nouvelle conversation
    if (isset($data['annonce_id']) && isset($data['destinataire_id'])) {
        // Vérification de l'existence d'une conversation
        $stmt = $db->prepare("
            SELECT conversation_id 
            FROM conversations c
            WHERE c.annonce_id = ? 
            AND EXISTS (
                SELECT 1 
                FROM messages m 
                WHERE m.conversation_id = c.conversation_id
                AND (
                    (m.expediteur_id = ? AND m.destinataire_id = ?) 
                    OR (m.expediteur_id = ? AND m.destinataire_id = ?)
                )
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
            // Création d'une nouvelle conversation
            $stmt = $db->prepare("
                INSERT INTO conversations (annonce_id, date_creation) 
                VALUES (?, NOW())
            ");
            $stmt->execute([$data['annonce_id']]);
            $conversationId = $db->lastInsertId();
        }
        $destinataireId = $data['destinataire_id'];
    }
    // Gestion d'une conversation existante
    elseif (isset($data['conversation_id'])) {
        // Vérification des droits d'accès
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

    // Insertion du message
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

    // Récupération des détails du message
    $stmt = $db->prepare("
        SELECT 
            m.*,
            u.pseudo as expediteur_pseudo,
            c.annonce_id,
            a.titre as annonce_titre
        FROM messages m
        JOIN utilisateurs u ON m.expediteur_id = u.utilisateur_id
        JOIN conversations c ON m.conversation_id = c.conversation_id
        JOIN annonces a ON c.annonce_id = a.annonce_id
        WHERE m.message_id = ?
    ");
    
    $stmt->execute([$messageId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        throw new Exception('Erreur lors de la récupération du message');
    }

    $db->commit();

    // Réponse avec les détails du message
    echo json_encode([
        'success' => true,
        'message' => [
            'id' => $message['message_id'],
            'conversation_id' => $message['conversation_id'],
            'expediteur_id' => $message['expediteur_id'],
            'expediteur_pseudo' => $message['expediteur_pseudo'],
            'message' => htmlspecialchars($message['message']),
            'date_envoi' => $message['date_envoi'],
            'annonce_id' => $message['annonce_id'],
            'annonce_titre' => $message['annonce_titre']
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