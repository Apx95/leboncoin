<?php
session_start();
require "db.php";

header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Non autorisé']));
}

// Récupération et validation des données
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['brouillon_id']) || !is_numeric($data['brouillon_id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'ID du brouillon invalide ou manquant']));
}

try {
    $db->beginTransaction();

    // Vérifier que le brouillon appartient à l'utilisateur
    $stmt = $db->prepare("
        SELECT brouillon_id, images 
        FROM brouillons 
        WHERE brouillon_id = ? 
        AND utilisateur_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$data['brouillon_id'], $_SESSION['user_id']]);
    $brouillon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$brouillon) {
        $db->rollBack();
        http_response_code(404);
        die(json_encode(['error' => 'Brouillon non trouvé ou non autorisé']));
    }

    // Suppression des images physiques
    if (!empty($brouillon['images'])) {
        $images = json_decode($brouillon['images'], true);
        if (is_array($images)) {
            foreach ($images as $image) {
                $filepath = __DIR__ . '/uploads/brouillons/' . $image;
                if (file_exists($filepath)) {
                    if (!unlink($filepath)) {
                        error_log('Impossible de supprimer le fichier: ' . $filepath);
                        // Continuer même si l'image ne peut pas être supprimée
                    }
                }
            }
        }
    }

    // Suppression du brouillon dans la base de données
    $stmt = $db->prepare("
        DELETE FROM brouillons 
        WHERE brouillon_id = ? 
        AND utilisateur_id = ?
    ");
    $stmt->execute([$data['brouillon_id'], $_SESSION['user_id']]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Erreur lors de la suppression du brouillon');
    }

    $db->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Brouillon supprimé avec succès'
    ]);

} catch (PDOException $e) {
    $db->rollBack();
    error_log('Erreur SQL: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la suppression',
        'message' => 'Une erreur est survenue lors de la suppression du brouillon'
    ]);
} catch (Exception $e) {
    $db->rollBack();
    error_log('Erreur: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la suppression',
        'message' => $e->getMessage()
    ]);
}