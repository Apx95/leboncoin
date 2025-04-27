<!-- filepath: c:\xampp\htdocs\leboncoin\navbarCo.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroBazar - Navbar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            overflow-x: hidden; /* Empêche le débordement horizontal */
        }
        .navbar {
            background-color: #FFFFFF;
            border-bottom: 1px solid #E0E0E0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 25px;
        }
        

        /* Barre de recherche */
        .navbar .form-control {
            border: none;
            border-radius: 50px;
            background-color: #F8F9FA;
            padding: 10px 20px;
            font-size: 1rem;
            color: #333;
            transition: all 0.3s ease-in-out;
            width: 250px;
        }
        .navbar .form-control:focus {
            width: 400px;
            background-color: #FFFFFF;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
            outline: none;
        }
        .navbar .form-control::placeholder {
            color: #999;
        }
        .navbar .btn-search {
            background: none;
            border: none;
            color: #007BFF;
            font-size: 1.2rem;
        }

        /* Logo ElectroBazar */
        .navbar-brand {
            font-family: 'Quicksand', sans-serif;
            font-size: 2rem;
            font-weight: 600;
            color: #007BFF;
            text-decoration: none;
            letter-spacing: -1px;
            text-transform: uppercase;
            
        }
    
        .navbar-brand span {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(45deg, #007BFF, #00C6FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Bouton "Déposer une annonce" */
        .btn-deposer {
            background-color: #007BFF;
            color: #FFFFFF;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out;
        }
        .btn-deposer i {
            font-size: 1.2rem;
        }
        .btn-deposer:hover {
            background-color: #0056b3;
            text-decoration: none;
        }

        /* Liens de navigation */
        .navbar-nav .nav-item {
            text-align: center;
            margin: 0 10px;
        }
        .navbar-nav .nav-link {
            color: #333;
            font-size: 0.9rem;
            position: relative;
        }
        .navbar-nav .nav-link i {
            font-size: 2rem;
            font-weight: bold;
            color: #007BFF;
        }
        .navbar-nav .nav-link span {
            display: block;
            font-size: 0.8rem;
            font-weight: normal;
        }
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -5px;
            width: 0;
            height: 3px;
            background-color: #007BFF;
            transition: width 0.3s ease-in-out, left 0.3s ease-in-out;
        }
        .navbar-nav .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        /* Menu tiroir "Profil" */
        .drawer {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background-color: #FFFFFF;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease-in-out;
            z-index: 1050;
            padding: 20px;
        }
        .drawer.open {
            right: 0;
        }
        .drawer-header {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .drawer a {
            display: flex;
            align-items: center;
            padding: 10px 0;
            color: #333;
            text-decoration: none;
            font-size: 1.2rem;
        }
        .drawer a i {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #007BFF;
        }
        .drawer a:hover {
            color: #007BFF;
        }
        .drawer-close {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="accueil.php">
                <i class="bi bi-cpu me-2 text-primary"></i>
                <span>ElectroBazar</span>
            </a>

            <!-- Bouton "Déposer une annonce" -->
            <a href="Depot_annonce.php" class="btn-deposer ms-3">
                <i class="bi bi-plus-circle"></i>Déposer une annonce
            </a>

            <!-- Barre de recherche -->
            <form class="d-flex mx-auto" action="search.php" method="GET">
                <input class="form-control me-2" type="search" name="keyword" placeholder="Rechercher sur ElectroBazar" aria-label="Search">
                <button class="btn-search" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <!-- Liens de navigation -->
            <ul class="navbar-nav ms-auto">
                
                <!-- Remplacer la section des catégories par -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-grid"></i>
                        <span>Catégories</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">High-Tech</h6></li>
                        <li><a class="dropdown-item" href="category.php?id=1">Smartphones et Téléphonie</a></li>
                        <li><a class="dropdown-item" href="category.php?id=2">Ordinateurs et Informatique</a></li>
                        <li><a class="dropdown-item" href="category.php?id=3">TV, Son & Home Cinéma</a></li>
                        <li><a class="dropdown-item" href="category.php?id=4">Photo & Caméras</a></li>
                        <li><a class="dropdown-item" href="category.php?id=5">Jeux Vidéo & Consoles</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Électroménager</h6></li>
                        <li><a class="dropdown-item" href="category.php?id=7">Gros Électroménager</a></li>
                        <li><a class="dropdown-item" href="category.php?id=8">Petit Électroménager</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Autres</h6></li>
                        <li><a class="dropdown-item" href="category.php?id=12">Objets Connectés</a></li>
                        <li><a class="dropdown-item" href="category.php?id=17">Autres Appareils</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="favoris.php">
                        <i class="bi bi-heart"></i>
                        <span>Favoris</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="messages.php">
                        <i class="bi bi-envelope"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mes_annonces.php">
                        <i class="bi bi-archive"></i>
                        <span>Mes Annonces</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="profileButton">
                        <i class="bi bi-person"></i>
                        <span>Profil</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Menu tiroir "Profil" -->
    <div class="drawer" id="profileDrawer">
        <span class="drawer-close" id="closeDrawer">&times;</span>
        <div class="drawer-header">Mon Profil</div>
        <a href="profile_info.php"><i class="bi bi-person"></i>Afficher le profil</a>
        <a href="edit_profile.php"><i class="bi bi-pencil-square"></i>Modifier le profil</a>
        <a href="my_ads.php"><i class="bi bi-card-list"></i>Mes annonces</a>
        <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i>Déconnexion</a>
    </div>

    <script>
        // Gestion du menu tiroir "Profil"
        document.addEventListener("DOMContentLoaded", function () {
            const profileButton = document.getElementById("profileButton");
            const profileDrawer = document.getElementById("profileDrawer");
            const closeDrawer = document.getElementById("closeDrawer");

            profileButton.addEventListener("click", function (e) {
                e.preventDefault();
                profileDrawer.classList.add("open");
            });

            closeDrawer.addEventListener("click", function () {
                profileDrawer.classList.remove("open");
            });

            // Fermer le tiroir si on clique en dehors
            document.addEventListener("click", function (e) {
                if (!profileDrawer.contains(e.target) && e.target !== profileButton) {
                    profileDrawer.classList.remove("open");
                }
            });
        });
    </script>
</body>
</html>