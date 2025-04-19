# Projet de Base de Données Leboncoin

## Aperçu
Ce projet est un schéma de base de données conçu pour une plateforme (electroBazar) inspirée de Leboncoin, qui facilite les transactions entre utilisateurs via des annonces. La base de données inclut la gestion des utilisateurs, la publication d'annonces, la messagerie, la gestion des médias, un système de notation, des fonctionnalités de modération et des capacités de transaction optionnelles.

## Fonctionnalités
- **Gestion des utilisateurs** : Gérer les comptes utilisateurs avec des champs pour l'email, le mot de passe, le téléphone et la localisation.
- **Publication d'annonces** : Les utilisateurs peuvent créer, modifier et supprimer des annonces avec des détails tels que le titre, la description, le prix et la localisation.
- **Recherche avancée** : Une vue qui permet aux utilisateurs d'effectuer des recherches complexes sur les annonces et les catégories.
- **Messagerie** : Les utilisateurs peuvent envoyer et recevoir des messages liés aux annonces.
- **Gestion des médias** : Prise en charge du téléchargement et de la gestion des images associées aux annonces.
- **Système de notation** : Les utilisateurs peuvent noter les annonces et laisser des avis.
- **Modération** : Un système pour signaler les annonces inappropriées et gérer leur statut.
- **Fonctionnalités de transaction** : Fonctionnalité optionnelle pour gérer les transactions entre utilisateurs.

## Structure du projet
```
leboncoin-database-project
├── sql
│   ├── schema
│   │   ├── users.sql
│   │   ├── ads.sql
│   │   ├── messages.sql
│   │   ├── media.sql
│   │   ├── ratings.sql
│   │   ├── moderation.sql
│   │   └── transactions.sql
│   ├── seed
│   │   ├── users_seed.sql
│   │   ├── ads_seed.sql
│   │   └── messages_seed.sql
│   └── views
│       ├── advanced_search_view.sql
│       └── user_activity_view.sql
├── README.md
└── .gitignore
```

## Instructions d'installation
1. Clonez le dépôt sur votre machine locale.
2. Naviguez vers le répertoire `sql`.
3. Exécutez les fichiers SQL dans le répertoire `schema` pour créer les tables nécessaires.
4. Facultativement, exécutez les fichiers de données dans le répertoire `seed` pour remplir les tables avec des données d'exemple.
5. Créez les vues en exécutant les fichiers SQL dans le répertoire `views`.

## Utilisation
- Utilisez la table `users` pour gérer les comptes utilisateurs.
- La table `ads` est utilisée pour stocker les annonces.
- La table `messages` facilite la communication entre les utilisateurs.
- La table `media` gère les images associées aux annonces.
- Les notations et avis peuvent être stockés dans la table `ratings`.
- La table `moderation` est utilisée pour signaler et gérer le contenu inapproprié.
- La table `transactions` peut être utilisée pour gérer les transactions financières si elle est implémentée.

## Contribution
Les contributions sont les bienvenues ! Veuillez soumettre une pull request ou ouvrir une issue pour toute amélioration ou correction de bug.

## Licence
Ce projet est sous licence MIT.