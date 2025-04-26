<?php
session_start();
require 'db.php';

// Vérification du paramètre ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: accueil.php');
    exit;
}

try {
    // Récupération de l'annonce avec toutes les informations
    $stmt = $db->prepare("
        SELECT a.*, 
               u.pseudo,
               u.telephone,
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
    error_log($e->getMessage());
    header('Location: accueil.php');
    exit;
}
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
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="accueil.php">Accueil</a></li>
                <li class="breadcrumb-item">
                    <a href="category.php?id=<?= $annonce['categorie_id'] ?>">
                        <?= htmlspecialchars($annonce['categorie_nom']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($annonce['titre']) ?></li>
            </ol>
        </nav>

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
                                         alt="Image <?= $index + 1 ?>">
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
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="bi bi-chat-dots"></i> Contacter le vendeur
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description et détails -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="h5">Description</h2>
                        <p class="card-text"><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
                        
                        <h3 class="h5 mt-4">Caractéristiques</h3>
                        <ul class="list-unstyled">
                            <li><strong>État :</strong> <?= htmlspecialchars($annonce['etat']) ?></li>
                            <li><strong>Marque :</strong> <?= htmlspecialchars($annonce['marque_nom']) ?></li>
                            <li><strong>Mode de remise :</strong> <?= htmlspecialchars($annonce['mode_remise']) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de contact -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contacter le vendeur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Vendeur :</strong> <?= htmlspecialchars($annonce['pseudo']) ?></p>
                    <?php if (!$annonce['masquer_telephone'] && $annonce['telephone']): ?>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($annonce['telephone']) ?></p>
                    <?php endif; ?>
                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="message" class="form-label">Votre message</label>
                            <textarea class="form-control" id="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
            .catch(error => console.error('Erreur:', error));
        }

        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Ici, ajoutez la logique pour envoyer le message
            alert('Fonctionnalité en développement');
        });

        function contacterVendeur(annonceId, destinataireId) {
            const message = document.getElementById('message').value.trim();
            if (!message) return;
                
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
                    window.location.href = 'messages.php';
                } else {
                    alert('Erreur lors de l\'envoi du message');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }
    </script>
</body>
</html>