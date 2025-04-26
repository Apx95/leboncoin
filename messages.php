<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Get all conversations for the current user
    $stmt = $db->prepare("
        SELECT DISTINCT 
            m.conversation_id,
            m.message,
            m.date_envoi,
            m.lu,
            a.annonce_id,
            a.titre as annonce_titre,
            a.prix,
            CASE 
                WHEN m.expediteur_id = ? THEN u_dest.pseudo
                ELSE u_exp.pseudo
            END as autre_utilisateur,
            CASE 
                WHEN m.expediteur_id = ? THEN u_dest.utilisateur_id
                ELSE u_exp.utilisateur_id
            END as autre_utilisateur_id,
            (
                SELECT COUNT(*) 
                FROM messages m2 
                WHERE m2.conversation_id = m.conversation_id 
                AND m2.lu = 0 
                AND m2.destinataire_id = ?
            ) as nb_non_lus
        FROM messages m
        JOIN conversations c ON m.conversation_id = c.conversation_id
        JOIN annonces a ON c.annonce_id = a.annonce_id
        JOIN utilisateurs u_exp ON m.expediteur_id = u_exp.utilisateur_id
        JOIN utilisateurs u_dest ON m.destinataire_id = u_dest.utilisateur_id
        WHERE m.expediteur_id = ? OR m.destinataire_id = ?
        GROUP BY m.conversation_id
        ORDER BY MAX(m.date_envoi) DESC
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
    error_log($e->getMessage());
    $error = "Une erreur est survenue";
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
            transition: background-color 0.2s;
        }
        .conversation:hover {
            background-color: #f8f9fa;
        }
        .conversation.active {
            background-color: #e9ecef;
        }
        .message-container {
            height: 400px;
            overflow-y: auto;
        }
        .message {
            max-width: 75%;
            margin-bottom: 1rem;
        }
        .message.sent {
            margin-left: auto;
        }
        .unread {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mes Messages</h2>
            <a href="accueil.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (empty($conversations)): ?>
            <div class="alert alert-info">
                Vous n'avez pas encore de messages.
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Liste des conversations -->
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <?php foreach($conversations as $conv): ?>
                            <a href="#" 
                               class="list-group-item list-group-item-action conversation <?= $conv['nb_non_lus'] > 0 ? 'unread' : '' ?>"
                               data-conversation="<?= $conv['conversation_id'] ?>">
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
                                    Dernier message : <?= date('d/m/Y H:i', strtotime($conv['date_envoi'])) ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Zone de messages -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" id="conversation-header">
                            Sélectionnez une conversation
                        </div>
                        <div class="card-body">
                            <div class="message-container" id="messages">
                                <div class="text-center text-muted">
                                    <i class="bi bi-chat-dots"></i>
                                    Sélectionnez une conversation pour voir les messages
                                </div>
                            </div>
                            <form id="message-form" class="mt-3 d-none">
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
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let activeConversation = null;

        document.querySelectorAll('.conversation').forEach(conv => {
            conv.addEventListener('click', function(e) {
                e.preventDefault();
                const convId = this.dataset.conversation;
                loadMessages(convId);
                
                // Update active state
                document.querySelectorAll('.conversation').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                this.classList.remove('unread');
                
                // Show message form
                document.getElementById('message-form').classList.remove('d-none');
            });
        });

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
                        document.getElementById('conversation-header').textContent = 
                            `Discussion avec ${data.autre_utilisateur}`;
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

        function displayMessages(messages) {
            const container = document.getElementById('messages');
            container.innerHTML = messages.map(msg => `
                <div class="message ${msg.expediteur_id == <?= $_SESSION['user_id'] ?> ? 'sent' : ''}">
                    <div class="card ${msg.expediteur_id == <?= $_SESSION['user_id'] ?> ? 'bg-primary text-white' : ''}">
                        <div class="card-body py-2">
                            <p class="card-text mb-1">${msg.message}</p>
                            <small class="${msg.expediteur_id == <?= $_SESSION['user_id'] ?> ? 'text-white-50' : 'text-muted'}">
                                ${new Date(msg.date_envoi).toLocaleString()}
                            </small>
                        </div>
                    </div>
                </div>
            `).join('');
            container.scrollTop = container.scrollHeight;
        }
    </script>
</body>
</html>