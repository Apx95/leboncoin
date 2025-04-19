<?php
    
    require("db.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $location = $_POST["location"];
   
    

    if(strlen($password) < 8){
        die("Le mot de passe doit contenir au moins 8 caractères");
    }

    // Hacher le mot de passe pour plus de sécurité
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //check id user already exists
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if($user){
        die("Cet utilisateur existe déjà !");
    }


    try {
        // Insérer les données dans la base de données
        $sql = "INSERT INTO users (pseudo, email, phone, password_hash, location) VALUES (:username, :email, :phone, :password, :location)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'pseudo' => $username,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => $hashed_password,
            'location' => $location
        ]);

        echo "Utilisateur enregistré avec succès ! <a href='login.php'>Connectez-vous ici</a>";
    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}

    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>
<body class=" bg-secondary">

    <form action="login.php" class="p-3 rounded shadow container bg-light" method="post">
        
        <h1 class="text-center text-danger">Register</h1>
            <label for="username" class="form-label">Username</label>
            <input required type="text" class="form-control" id="username" name="username">
            <label for="password" class="form-label">Password</label>
            <input required type="password" class="form-control" id="password" name="password">
            <label for="password" class="form-label">Confirm Password</label>
            <input required type="password_verify" class="form-control" id="password" name="password">
            <label for="email" class="form-label">Email</label>
            <input required type="email" class="form-control" id="email" name="email">
            <label for="phone" class="form-label">Phone</label>
            <input type="number" name="phone" id="phone" class="form-control" >
            <label for="location" class="form-label">Location</label>
            <input type="search" class="form-control" name="location" id="location">
            <br>
        
            <input type="submit" value="register" href="" class="mt-3 btn btn-primary">

            <a href="../idx.php" class="mt-3 ms-4 btn btn-outline-primary">Login</a>
    </form>
</body>
</html>