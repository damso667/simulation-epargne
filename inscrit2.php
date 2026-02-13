<?php
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

if (isset($_POST['valider'])) {
    try {


        $id_utilisateur = $_POST['id_utilisateur'];
        $montant = $_POST['montant_initial'];
        $mois = $_POST['nombre_mois'];
        $taux = $_POST['taux_interet'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $image = file_get_contents($_FILES["photo"]["tmp_name"]);
        } else {
            echo  "photo n'a pas bien ete charger ";
        }

        // Enregistrer la simulation
        $stmt = $connec->prepare("INSERT INTO similitude (id_utilisateurs, montant_initial, dureee_moi, taux_interet, photo)
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_utilisateur, $montant, $mois, $taux, $image]);

        $id_simulation = $connec->lastInsertId();

        //  Enregistrer les calculs dans detail_simulation
        $montant_courant = $montant;
        for ($i = 1; $i <= $mois; $i++) {
            $interet = ($montant_courant * $taux) / 100 / 12;
            $montant_courant += $interet;

            $stmtDetail = $connec->prepare("INSERT INTO details_simulation (id_simulation, mois, montant_cumuler)
                                        VALUES (?, ?, ?)");
            $stmtDetail->execute([$id_simulation, $i, $montant_courant]);
        }

        // Redirige vers recap
        header("Location: recap.php?id=$id_utilisateur");
        exit();
    } catch (Exception $e) {
        echo "errers: " . $e->getMessage();
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="inscrit2.css">
</head>

<body>


    <?php
    $id_utilisateur =  isset($_GET['id']) ? $_GET['id'] : null;
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <section class="box">
            <div class="bloc">
                <h1>S'INSCRIRE</h1>
                <input type="hidden" name="id_utilisateur" value="<?= $id_utilisateur ?>">
                <label for="montant initial">MONTANT INITIAL:</label>
                <input type="text" placeholder="entrer vote budjet *fcfa" name="montant_initial" autocomplete="off"><br><br>
                <label for="prenom">NOMBRE DE MOIS :</label>
                <input type="number" placeholder="entrer le nombre de mois " name="nombre_mois" autocomplete="off"><br><br>
                <label for="taux interet">TAUX D'INTERET:</label>
                <select id="" class="select" name="taux_interet" autocomplete="off">
                    <option value="5%" name="taux_interet">5</option>
                    <option value="10%" name="taux_interet">10</option>
                    <option value="15%" name="taux_interet">15</option>
                    <option value="20%" name="taux_interet">20</option>

                </select>
                <label for="photos" class="custom">parcourir</label>
                <input type="file" name="photo" id="photos">
                <style>
                    input[type="file"] {
                        display: none;

                    }

                    .custom {
                        display: inline-bloc;
                        padding: 10px 20px;
                        cursor: pointer;
                        background-color: #cfe2ff;
                        color: #fff;
                        border-radius: 5px;
                        font-weight: bold;
                        border-radius: 12px;

                    }

                    .custom:hover {
                        background-color: rgb(43, 214, 206);
                    }
                </style>
                <button><a href="inscrit.php" class="btn2">RETOUR</a></button>
                <button type="submit" name="valider">VALIDER</button>

            </div>
        </section>
    </form>

</body>

</html>