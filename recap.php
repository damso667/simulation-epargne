<?php
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

$id_utilisateur = $_GET['id'];

// Infos utilisateur
$stmt = $connec->prepare("SELECT * FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->execute([$id_utilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Dernière simulation
$stmt2 = $connec->prepare("SELECT * FROM similitude WHERE id_utilisateurs = ? ORDER BY id_simulation DESC LIMIT 1");
$stmt2->execute([$id_utilisateur]);
$simulation = $stmt2->fetch(PDO::FETCH_ASSOC);

// Détails simulation
$stmt3 = $connec->prepare("SELECT * FROM details_simulation WHERE id_simulation = ?");
$stmt3->execute([$simulation['id_simulation']]);
$details = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Récapitulatif</title>
    <link rel="stylesheet" href="recap.css">
</head>

<body>

    <div class="container">
        <?php
        if (!empty($simulation['photo'])) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($simulation['photo']);
        $imgData = base64_encode($simulation['photo']);
        echo " <img src='data:$mime;base64,$imgData' width='250' height='150' />";
        }?>
        <h2>Compte de <?= htmlspecialchars($utilisateur['nom']) ?></h2>

        <h3>Simulation</h3>
        <p>Montant initial : <?= $simulation['montant_initial'] ?> FCFA</p>
        <p>Durée : <?= $simulation['dureee_moi'] ?> mois</p>
        <p>Taux d'intérêt : <?= $simulation['taux_interet'] ?>%</p>

        <h3>Détail par mois</h3>
        <ul>
            <?php foreach ($details as $detail) { ?>
                <li>Mois <?= $detail['mois'] ?> : <?= number_format($detail['montant_cumuler'], 2) ?> FCFA</li>
            <?php } ?>
        </ul>

        <a href="index.html">Retour</a>
    </div>

</body>

</html>