<!-- filepath: c:\xampp\htdocs\leboncoin\index.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroBazar - Accueil</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ajoutez un fichier CSS pour le style -->
</head>
<body>
    <!-- En-tête avec logo et barre de navigation -->
<header>
    <div class="logo-container">
        <img src="logo.png" alt="Logo ElectroBazar" class="logo">
        <h1>ElectroBazar</h1>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="connexion.php">Connexion</a></li>
            <li><a href="inscription.php">Inscription</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

    <!-- Barre de recherche -->
    <section class="search-bar">
        <form action="search.php" method="GET">
            <input type="text" name="keyword" placeholder="Rechercher un produit..." required>
            <input type="text" name="location" placeholder="Emplacement">
            <input type="number" name="min_price" placeholder="Prix min">
            <input type="number" name="max_price" placeholder="Prix max">
            <select name="sort_by">
                <option value="date">Date de publication</option>
                <option value="price_asc">Prix croissant</option>
                <option value="price_desc">Prix décroissant</option>
            </select>
            <button type="submit">Rechercher</button>
        </form>
    </section>

    <main>
        <!-- Section latérale avec catégories et filtres -->
        <aside class="sidebar">
            <h2>Catégories</h2>
            <ul>
                <li><a href="category.php?id=1">Électronique</a></li>
                <li><a href="category.php?id=2">Accessoires</a></li>
                <li><a href="category.php?id=3">Pièces détachées</a></li>
            </ul>
            <h2>Filtres</h2>
            <form action="filter.php" method="GET">
                <label>
                    <input type="checkbox" name="verified_seller"> Vendeur vérifié
                </label><br>
                <label>
                    <input type="checkbox" name="accessories"> Accessoires
                </label><br>
                <label>
                    <input type="checkbox" name="spare_parts"> Pièces détachées
                </label><br>
                <button type="submit">Appliquer les filtres</button>
            </form>
        </aside>

        <!-- Liste des produits -->
        <section class="product-list">
            <h2>Produits en vente</h2>
            <div class="product">
                <img src="images/product1.jpg" alt="Produit 1">
                <h3>Produit 1</h3>
                <p>Prix : 150 €</p>
                <p>Vendeur : John Doe</p>
                <button>Contacter</button>
            </div>
            <div class="product">
                <img src="images/product2.jpg" alt="Produit 2">
                <h3>Produit 2</h3>
                <p>Prix : 300 €</p>
                <p>Vendeur : Jane Smith</p>
                <button>Contacter</button>
            </div>
            <div class="product">
                <img src="images/product3.jpg" alt="Produit 3">
                <h3>Produit 3</h3>
                <p>Prix : 200 €</p>
                <p>Vendeur : Alice Johnson</p>
                <button>Contacter</button>
            </div>
        </section>
    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 ElectroBazar. Tous droits réservés.</p>
    </footer>
</body>
</html> 