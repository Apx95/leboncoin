<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Validation des données
        $categorie_id = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_NUMBER_INT);
        $marque_id = filter_input(INPUT_POST, 'marque', FILTER_SANITIZE_STRING);
        $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $etat = filter_input(INPUT_POST, 'etat', FILTER_SANITIZE_STRING);
        $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $prix_negociable = isset($_POST['prix_negociable']) ? 1 : 0;
        $localisation = filter_input(INPUT_POST, 'localisation', FILTER_SANITIZE_STRING);
        $masquer_telephone = isset($_POST['masquer_telephone']) ? 1 : 0;

        // Gestion de la nouvelle marque
        if ($marque_id === 'autre' && !empty($_POST['nouvelle_marque'])) {
            $nouvelle_marque = filter_input(INPUT_POST, 'nouvelle_marque', FILTER_SANITIZE_STRING);
            // On l'enregistre dans la catégorie Accessoires (id=6)
            $stmt = $pdo->prepare("INSERT INTO marques (nom, categorie_id) VALUES (?, 6)");
            $stmt->execute([$nouvelle_marque]);
            $marque_id = $pdo->lastInsertId();
        }

        // Début de la transaction
        $pdo->beginTransaction();

        // Insertion de l'annonce
        $stmt = $pdo->prepare("
            INSERT INTO annonces (
                utilisateur_id, categorie_id, marque_id, titre, description,
                etat, prix, prix_negociable, localisation, masquer_telephone, statut, date_creation
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', NOW())
        ");
        $stmt->execute([
            $_SESSION['user_id'], $categorie_id, $marque_id, $titre, $description,
            $etat, $prix, $prix_negociable, $localisation, $masquer_telephone
        ]);

        $annonce_id = $pdo->lastInsertId();

        // Traitement des photos
        if (isset($_FILES['photos'])) {
            $uploadDir = "uploads/annonces/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION);
                    $newFileName = uniqid() . '_' . $annonce_id . '.' . $extension;
                    $filePath = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmp_name, $filePath)) {
                        $stmt = $pdo->prepare("INSERT INTO images (annonce_id, url, ordre) VALUES (?, ?, ?)");
                        $stmt->execute([$annonce_id, $newFileName, $key]);
                    }
                }
            }
        }

        // Validation de la transaction
        $pdo->commit();

        // Redirection vers la page de confirmation
        header("Location: publication.php?id=" . $annonce_id);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la publication : " . $e->getMessage();
        header("Location: Depot_annonce.php");
        exit();
    }
}
?>