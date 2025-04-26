<?php
session_start();

// Si déjà connecté, rediriger vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: accueil.php');
    exit();
}

// Inclure la configuration de la base de données
require_once 'db.php';

$error_message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // Rechercher l'utilisateur dans la table utilisateurs
        $stmt = $db->prepare("SELECT utilisateur_id, mot_de_passe, pseudo FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error_message = "Aucun compte n'existe avec cet email.";
        } else if (!password_verify($password, $user['mot_de_passe'])) {
            $error_message = "Mot de passe incorrect.";
        } else {
            // Connexion réussie
            $_SESSION['user_id'] = $user['utilisateur_id'];
            $_SESSION['pseudo'] = $user['pseudo'];
            
            // Mise à jour de la dernière connexion
            $updateStmt = $db->prepare("UPDATE utilisateurs SET derniere_connexion = CURRENT_TIMESTAMP WHERE utilisateur_id = ?");
            $updateStmt->execute([$user['utilisateur_id']]);
            
            // Si "Se souvenir de moi" est coché
            if (isset($_POST['rememberMe'])) {
                setcookie('remember_user', $user['utilisateur_id'], time() + (30 * 24 * 60 * 60), '/');
            }

            header('Location: accueil.php');
            exit();
        }
    } catch(PDOException $e) {
        $error_message = "Une erreur est survenue. Veuillez réessayer plus tard.";
        error_log("Erreur de connexion : " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ElectroBazar</title>
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
            background:white
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: relative;
            z-index: 10;
        }

        .back-btn {
            font-size: 1.5rem;
            color: var(--primary-color);
            transition: all 0.3s ease;
            position: absolute;
            left: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            text-decoration: none;
        }

        .back-btn:hover {
            transform: translateX(-5px);
            color: var(--hover-color);
        }

        .navbar-brand {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-align: center;
            width: 100%;
        }

        .main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            gap: 4rem;
            padding: 2rem;
        }

        .form-section {
            width: 500px;
            padding: 3rem;
            background: transparent;
            border-radius: 20px;
            animation: slideInLeft 0.8s ease-out;
        }

        .form-control {
            padding: 1.2rem 1.5rem;
            font-size: 1.2rem;
            border: 2px solid #eee;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
            transform: translateY(-2px);
        }

        .password-container {
            position: relative;
            border-radius: 15px;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .btn-primary {
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 12px;
            background: var(--primary-color);
            border: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.2);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            perspective: 1000px;
            height: 500px; /* Hauteur augmentée pour mieux voir l'animation */
            position: relative;
            overflow: hidden;
        }

        .category-card {
            position: relative;
            aspect-ratio: 7/11;
            border-radius: 15px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scrollAnimation 8s linear infinite;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            opacity: 0;
            width: 100%;
            max-width: 250px;
            margin: 0 auto;
            transform-origin: center center;
        }

        @keyframes scrollAnimation {
            0% {
                opacity: 0;
                transform: translateY(100%);
            }
            10%, {
                opacity: 1;
                transform: translateY(50%);
            }
            90% {
                opacity: 1;
                transform: translateY(-50%);
            }
            100% {
                opacity: 0;
                transform: translateY(-100%);
            }
        }

        /* Délais pour l'effet de défilement continu */
        .category-card:nth-child(1) { animation-delay: 0s; }
        .category-card:nth-child(2) { animation-delay: 3s; }
        .category-card:nth-child(3) { animation-delay: 6s; }
        .category-card:nth-child(4) { animation-delay: 9s; }
        .category-card:nth-child(5) { animation-delay: 12s; }
        .category-card:nth-child(6) { animation-delay: 15s; }

        .category-card img {
            width: 60%;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
            margin: auto;
        }

        /* Ajustement responsive */
        @media (max-width: 992px) {
            .categories-grid {
                height: 400px;
                gap: 1rem;
            }

            .category-card {
                max-width: 200px;
            }
        }
        

        .heart-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: white;
            font-size: 1.2rem;
            opacity: 0;
            animation: fadeIn 0.3s ease forwards 0.5s;
        }

        /* Gradients pastel */
        .bg-pastel-red { background: linear-gradient(135deg, #FFE2E2, #FFD1D1); }
        .bg-pastel-yellow { background: linear-gradient(135deg, #FFF4D9, #FFE5B4); }
        .bg-pastel-blue { background: linear-gradient(135deg, #E2F0FF, #D1E8FF); }

        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
            }
            
            .categories-grid {
                width: 100%;
                grid-template-columns: repeat(2, 1fr);
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
            <h2 class="text-center mb-4">Connexion</h2>
            <?php if ($error_message): ?>
                <div class="alert alert-danger text-center animate__animated animate__shakeX">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <!-- ... Garder le reste du formulaire existant ... -->
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" 
                           placeholder="Adresse e-mail" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <div class="password-container">
                        <input type="password" class="form-control" name="password" 
                               id="password" placeholder="Mot de passe" required>
                        <i class="bi bi-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                    <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>
                <div class="text-center">
                    <a href="forgot_password.php" class="text-decoration-none">Mot de passe oublié ?</a>
                </div>
                <div class="text-center">
                    <p>Pas encore de compte ? <a href="register.php" class="text-decoration-none">S'inscrire</a></p>
                </div>
            </form>
        </div>

        <div class="categories-grid">
            <div class="category-card bg-pastel-red">
                <i class="bi bi-heart heart-icon"></i>
                <img src="assets/images/phone.svg" alt="Téléphone">
            </div>
            <div class="category-card bg-pastel-yellow">
                <i class="bi bi-heart heart-icon"></i>
                <img src="assets/images/laptop.svg" alt="Ordinateur">
            </div>
            <div class="category-card bg-pastel-blue">
                <i class="bi bi-heart heart-icon"></i>
                <img src="assets/images/tv.svg" alt="Télévision">
            </div>
            <div class="category-card bg-pastel-red">
                <i class="bi bi-heart heart-icon"></i>
                <img src="assets/images/headphones.svg" alt="Casque Audio">
            </div>
            <div class="category-card bg-pastel-yellow">
                <i class="bi bi-heart heart-icon"></i>
                <img src="assets/images/tablet.svg" alt="Tablette">
            </div>
            <div class="category-card bg-pastel-blue">
                <i class="bi bi-heart heart-icon"></i>
                <img src="assets/images/keyboard.svg" alt="Clavier">
            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });

            document.querySelectorAll('.heart-icon').forEach(heart => {
                heart.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.classList.toggle('text-danger');
                });
            });
        });
    </script>
</body>
</html>