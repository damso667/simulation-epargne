<?php
session_start();
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

if (isset($_POST['connexion'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $connec->prepare("SELECT * FROM utilisateurs WHERE nom = ? AND email = ? ");
    $stmt->execute([$nom, $email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    // $stmt = $connec->prepare("SELECT * FROM similitude WHERE photo = ? ");
    // $stmt->execute([$nom, $email]);
    // $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    // var_dump($utilisateur);
    // $hash_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    // echo " mot de passe hashe 1 : $hash_password\n";
    // echo "mot de passe hashe 2 " . password_hash($mot_de_passe, PASSWORD_DEFAULT) . "\n";
    if ($utilisateur) {
        if (($mot_de_passe) == $utilisateur['mot_de_passe']) {
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
?>



<!DOCTYPE html>
<html>

<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="recap.css">
</head>

<body>

    <div class="container">
        <h2>Connexion</h2>
        <form action="" method="POST" id="connecter">
            <label>Nom :</label>
            <input type="text" name="nom" required>

            <label>Email :</label>
            <input type="email" name="email" required>

            <label>Mot de passe :</label>
            <input type="password" name="mot_de_passe" required>

            <button type="submit" name="connexion">Se connecter</button>
            
        </form>
        <style>
            input{
                outline: none;
                border: none;
                opacity: 0.5;

            }
            .container{
                opacity: 0.9;
            }
        </style>
    </div>
    <script>
        document.getElementById("connecter").addEventListener("submit", function(event) {
            let isValid = true;

            // Liste des champs obligatoires (ajoute ici ceux que tu veux vérifier)
            const requiredFields = ["nom", "email", "mot_de_passe"];

            requiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field && field.value.trim() === "") {
                    field.style.border = "2px solid red";
                    isValid = false;
                } else if (field) {
                    field.style.border = "";
                }
            });
            if (!isValid) {
                event.preventDefault();
                alert("Veuillez remplir tous les champs requis.");
            }
        })
    </script>
</body>

</html>