<?php
session_start();
require 'db.php';

// Verify authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Get all conversations with latest message and unread count
    $stmt = $db->prepare("
        SELECT 
            c.conversation_id,
            c.annonce_id,
            a.titre as annonce_titre,
            a.prix,
            (
                SELECT message 
                FROM messages m2 
                WHERE m2.conversation_id = c.conversation_id 
                ORDER BY m2.date_envoi DESC 
                LIMIT 1
            ) as dernier_message,
            (
                SELECT date_envoi 
                FROM messages m2 
                WHERE m2.conversation_id = c.conversation_id 
                ORDER BY m2.date_envoi DESC 
                LIMIT 1
            ) as date_dernier_message,
            (
                SELECT COUNT(*) 
                FROM messages m2 
                WHERE m2.conversation_id = c.conversation_id 
                AND m2.lu = 0 
                AND m2.destinataire_id = ?
            ) as nb_non_lus,
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
        WHERE m.expediteur_id = ? OR m.destinataire_id = ?
        GROUP BY c.conversation_id
        ORDER BY date_dernier_message DESC
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id']
    ]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log('Erreur messages.php: ' . $e->getMessage());
    $error = "Une erreur est survenue lors du chargement des messages";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Messages - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .conversation {
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .conversation:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .conversation.active {
            background-color: #e9ecef;
            border-left-color: #0d6efd;
        }
        .message-container {
            height: 60vh;
            overflow-y: auto;
            padding: 1rem;
        }
        .message {
            max-width: 75%;
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.3s ease forwards;
        }
        .message.sent {
            margin-left: auto;
        }
        .unread {
            font-weight: bold;
            position: relative;
        }
        .unread::after {
            content: '';
            position: absolute;
            right: -10px;
            top: 50%;
            width: 8px;
            height: 8px;
            background-color: #0d6efd;
            border-radius: 50%;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .typing-indicator {
            display: none;
            padding: 0.5rem;
            color: #6c757d;
            font-style: italic;
        }
        .message-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }
        .message:hover .message-actions {
            opacity: 1;
        }
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container mt-4">
        <!-- Header with buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mes Messages</h2>
            <a href="accueil.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <!-- Error messages -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Conversations list -->
            <div class="col-md-4 mb-4">
                <div class="list-group">
                    <?php if (empty($conversations)): ?>
                        <div class="alert alert-info">
                            Vous n'avez pas encore de messages.
                        </div>
                    <?php else: ?>
                        <?php foreach($conversations as $conv): ?>
                            <a href="#" 
                               class="list-group-item list-group-item-action conversation <?= $conv['nb_non_lus'] > 0 ? 'unread' : '' ?>"
                               data-conversation="<?= $conv['conversation_id'] ?>"
                               data-autre-utilisateur="<?= htmlspecialchars($conv['autre_utilisateur']) ?>"
                               data-annonce-id="<?= $conv['annonce_id'] ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1"><?= htmlspecialchars($conv['autre_utilisateur']) ?></h6>
                                    <?php if($conv['nb_non_lus'] > 0): ?>
                                        <span class="badge bg-primary"><?= $conv['nb_non_lus'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1">
                                    <?= htmlspecialchars($conv['annonce_titre']) ?><br>
                                    <small class="text-muted">
                                        Prix : <?= number_format($conv['prix'], 2, ',', ' ') ?> €
                                    </small>
                                </p>
                                <small class="text-muted">
                                    <?= htmlspecialchars(mb_strimwidth($conv['dernier_message'], 0, 50, '...')) ?><br>
                                    <?= date('d/m/Y H:i', strtotime($conv['date_dernier_message'])) ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Messages zone -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span id="conversation-header">Sélectionnez une conversation</span>
                        <div class="btn-group">
                            <a href="#" id="voir-annonce" class="btn btn-sm btn-outline-primary d-none" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> Voir l'annonce
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger d-none" id="deleteConvBtn" 
                                    data-bs-toggle="modal" data-bs-target="#deleteConvModal">
                                <i class="bi bi-trash"></i> Supprimer
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning d-none" id="reportConvBtn"
                                    data-bs-toggle="modal" data-bs-target="#reportConvModal">
                                <i class="bi bi-flag"></i> Signaler
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="message-container" id="messages">
                            <div class="text-center text-muted p-4">
                                <i class="bi bi-chat-dots fs-1"></i><br>
                                Sélectionnez une conversation pour voir les messages
                            </div>
                        </div>
                        <div class="typing-indicator" id="typing-indicator">
                            <i class="bi bi-three-dots"></i> En train d'écrire...
                        </div>
                        <form id="message-form" class="p-3 border-top d-none">
                            <div class="input-group">
                                <input type="text" class="form-control" id="message-input" 
                                       placeholder="Votre message..." required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Conversation Modal -->
    <div class="modal fade" id="deleteConvModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supprimer la conversation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette conversation ? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" onclick="deleteConversation()">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Conversation Modal -->
    <div class="modal fade" id="reportConvModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Signaler la conversation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm">
                        <div class="mb-3">
                            <label class="form-label">Motif du signalement</label>
                            <select class="form-select" id="reportReason" required>
                                <option value="">Choisir un motif</option>
                                <option value="spam">Spam</option>
                                <option value="harassment">Harcèlement</option>
                                <option value="inappropriate">Contenu inapproprié</option>
                                <option value="scam">Arnaque</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="reportDescription" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-warning" onclick="reportConversation()">Signaler</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let activeConversation = null;
    let lastMessageId = null;
    let checkMessagesInterval;

    // Handle conversation selection
    document.querySelectorAll('.conversation').forEach(conv => {
        conv.addEventListener('click', function(e) {
            e.preventDefault();
            const convId = this.dataset.conversation;
            const autreUtilisateur = this.dataset.autreUtilisateur;
            const annonceId = this.dataset.annonceId;
            
            loadMessages(convId);
            
            // Update UI
            document.querySelectorAll('.conversation').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            this.classList.remove('unread');
            
            // Show form and header
            document.getElementById('message-form').classList.remove('d-none');
            
            // Configure buttons
            const voirAnnonceBtn = document.getElementById('voir-annonce');
            const deleteConvBtn = document.getElementById('deleteConvBtn');
            const reportConvBtn = document.getElementById('reportConvBtn');
            
            voirAnnonceBtn.classList.remove('d-none');
            voirAnnonceBtn.href = `voir_annonce.php?id=${annonceId}`;
            deleteConvBtn.classList.remove('d-none');
            reportConvBtn.classList.remove('d-none');
            
            document.getElementById('conversation-header').textContent = 
                `Discussion avec ${autreUtilisateur}`;
            
            // Start checking for new messages
            if (checkMessagesInterval) clearInterval(checkMessagesInterval);
            checkMessagesInterval = setInterval(() => checkNewMessages(convId), 5000);
        });
    });

    // Handle message form submission
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!activeConversation) return;

        const input = document.getElementById('message-input');
        const message = input.value.trim();
        if (!message) return;

        sendMessage(activeConversation, message);
        input.value = '';
    });

    function loadMessages(conversationId) {
        activeConversation = conversationId;
        fetch(`get_messages.php?conversation_id=${conversationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessages(data.messages);
                    lastMessageId = data.messages[data.messages.length - 1]?.message_id;
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    function sendMessage(conversationId, message) {
        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMessages(conversationId);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function deleteMessage(messageId) {
        if (!confirm('Voulez-vous vraiment supprimer ce message ?')) return;

        fetch('delete_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message_id: messageId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMessages(activeConversation);
            } else {
                alert(data.error || 'Erreur lors de la suppression');
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function deleteConversation() {
        fetch('delete_conversation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ conversation_id: activeConversation })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Erreur lors de la suppression');
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function reportConversation() {
        const reason = document.getElementById('reportReason').value;
        const description = document.getElementById('reportDescription').value;

        if (!reason || !description) {
            alert('Veuillez remplir tous les champs');
            return;
        }

        fetch('report_conversation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                conversation_id: activeConversation,
                reason: reason,
                description: description
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('reportConvModal'));
                modal.hide();
                alert('Signalement envoyé avec succès');
            } else {
                alert(data.error || 'Erreur lors du signalement');
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function displayMessages(messages) {
        const container = document.getElementById('messages');
        container.innerHTML = messages.map(msg => `
            <div class="message ${msg.expediteur_id == <?= $_SESSION['user_id'] ?> ? 'sent' : ''}">
                <div class="card ${msg.expediteur_id == <?= $_SESSION['user_id'] ?> ? 'bg-primary text-white' : ''}">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between">
                            <p class="card-text mb-1">${msg.message}</p>
                            ${msg.expediteur_id == <?= $_SESSION['user_id'] ?> ? `
                                <div class="message-actions">
                                    <button class="btn btn-sm text-white-50" onclick="deleteMessage(${msg.message_id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                        <small class="${msg.expediteur_id == <?= $_SESSION['user_id'] ?> ? 'text-white-50' : 'text-muted'}">
                            ${new Date(msg.date_envoi).toLocaleString()}
                        </small>
                    </div>
                </div>
            </div>
        `).join('');
        container.scrollTop = container.scrollHeight;
    }

    function checkNewMessages(conversationId) {
        if (!lastMessageId) return;
        
        fetch(`get_messages.php?conversation_id=${conversationId}&after=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages.length > 0) {
                    loadMessages(conversationId);
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (checkMessagesInterval) clearInterval(checkMessagesInterval);
    });
</script>
</body>
</html>