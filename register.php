<?php
require("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = trim($_POST["pseudo"]);
    $email = trim($_POST["email"]);
    $telephone = trim($_POST["telephone"]);
    $mot_de_passe = $_POST["mot_de_passe"];
    $confirmation_mot_de_passe = $_POST["confirmation_mot_de_passe"];
    $localisation = trim($_POST["localisation"]);

    if ($mot_de_passe !== $confirmation_mot_de_passe) {
        $error_message = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($mot_de_passe) < 8) {
        $error_message = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        try {
            // Vérification si l'utilisateur existe déjà
            $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ? OR pseudo = ?");
            $stmt->execute([$email, $pseudo]);
            
            if ($stmt->rowCount() > 0) {
                $error_message = "Un utilisateur avec cet email ou ce pseudo existe déjà !";
            } else {
                // Insertion du nouvel utilisateur
                $stmt = $db->prepare("INSERT INTO utilisateurs (pseudo, email, telephone, mot_de_passe, localisation) 
                                    VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$pseudo, $email, $telephone, $hashed_password, $localisation]);

                header("Location: login.php?registered=1");
                exit();
            }
        } catch(PDOException $e) {
            $error_message = "Une erreur est survenue lors de l'inscription.";
            error_log($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007BFF;
            --hover-color: #00C6FF;
            --bg-color: #F4F6F8;
        }

        body {
            background: var(--bg-color);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: transform 0.3s ease;
        }

        .back-btn:hover {
            transform: translateX(-5px);
            color: var(--hover-color);
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .form-section {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            animation: fadeInUp 0.8s ease-out;
        }

        .form-control {
            height: 3.2rem;
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0,123,255,0.1);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 2px solid #eee;
            border-right: none;
            background: white;
        }

        .btn-primary {
            height: 3.2rem;
            border-radius: 10px;
            background: var(--primary-color);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.2);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                <span>Retour</span>
            </a>
            <span class="navbar-brand">
                <i class="bi bi-cpu"></i>
                ElectroBazar
            </span>
        </div>
    </nav>

    <div class="main-container">
        <div class="form-section">
            <h2 class="text-center mb-4">Créer un compte</h2>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center animate__animated animate__shakeX">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" name="pseudo" placeholder="Pseudo" 
                           required value="<?php echo isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : ''; ?>">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Adresse e-mail" 
                           required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="tel" class="form-control" name="telephone" placeholder="Téléphone"
                           pattern="[0-9]{10}" required value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" class="form-control" name="localisation" placeholder="Localisation" 
                           required value="<?php echo isset($_POST['localisation']) ? htmlspecialchars($_POST['localisation']) : ''; ?>">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" name="mot_de_passe" 
                           placeholder="Mot de passe (8 caractères minimum)" required>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-lock-check"></i></span>
                    <input type="password" class="form-control" name="confirmation_mot_de_passe" 
                           placeholder="Confirmer le mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-2"></i>S'inscrire
                </button>
            </form>

            <p class="text-center mt-4">
                Déjà inscrit ? <a href="login.php" class="text-decoration-none">Se connecter</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation des formulaires Bootstrap
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>