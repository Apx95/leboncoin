<?php
session_start();
require 'db.php';

// Vérification du paramètre ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Annonce invalide";
    header('Location: accueil.php');
    exit;
}

try {
    // Récupération de l'annonce avec toutes les informations
    $stmt = $db->prepare("
        SELECT a.*, 
               u.pseudo,
               u.telephone,
               u.utilisateur_id as vendeur_id,
               c.nom as categorie_nom,
               m.nom as marque_nom,
               GROUP_CONCAT(DISTINCT i.url) as images,
               (SELECT COUNT(*) FROM favoris f WHERE f.annonce_id = a.annonce_id) as nb_favoris
        FROM annonces a
        LEFT JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
        LEFT JOIN categories c ON a.categorie_id = c.categorie_id
        LEFT JOIN marques m ON a.marque_id = m.id
        LEFT JOIN images i ON a.annonce_id = i.annonce_id
        WHERE a.annonce_id = ?
        GROUP BY a.annonce_id
    ");
    $stmt->execute([$_GET['id']]);
    $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$annonce) {
        $_SESSION['error'] = "Annonce introuvable";
        header('Location: accueil.php');
        exit;
    }

    // Vérifier si l'annonce est en favori pour l'utilisateur connecté
    $is_favori = false;
    if (isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ? AND annonce_id = ?");
        $stmt->execute([$_SESSION['user_id'], $_GET['id']]);
        $is_favori = $stmt->fetchColumn() > 0;
    }

} catch(PDOException $e) {
    error_log('Erreur voir_annonce.php: ' . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue";
    header('Location: accueil.php');
    exit;
}

// Vérifier si l'utilisateur peut contacter le vendeur
$can_contact = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $annonce['vendeur_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($annonce['titre']) ?> - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .carousel-item img {
            height: 400px;
            object-fit: contain;
            background-color: #f8f9fa;
        }
        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        .price-badge {
            font-size: 1.5rem;
        }
        .modal-message {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <!-- Messages d'erreur/succès -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Contenu principal -->
    <div class="container mt-4">
        <!-- ... Votre code existant pour le fil d'Ariane ... -->

        <div class="row">
            <!-- Carousel d'images -->
            <div class="col-md-8 position-relative">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn <?= $is_favori ? 'btn-danger' : 'btn-outline-danger' ?> favorite-btn"
                            onclick="toggleFavorite(<?= $annonce['annonce_id'] ?>)">
                        <i class="bi bi-heart<?= $is_favori ? '-fill' : '' ?>"></i>
                    </button>
                <?php endif; ?>

                <?php 
                $images = explode(',', $annonce['images']);
                if (!empty($images[0])): 
                ?>
                    <div id="carouselAnnonce" class="carousel slide">
                        <div class="carousel-inner">
                            <?php foreach($images as $index => $image): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="uploads/<?= htmlspecialchars($image) ?>" 
                                         class="d-block w-100" 
                                         alt="Image <?= $index + 1 ?>"
                                         onerror="this.src='images/default.jpg'">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselAnnonce" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselAnnonce" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Informations de l'annonce -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title h4"><?= htmlspecialchars($annonce['titre']) ?></h1>
                        <p class="price-badge text-primary fw-bold">
                            <?= number_format($annonce['prix'], 2, ',', ' ') ?> €
                            <?php if($annonce['prix_negociable']): ?>
                                <span class="badge bg-info">Négociable</span>
                            <?php endif; ?>
                        </p>
                        <hr>
                        <p><i class="bi bi-person"></i> <?= htmlspecialchars($annonce['pseudo']) ?></p>
                        <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($annonce['localisation']) ?></p>
                        <?php if (!$annonce['masquer_telephone'] && $annonce['telephone']): ?>
                            <p><i class="bi bi-telephone"></i> <?= htmlspecialchars($annonce['telephone']) ?></p>
                        <?php endif; ?>
                        <p><i class="bi bi-clock"></i> Publiée le <?= date('d/m/Y', strtotime($annonce['date_creation'])) ?></p>
                        <hr>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Connectez-vous pour contacter le vendeur
                            </a>
                        <?php elseif ($can_contact): ?>
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#contactModal">
                                <i class="bi bi-chat-dots"></i> Contacter le vendeur
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary w-100" disabled>
                                <i class="bi bi-chat-dots"></i> Votre annonce
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ... Votre code existant pour la description et les détails ... -->
    </div>

    <!-- Modal de contact -->
    <?php if ($can_contact): ?>
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contacter le vendeur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm" onsubmit="return envoyerMessage(event)">
                        <input type="hidden" id="annonce_id" value="<?= $annonce['annonce_id'] ?>">
                        <input type="hidden" id="destinataire_id" value="<?= $annonce['vendeur_id'] ?>">
                        <div class="mb-3">
                            <label for="message" class="form-label">Votre message</label>
                            <textarea class="form-control" id="message" rows="4" required 
                                    minlength="10" maxlength="1000"></textarea>
                            <div class="form-text">
                                Entre 10 et 1000 caractères
                            </div>
                        </div>
                        <div class="alert alert-danger modal-message" id="errorMessage"></div>
                        <div class="alert alert-success modal-message" id="successMessage"></div>
                        <button type="submit" class="btn btn-primary" id="sendButton">
                            <span class="spinner-border spinner-border-sm d-none" id="sendSpinner"></span>
                            Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFavorite(annonceId) {
            fetch('toggle_favori.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ annonce_id: annonceId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = document.querySelector('.favorite-btn');
                    const icon = btn.querySelector('i');
                    if (data.is_favori) {
                        btn.classList.remove('btn-outline-danger');
                        btn.classList.add('btn-danger');
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                    } else {
                        btn.classList.add('btn-outline-danger');
                        btn.classList.remove('btn-danger');
                        icon.classList.add('bi-heart');
                        icon.classList.remove('bi-heart-fill');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }

        function envoyerMessage(event) {
            event.preventDefault();
            
            const message = document.getElementById('message').value.trim();
            const annonceId = document.getElementById('annonce_id').value;
            const destinataireId = document.getElementById('destinataire_id').value;
            const errorDiv = document.getElementById('errorMessage');
            const successDiv = document.getElementById('successMessage');
            const sendButton = document.getElementById('sendButton');
            const spinner = document.getElementById('sendSpinner');
            
            // Cacher les messages précédents
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
            
            // Validation
            if (message.length < 10) {
                errorDiv.textContent = 'Le message doit contenir au moins 10 caractères';
                errorDiv.style.display = 'block';
                return false;
            }

            // Désactiver le bouton et afficher le spinner
            sendButton.disabled = true;
            spinner.classList.remove('d-none');
            
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    annonce_id: annonceId,
                    destinataire_id: destinataireId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successDiv.textContent = 'Message envoyé avec succès';
                    successDiv.style.display = 'block';
                    document.getElementById('message').value = '';
                    setTimeout(() => {
                        window.location.href = 'messages.php';
                    }, 1500);
                } else {
                    errorDiv.textContent = data.error || 'Erreur lors de l\'envoi du message';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorDiv.textContent = 'Une erreur est survenue lors de l\'envoi du message';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                // Réactiver le bouton et cacher le spinner
                sendButton.disabled = false;
                spinner.classList.add('d-none');
            });

            return false;
        }
    </script>
</body>
</html>