<?php
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

if (isset($_POST['envoyer'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        $stmt = $connec->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $mot_de_passe]);

        $id_utilisateur = $connec->lastInsertId();
        header("Location: inscrit2.php?id=$id_utilisateur");
        exit();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inscription</title>
    <link rel="stylesheet" href="inscrit.css">
</head>

<body>
    <section class="box">
        <div class="bloc">
            <form action="inscrit.php" method="POST">
                <h1>S'INSCRIRE</h1>
                <label for="nom">NOM:</label>
                <input type="text" placeholder="entrer votre nom" name="nom" autocomplete="off" required><br><br>
                <label for="prenom">PRENOM:</label>
                <input type="text" placeholder="entrer votre prenom" name="prenom" autocomplete="off" required><br><br>
                <label for="email">EMAIL:</label>
                <input type="text" placeholder="entrer votre email" name="email" autocomplete="off" required><br><br>
                <label for="mot de passe">MOT DE PASSE:</label>
                <input type="password" placeholder="entrer votre mot de passe" name="mot_de_passe" autocomplete="new-password" required><br><br>
                <button name=""class="btn2"> <a href="index.html" class="btn2">RETOUR</a></button>
                <button type="submit" name="envoyer" >SUIVANT</button>
                <style>
                    .pointer {
                        cursor: pointer;
                    } 
                    .btn2{
                        color: black !important;
                    }
                </style>
            </form>
        </div>
    </section>
    <script src="inscrit.js"></script>
</body>

</html>