<?php
session_start();
require 'db.php';

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérification de l'authentification et des paramètres
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID du brouillon invalide";
    header('Location: mes_annonces.php');
    exit;
}

try {
    // Récupérer le brouillon avec toutes les informations nécessaires
    $stmt = $db->prepare("
        SELECT b.*, 
               c.nom as categorie_nom,
               m.nom as marque_nom,
               c.categorie_parent_id
        FROM brouillons b
        LEFT JOIN categories c ON b.categorie_id = c.categorie_id
        LEFT JOIN marques m ON b.marque_id = m.id
        WHERE b.brouillon_id = ? 
        AND b.utilisateur_id = ?
        FOR UPDATE
    ");
    
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $brouillon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$brouillon) {
        $_SESSION['error'] = "Brouillon introuvable ou non autorisé";
        header('Location: mes_annonces.php');
        exit;
    }

    // Nettoyer et préparer les données pour le formulaire
    if (!empty($brouillon['images'])) {
        $brouillon['images'] = json_decode($brouillon['images'], true);
    }

    // Stocker les données dans la session
    $_SESSION['brouillon_data'] = $brouillon;
    
    // Rediriger vers le formulaire de dépôt avec le mode édition
    header('Location: Depot_annonce.php?edit_brouillon=' . $_GET['id']);
    exit;

} catch(PDOException $e) {
    // Log l'erreur
    error_log("Erreur edit_brouillon.php: " . $e->getMessage());
    
    // Stocker le message d'erreur dans la session
    $_SESSION['error'] = "Une erreur est survenue lors de la récupération du brouillon";
    
    // Rediriger vers la page des annonces
    header('Location: mes_annonces.php');
    exit;
} catch(Exception $e) {
    error_log("Erreur inattendue: " . $e->getMessage());
    $_SESSION['error'] = "Une erreur inattendue est survenue";
    header('Location: mes_annonces.php');
    exit;
}