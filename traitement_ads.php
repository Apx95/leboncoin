<?php
session_start();
require "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

try {
    // Validation des données requises
    $required = ['titre', 'description', 'categorie', 'prix', 'etat', 'localisation'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Le champ $field est requis");
        }
    }

    // Validation des fichiers
    if (!isset($_FILES['photos']) || empty($_FILES['photos']['name'][0])) {
        throw new Exception('Au moins une photo est requise');
    }

    $db->beginTransaction();

    // Insertion de l'annonce
    $stmt = $db->prepare("
        INSERT INTO annonces (
            utilisateur_id, categorie_id, marque_id, titre, description,
            etat, prix, prix_negociable, mode_remise, localisation,
            masquer_telephone, statut, date_creation
        ) VALUES (
            :user_id, :categorie_id, :marque_id, :titre, :description,
            :etat, :prix, :prix_negociable, :mode_remise, :localisation,
            :masquer_telephone, 'active', NOW()
        )
    ");

    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'categorie_id' => $_POST['categorie'],
        'marque_id' => !empty($_POST['marque']) && $_POST['marque'] !== 'autre' ? $_POST['marque'] : null,
        'titre' => trim($_POST['titre']),
        'description' => trim($_POST['description']),
        'etat' => $_POST['etat'],
        'prix' => floatval($_POST['prix']),
        'prix_negociable' => isset($_POST['prix_negociable']) ? 1 : 0,
        'mode_remise' => $_POST['mode_remise'],
        'localisation' => trim($_POST['localisation']),
        'masquer_telephone' => isset($_POST['masquer_telephone']) ? 1 : 0
    ]);

    $annonce_id = $db->lastInsertId();

    // Gestion des photos
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
            $filename = uniqid('img_') . '_' . time() . '.jpg';
            
            if (move_uploaded_file($tmp_name, "uploads/" . $filename)) {
                $stmt = $db->prepare("
                    INSERT INTO images (annonce_id, url, ordre) 
                    VALUES (:annonce_id, :url, :ordre)
                ");
                $stmt->execute([
                    'annonce_id' => $annonce_id,
                    'url' => $filename,
                    'ordre' => $key
                ]);
            }
        }
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'annonce_id' => $annonce_id,
        'message' => 'Annonce publiée avec succès'
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}