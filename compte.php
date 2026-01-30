<?php
session_start();
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connecter.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

$stmt = $connec->prepare("SELECT * FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->execute([$id_utilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Dernière simulation
$stmt2 = $connec->prepare("SELECT * FROM similitude WHERE id_utilisateurs = ? ORDER BY id_simulation DESC LIMIT 1");
$stmt2->execute([$id_utilisateur]);
$simulation = $stmt2->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Mon Compte</title>
    <link rel="stylesheet" href="recap.css">
</head>

<body>

    <div class="container">
        <h2>Bienvenue <?= htmlspecialchars($utilisateur['nom']) ?></h2>
        <?php

        if (!empty($simulation['photo'])) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($simulation['photo']);
            $imgData = base64_encode($simulation['photo']);
            echo " <img src= 'data:$mime;base64,$imgData' width ='100' height = '100' />";
        } ?>
        <p>Email : <?= htmlspecialchars($utilisateur['email']) ?></p>

        <?php if ($simulation) { ?>

            <h3>Votre dernière simulation :</h3>
            <p>Montant initial : <?= $simulation['montant_initial'] ?> FCFA</p>
            <p>Durée : <?= $simulation['dureee_moi'] ?> mois</p>
            <p>Taux d'intérêt : <?= $simulation['taux_interet'] ?>%</p>
        <?php } else { ?>
            <p>Aucune simulation enregistrée.</p>
        <?php } ?>

        <a href="deconnexion.php">Se déconnecter</a>
    </div>

</body>

</html>