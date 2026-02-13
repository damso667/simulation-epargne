<?php
session_start();
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

if (isset($_POST['connexion'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $connec->prepare("SELECT * FROM utilisateurs WHERE nom = ? AND email = ?");
    $stmt->execute([$nom, $email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur) {
        if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateurs'];
            header("Location: compte.php");
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Utilisateur non trouvé.";
    }
}
?>$

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="bootstrap.min.css"> -->
    <link rel="stylesheet" href="connecter.css">
</head>
<body>
    <section class="box">
        <div class="bloc">
            <form action="" method="POST" autocomplete="off">
                <h1>LOGIN</h1>
                <label for="nom" >NOM:</label>
                <input type="text" placeholder="entrer votre nom" name="nom" autocomplete="off"><br><br>
                <label for="email" >EMAIL:</label>
                <input type="text" placeholder="entrer votre email" name="email" autocomplete="off"><br><br>
                <label for="mot de passe">MOT DE PASSE:</label>
                <input type="password" placeholder="entrer votre mot de passe" name="mot_de_passe" autocomplete="off"><br><br>
                <button name="connexion" type="submit">SE CONNECTER</button>
            </form>
        </div>
    </section>
</body>
</html>