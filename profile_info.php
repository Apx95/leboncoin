<!-- filepath: c:\xampp\htdocs\leboncoin\profile_info.php -->
<?php
require("db.php");
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$sql = "SELECT pseudo, email, telephone, localisation, date_creation FROM utilisateurs WHERE utilisateur_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur non trouvé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - ElectroBazar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #F4F6F8;
            font-family: 'Roboto', sans-serif;
        }
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #FFFFFF;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .profile-container h3 {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .profile-container .info {
            margin-bottom: 15px;
        }
        .profile-container .info span {
            font-weight: bold;
        }
        .btn-back {
            background-color: #007BFF;
            color: #FFFFFF;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h3>Informations du profil</h3>
        <div class="info">
            <span>Pseudo :</span> <?php echo htmlspecialchars($user['pseudo']); ?>
        </div>
        <div class="info">
            <span>Email :</span> <?php echo htmlspecialchars($user['email']); ?>
        </div>
        <div class="info">
            <span>Téléphone :</span> <?php echo htmlspecialchars($user['telephone']); ?>
        </div>
        <div class="info">
            <span>Localisation :</span> <?php echo htmlspecialchars($user['localisation']); ?>
        </div>
        <div class="info">
            <span>Date d'inscription :</span> <?php echo htmlspecialchars($user['date_creation']); ?>
        </div>
        <a href="acceuil.php" class="btn-back"><i class="bi bi-arrow-left"></i> Retour à l'accueil</a>
    </div>
</body>
</html>