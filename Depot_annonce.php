<?php
session_start();
require "db.php";

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Ajouter validation des fichiers
function validateFile($file) {
    $maxSize = 5 * 1024 * 1024; // 5MB
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    
    if ($file['size'] > $maxSize) {
        return false;
    }
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    return true;
}

$brouillon = null;
if (isset($_GET['edit_brouillon']) && isset($_SESSION['brouillon_data'])) {
    $brouillon = $_SESSION['brouillon_data'];
    unset($_SESSION['brouillon_data']); // Nettoyer la session
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Données reçues : ' . print_r($_POST, true));
    error_log('Fichiers reçus : ' . print_r($_FILES, true));
}
// Récupération des catégories
try {
    $stmt = $db->query("SELECT * FROM categories WHERE categorie_parent_id IS NULL ORDER BY nom");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($categories)) {
        throw new Exception('Aucune catégorie principale trouvée');
    }
} catch(PDOException $e) {
    error_log("Erreur SQL: " . $e->getMessage());
    die("Une erreur est survenue lors du chargement des catégories.");
}

// Constantes
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_TYPES', ['image/jpeg', 'image/png']);
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Traitement du formulaire - AJOUTER ICI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validation des données requises
        if (empty($_POST['titre']) || empty($_POST['description']) || empty($_POST['categorie']) 
            || empty($_POST['prix']) || empty($_POST['etat']) || empty($_POST['localisation'])) {
            throw new Exception('Tous les champs obligatoires doivent être remplis');
        }

        // Validation et nettoyage des données
        $titre = trim($_POST['titre']);
        $description = trim($_POST['description']);
        $prix = floatval($_POST['prix']);
        $categorie_id = intval($_POST['categorie']);
        $marque_id = !empty($_POST['marque']) && $_POST['marque'] !== 'autre' ? intval($_POST['marque']) : null;
        $etat = $_POST['etat'];
        $prix_negociable = isset($_POST['prix_negociable']) ? 1 : 0;
        $mode_remise = $_POST['mode_remise'];
        $localisation = trim($_POST['localisation']);
        $masquer_telephone = isset($_POST['masquer_telephone']) ? 1 : 0;
        
        // Vérification de l'existence des photos
        if (!isset($_FILES['photos']) || empty($_FILES['photos']['name'][0])) {
            throw new Exception('Au moins une photo est requise');
        }

        // Insertion de l'annonce
        $stmt = $db->prepare("INSERT INTO annonces (
            utilisateur_id, categorie_id, marque_id, titre, description,
            etat, prix, prix_negociable, mode_remise, localisation,
            masquer_telephone, statut, date_creation
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");

        $stmt->execute([
            $_SESSION['user_id'],
            $categorie_id,
            $marque_id,
            $titre,
            $description,
            $etat,
            $prix,
            $prix_negociable,
            $mode_remise,
            $localisation,
            $masquer_telephone
        ]);

        $annonce_id = $db->lastInsertId();

        // Créer le dossier uploads s'il n'existe pas
        if (!file_exists(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }

        // Gestion des photos
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                $fileInfo = pathinfo($_FILES['photos']['name'][$key]);
                $filename = uniqid() . '_' . time() . '.' . $fileInfo['extension'];
                
                if (move_uploaded_file($tmp_name, UPLOAD_DIR . $filename)) {
                    $stmt = $db->prepare("INSERT INTO images (annonce_id, url, ordre) VALUES (?, ?, ?)");
                    $stmt->execute([$annonce_id, $filename, $key]);
                }
            }
        }

        // Redirection vers la page de l'annonce
        header("Location: publication.php?id=" . $annonce_id);
        exit();

    } catch(Exception $e) {
        error_log($e->getMessage());
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une annonce - ElectroBazar</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Navbar styles */
        .navbar {
            background-color: #FFFFFF;
            border-bottom: 1px solid #E0E0E0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            border-radius: 25px;
        }

        .navbar-brand {
            font-family: 'Quicksand', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #007BFF;
            text-decoration: none;
            letter-spacing: -1px;
            text-transform: uppercase;
            margin-left: -200px;
        }
        
        .navbar-brand span {
            background: linear-gradient(45deg, #007BFF, #00C6FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Form container styles */
        .container-fluid {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Buttons styles */
        .large-btn {
            padding: 0.8rem 3rem;
            font-size: 1.2rem;
            border-radius: 25px;
            font-weight: 600;
            margin-left: -100px;
        }

        .btn-primary {
            background-color: #007BFF;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Categories styles */
        .categories-suggestions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .category-card {
            display: block;
            text-align: center;
            padding: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }

        .category-card:hover,
        .category-card.active {
            border-color: #007bff;
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .category-card i {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 0.5rem;
        }

        /* Steps styles */
        .step {
            display: none;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .step.active {
            display: block;
        }

        .progress-bar {
            transition: width 0.3s ease;
            background-color: #007bff;
            height: 4px;
        }

        /* Photo upload styles */
        .photo-slot {
            aspect-ratio: 1;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 1rem;
        }

        .photo-slot:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .photo-slot.has-image {
            background-size: cover;
            background-position: center;
            border: none;
        }

        /* Navigation buttons styles */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a class="navbar-brand d-flex align-items-center" href="">
                    <i class="bi bi-cpu me-2 text-primary"></i>
                    <span>ElectroBazar</span>
                </a>
                <span class="h4 text-primary mb-0 ms-3">Je dépose mon annonce</span>
            </div>
            <a href="accueil.php" class="btn btn-primary large-btn">Quitter</a>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mt-4">
        <div class="progress mb-4">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>

        <form id="annonce-form" action="Depot_annonce.php" method="POST" enctype="multipart/form-data">
            <!-- Step 1: Category -->
            <section class="step active" data-step="1">
                <h3 class="mb-4">Choisissez une catégorie</h3>
                <div class="categories-suggestions mb-4">
                    <?php
                    $categories_icons = [
                        'Ordinateurs & Informatique' => 'laptop',
                        'Smartphones & Tablettes' => 'phone',
                        'TV & Home Cinéma' => 'tv',
                        'Audio & Son' => 'music-note-list',
                        'Gaming & Consoles' => 'controller',
                        'Photo & Vidéo' => 'camera',
                        'Gros Électroménager' => 'houses',
                        'Petit Électroménager' => 'cup-hot',
                        'Domotique & Objets Connectés' => 'smartwatch',
                        'Composants & Pièces' => 'tools'
                    ];
                    
                    foreach($categories as $index => $cat): 
                        $icon = $categories_icons[$cat['nom']] ?? 'grid';
                    ?>
                        <a href="#" class="category-card" data-category="<?= $cat['categorie_id'] ?>">
                            <i class="bi bi-<?= $icon ?>"></i>
                            <span><?= htmlspecialchars($cat['nom']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="categorie" id="categorie-input" required>
                
                <div class="mb-4" id="marque-container" style="display: none;">
                    <h4>Sélectionnez la marque</h4>
                    <div class="form-group">
                        <select name="marque" class="form-select" required>
                            <option value="">Choisir une marque</option>
                        </select>
                        <div id="autre-marque" style="display: none;" class="mt-3">
                            <input type="text" name="nouvelle_marque" class="form-control" 
                                   placeholder="Précisez la marque"
                                   pattern="[A-Za-z0-9\s-]{2,50}"
                                   title="2 à 50 caractères alphanumériques">
                            <small class="form-text text-muted">
                                La nouvelle marque sera ajoutée dans la catégorie Accessoires
                            </small>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 2: Photos -->
            <section class="step" data-step="2">
                <h3 class="mb-4">Photos du produit</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="photo-slot main-photo">
                            <i class="bi bi-camera-fill"></i>
                            <span>Photo principale*</span>
                            <small>Face avant du produit</small>
                            <input type="file" name="photos[]" accept="image/*" class="d-none" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="photo-slot">
                            <i class="bi bi-camera"></i>
                            <span>Face arrière</span>
                            <input type="file" name="photos[]" accept="image/*" class="d-none">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="photo-slot">
                            <i class="bi bi-camera"></i>
                            <span>Vue détaillée</span>
                            <input type="file" name="photos[]" accept="image/*" class="d-none">
                        </div>
                    </div>
                </div>
                <small class="text-muted mt-3 d-block">Format accepté : JPG, PNG - Max 5Mo par photo</small>
            </section>

            <!-- Step 3: Description -->
            <section class="step" data-step="3">
                <h3 class="mb-4">Description du produit</h3>
                <div class="mb-4">
                    <label class="form-label">Titre de l'annonce*</label>
                    <input type="text" name="titre" class="form-control" maxlength="50" required
                           value="<?= htmlspecialchars($brouillon['titre'] ?? '') ?>">
                    <div class="char-counter">0/50</div>
                </div>
                <div class="mb-4">
                    <label class="form-label">État du produit*</label>
                    <select name="etat" class="form-select" required>
                        <option value="">Sélectionnez l'état</option>
                        <option value="neuf" <?= ($brouillon['etat'] ?? '') === 'neuf' ? 'selected' : '' ?>>Neuf</option>
                        <option value="tres-bon" <?= ($brouillon['etat'] ?? '') === 'tres-bon' ? 'selected' : '' ?>>Très bon état</option>
                        <option value="bon" <?= ($brouillon['etat'] ?? '') === 'bon' ? 'selected' : '' ?>>Bon état</option>
                        <option value="satisfaisant" <?= ($brouillon['etat'] ?? '') === 'satisfaisant' ? 'selected' : '' ?>>État satisfaisant</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Description détaillée*</label>
                    <textarea name="description" class="form-control" rows="6" required><?= htmlspecialchars($brouillon['description'] ?? '') ?></textarea>
                </div>
            </section>

            <!-- Remplacer la section Step 4 existante par -->
            <section class="step" data-step="4">
                <h3 class="mb-4">Prix et disponibilité</h3>
                <div class="mb-4">
                    <label class="form-label">Prix*</label>
                    <div class="input-group">
                        <input type="number" name="prix" class="form-control" required min="0"
                               value="<?= htmlspecialchars($brouillon['prix'] ?? '') ?>">
                        <span class="input-group-text">€</span>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="prix_negociable" name="prix_negociable"
                               <?= ($brouillon['prix_negociable'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="prix_negociable">Prix négociable</label>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mode de remise*</label>
                    <select name="mode_remise" class="form-select" required>
                        <option value="main-propre" <?= ($brouillon['mode_remise'] ?? '') === 'main-propre' ? 'selected' : '' ?>>Remise en main propre</option>
                        <option value="envoi" <?= ($brouillon['mode_remise'] ?? '') === 'envoi' ? 'selected' : '' ?>>Envoi postal</option>
                        <option value="les-deux" <?= ($brouillon['mode_remise'] ?? '') === 'les-deux' ? 'selected' : '' ?>>Les deux possibles</option>
                    </select>
                </div>
            </section>

            <!-- Remplacer la section Step 5 existante par -->
            <section class="step" data-step="5">
                <h3 class="mb-4">Localisation</h3>
                <div class="mb-4">
                    <label class="form-label">Ville ou code postal*</label>
                    <input type="text" name="localisation" class="form-control" required
                           value="<?= htmlspecialchars($brouillon['localisation'] ?? '') ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Contact</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="masquer_telephone" name="masquer_telephone"
                               <?= ($brouillon['masquer_telephone'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="masquer_telephone">
                            Masquer mon numéro de téléphone
                        </label>
                    </div>
                </div>
            </section>

            <!-- Navigation buttons -->
            <div class="form-navigation">
                <button type="button" class="btn btn-secondary" id="prev-btn" disabled>Précédent</button>
                <button type="button" class="btn btn-primary" id="next-btn">Suivant</button>
                <button type="submit" class="btn btn-success" id="submit-btn" style="display: none;">
                    Publier l'annonce
                    
                </button>
            </div>
        </form>
    </div>
    <!-- Modal de confirmation -->
    <div class="modal fade" id="quitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quitter la création d'annonce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Que souhaitez-vous faire de votre annonce ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continuer l'édition</button>
                    <button type="button" class="btn btn-primary" id="saveDraft">Enregistrer le brouillon</button>
                    <button type="button" class="btn btn-danger" id="discardDraft">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales
            const form = document.getElementById('annonce-form');
            const steps = document.querySelectorAll('.step');
            const progressBar = document.querySelector('.progress-bar');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');
            const categoryCards = document.querySelectorAll('.category-card');
            const categorieInput = document.getElementById('categorie-input');
            const marqueContainer = document.getElementById('marque-container');
            const marqueSelect = document.querySelector('select[name="marque"]');
            let currentStep = 0;
            let formModified = false;

            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Vérifier la validation de tous les champs
                    if (!validateAllSteps()) {
                        showError('Veuillez remplir tous les champs obligatoires');
                        return;
                    }
                    
                    // Désactiver le bouton et montrer le chargement
                    const submitBtn = document.getElementById('submit-btn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Publication en cours...';
                    
                    try {
                        const formData = new FormData(this);
                        
                        // Envoi du formulaire
                        const response = await fetch('traitement_ads.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Redirection vers la page de l'annonce
                            window.location.href = `publication.php?id=${data.annonce_id}`;
                        } else {
                            throw new Error(data.message || 'Erreur lors de la publication');
                        }
                        
                    } catch (error) {
                        console.error('Erreur:', error);
                        showError(error.message || 'Une erreur est survenue lors de la publication');
                    } finally {
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Publier l\'annonce';
                    }
                });
            }
            
            // Fonction d'affichage des erreurs
            function showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                errorDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                form.prepend(errorDiv);
                
                // Scroll vers le message d'erreur
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Auto-suppression après 5 secondes
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            }
            
            // Validation de toutes les étapes
            function validateAllSteps() {
                let isValid = true;
                steps.forEach((step, index) => {
                    const requiredFields = step.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        if (!field.value) {
                            isValid = false;
                            field.classList.add('is-invalid');
                            if (!field.nextElementSibling?.classList.contains('invalid-feedback')) {
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = 'Ce champ est requis';
                                field.after(feedback);
                            }
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                });
                return isValid;
            }

            // Détection des modifications du formulaire
            form.addEventListener('change', () => {
                formModified = true;
            });
        
            // Gestion du bouton Quitter
            document.querySelector('a[href="accueil.php"]').addEventListener('click', function(e) {
                e.preventDefault();
                if (formModified) {
                    const quitModal = new bootstrap.Modal(document.getElementById('quitModal'));
                    quitModal.show();
                } else {
                    window.location.href = 'accueil.php';
                }
            });
        
            // Gestion du bouton "Enregistrer le brouillon"
            document.getElementById('saveDraft').addEventListener('click', async function() {
                const formData = new FormData(form);
                formData.append('action', 'save_draft');
            
                try {
                    const response = await fetch('brouillon.php', {
                        method: 'POST',
                        body: formData
                    });
                
                    if (!response.ok) throw new Error('Erreur réseau');

                    const result = await response.json();
                    if (result.success) {
                        window.location.href = 'accueil.php?draft=saved';
                    } else {
                        throw new Error(result.error || 'Erreur inconnue');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la sauvegarde du brouillon');
                }
            });
        
            // Gestion du bouton "Supprimer"
            document.getElementById('discardDraft').addEventListener('click', function() {
                window.location.href = 'accueil.php';
            });
        
            // Prevention de la fermeture accidentelle
            window.addEventListener('beforeunload', function(e) {
                if (formModified) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Ajouter la fonction updateStep ici
            function updateStep(stepIndex) {
                steps.forEach((step, index) => {
                    if (index === stepIndex) {
                        step.classList.add('active');
                    } else {
                        step.classList.remove('active');
                    }
                });
            
                // Met à jour les boutons
                prevBtn.disabled = stepIndex === 0;
                if (stepIndex === steps.length - 1) {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'block';
                } else {
                    nextBtn.style.display = 'block';
                    submitBtn.style.display = 'none';
                }
            
                // Met à jour la barre de progression
                const progress = ((stepIndex + 1) / steps.length) * 100;
                progressBar.style.width = `${progress}%`;
            }

            // Gestion des catégories
            categoryCards.forEach(card => {
                card.addEventListener('click', async function(e) {
                    e.preventDefault();

                    // Reset des sélections précédentes
                    categoryCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    const categoryId = this.dataset.category;
                    const categoryName = this.querySelector('span').textContent;

                    try {
                        // 1. Charger les sous-catégories
                        const subCatResponse = await fetch(`sous_category.php?categorie_id=${categoryId}`);
                        if (!subCatResponse.ok) throw new Error('Erreur réseau');
                        const subCategories = await subCatResponse.json();

                        // 2. Créer le sélecteur de sous-catégories
                        const subCatContainer = document.createElement('div');
                        subCatContainer.className = 'subcategory-selector mt-4';
                        subCatContainer.innerHTML = `
                            <h4>Type de ${categoryName}</h4>
                            <select class="form-select mb-3" id="subcategory-select" required>
                                <option value="">Sélectionnez un type</option>
                                ${subCategories.map(sub => `
                                    <option value="${sub.categorie_id}">${sub.nom}</option>
                                `).join('')}
                            </select>
                        `;

                        // 3. Remplacer ou ajouter le sélecteur
                        const existingSubCat = document.querySelector('.subcategory-selector');
                        if (existingSubCat) {
                            existingSubCat.replaceWith(subCatContainer);
                        } else {
                            marqueContainer.before(subCatContainer);
                        }

                        // Masquer le conteneur de marques jusqu'à la sélection d'une sous-catégorie
                        marqueContainer.style.display = 'none';

                       // 4. Gérer le changement de sous-catégorie
                        const subcategorySelect = subCatContainer.querySelector('#subcategory-select');
                        subcategorySelect.addEventListener('change', async function() {
                            const subCatId = this.value;
                            if (!subCatId) {
                                marqueContainer.style.display = 'none';
                                return;
                            }

                            categorieInput.value = subCatId;

                            try {
                                // Charger les marques correspondantes
                                const marqueResponse = await fetch(`get_marques.php?categorie_id=${subCatId}`);
                                if (!marqueResponse.ok) throw new Error('Erreur réseau');

                                const marques = await marqueResponse.json();

                                // Mettre à jour le select des marques
                                marqueSelect.innerHTML = '<option value="">Choisir une marque</option>';
                                marques.forEach(marque => {
                                    const option = document.createElement('option');
                                    option.value = marque.id;
                                    option.textContent = marque.nom;
                                    marqueSelect.appendChild(option);
                                });

                                // Ajouter l'option "Autre marque"
                                const autreOption = document.createElement('option');
                                autreOption.value = 'autre';
                                autreOption.textContent = 'Autre marque';
                                marqueSelect.appendChild(autreOption);

                                // Afficher le conteneur des marques
                                marqueContainer.style.display = 'block';

                            } catch (error) {
                                console.error('Erreur:', error);
                                alert('Erreur lors du chargement des marques');
                            }
                        });

                    } catch (error) {
                        console.error('Erreur:', error);
                        alert('Erreur lors du chargement des sous-catégories');
                    }
                });
            });

            // Photo upload
            const photoSlots = document.querySelectorAll('.photo-slot');
            photoSlots.forEach(slot => {
                slot.addEventListener('click', () => {
                    slot.querySelector('input[type="file"]').click();
                });

                const input = slot.querySelector('input[type="file"]');
                input?.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            slot.style.backgroundImage = `url(${e.target.result})`;
                            slot.classList.add('has-image');
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            });

            // Navigation buttons
            nextBtn.addEventListener('click', () => {
                if (validateCurrentStep() && currentStep < steps.length - 1) {
                    currentStep++;
                    updateStep(currentStep);
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentStep > 0) {
                    currentStep--;
                    updateStep(currentStep);
                }
            });

            // Form validation
            function validateCurrentStep() {
                const currentSection = steps[currentStep];
                const requiredFields = currentSection.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                return isValid;
            }
        });
        // Ajouter dans le DOMContentLoaded, avant la fermeture
        let formModified = false;


    </script>
</body>
</html>