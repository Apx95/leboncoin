<?php
session_start();
require 'db.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming request
error_log('Delete message request: ' . print_r($_POST, true));

// Authentication check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode([
        'success' => false,
        'error' => 'Non autorisé',
        'code' => 'AUTH_REQUIRED'
    ]));
}

// Get and validate input data
try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    if (!isset($data['message_id']) || !filter_var($data['message_id'], FILTER_VALIDATE_INT)) {
        throw new Exception('ID de message invalide ou manquant');
    }

    $messageId = (int)$data['message_id'];
    
    $db->beginTransaction();

    // Get message details first
    $stmt = $db->prepare("
        SELECT m.*, c.annonce_id 
        FROM messages m
        JOIN conversations c ON m.conversation_id = c.conversation_id
        WHERE m.message_id = ? 
        AND m.expediteur_id = ?
        LIMIT 1
    ");
    $stmt->execute([$messageId, $_SESSION['user_id']]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        throw new Exception('Message non trouvé ou non autorisé', 403);
    }

    // Check if it's the only message in conversation
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM messages 
        WHERE conversation_id = ?
    ");
    $stmt->execute([$message['conversation_id']]);
    $messageCount = $stmt->fetchColumn();

    // Delete the message
    $stmt = $db->prepare("DELETE FROM messages WHERE message_id = ?");
    $success = $stmt->execute([$messageId]);

    if (!$success) {
        throw new Exception('Erreur lors de la suppression');
    }

    // If it was the last message, delete the conversation too
    if ($messageCount === 1) {
        $stmt = $db->prepare("DELETE FROM conversations WHERE conversation_id = ?");
        $stmt->execute([$message['conversation_id']]);
        $conversationDeleted = true;
    }

    // Log the deletion
    $stmt = $db->prepare("
        INSERT INTO message_deletions (
            message_id,
            user_id,
            conversation_id,
            deletion_date,
            message_content
        ) VALUES (?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([
        $messageId,
        $_SESSION['user_id'],
        $message['conversation_id'],
        $message['message']
    ]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'data' => [
            'message_id' => $messageId,
            'conversation_deleted' => $conversationDeleted ?? false
        ]
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log('Delete message error: ' . $e->getMessage());
    
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => 'DELETE_ERROR'
    ]);
}
?>
<style>
.delete-btn {
    opacity: 0;
    transition: opacity 0.3s ease;
    position: absolute;
    right: 5px;
    top: 5px;
}

.message:hover .delete-btn {
    opacity: 1;
}

.delete-btn.confirming {
    animation: pulse 1s infinite;
}

.message.deleting {
    animation: fadeOut 0.5s forwards;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

@keyframes fadeOut {
    to {
        opacity: 0;
        transform: translateX(100px);
    }
}

.toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 1rem;
    border-radius: 4px;
    z-index: 1000;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
async function deleteMessage(messageId, element) {
    const btn = element.querySelector('.delete-btn');
    
    // First click: show confirmation
    if (!btn.classList.contains('confirming')) {
        btn.classList.add('confirming');
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Confirmer';
        
        // Reset after 3 seconds if not confirmed
        setTimeout(() => {
            if (btn.classList.contains('confirming')) {
                btn.classList.remove('confirming');
                btn.innerHTML = '<i class="bi bi-trash"></i>';
            }
        }, 3000);
        
        return;
    }

    try {
        // Visual feedback
        element.classList.add('deleting');
        btn.disabled = true;

        const response = await fetch('delete_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message_id: messageId })
        });

        const data = await response.json();

        if (data.success) {
            // Remove message from DOM
            element.remove();

            // Show success toast
            showToast('Message supprimé avec succès');

            // If conversation was deleted, redirect
            if (data.data.conversation_deleted) {
                showToast('Conversation supprimée', 'info');
                setTimeout(() => window.location.reload(), 1500);
            }
        } else {
            throw new Error(data.error);
        }

    } catch (error) {
        console.error('Erreur:', error);
        element.classList.remove('deleting');
        btn.disabled = false;
        btn.classList.remove('confirming');
        btn.innerHTML = '<i class="bi bi-trash"></i>';
        showToast(error.message || 'Erreur lors de la suppression', 'error');
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Prevent accidental deletes when scrolling
let touchStartY = 0;
document.addEventListener('touchstart', e => {
    touchStartY = e.touches[0].clientY;
});

document.addEventListener('touchmove', e => {
    const touchEndY = e.touches[0].clientY;
    const deltaY = touchEndY - touchStartY;
    
    if (Math.abs(deltaY) > 10) {
        const deleteButtons = document.querySelectorAll('.delete-btn.confirming');
        deleteButtons.forEach(btn => {
            btn.classList.remove('confirming');
            btn.innerHTML = '<i class="bi bi-trash"></i>';
        });
    }
});
</script>