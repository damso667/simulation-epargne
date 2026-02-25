<?php
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

$error = '';
if (isset($_POST['valider'])) {
    try {
        $id_utilisateur = $_POST['id_utilisateur'];
        $montant = floatval($_POST['montant_initial']);
        $mois = intval($_POST['nombre_mois']);
        $taux = floatval($_POST['taux_interet']);
        
        $image = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            // Validate file type
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['photo']['tmp_name']);
            if (in_array($mime, $allowed)) {
                $image = file_get_contents($_FILES["photo"]["tmp_name"]);
            } else {
                $error = "Format d'image non supporté. Utilisez JPG, PNG ou GIF.";
            }
        }

        if (!$error) {
            if ($montant <= 0 || $mois <= 0 || $taux <= 0) {
                $error = "Veuillez entrer des valeurs positives.";
            }
        }

        if (!$error) {
            // Save simulation
            $stmt = $connec->prepare("INSERT INTO similitude (id_utilisateurs, montant_initial, dureee_moi, taux_interet, photo)
                                      VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_utilisateur, $montant, $mois, $taux, $image]);

            $id_simulation = $connec->lastInsertId();

            // Calculate monthly details
            $montant_courant = $montant;
            for ($i = 1; $i <= $mois; $i++) {
                $interet = ($montant_courant * $taux) / 100 / 12;
                $montant_courant += $interet;

                $stmtDetail = $connec->prepare("INSERT INTO details_simulation (id_simulation, mois, montant_cumuler)
                                                VALUES (?, ?, ?)");
                $stmtDetail->execute([$id_simulation, $i, $montant_courant]);
            }

            header("Location: recap.php?id=$id_utilisateur");
            exit();
        }
    } catch (Exception $e) {
        $error = "Une erreur est survenue. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation — QALF</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <!-- Loader -->
    <div class="loader-overlay" id="pageLoader">
        <div class="loader-spinner">
            <div class="spinner-ring"></div>
            <span class="loader-text">QALF</span>
        </div>
    </div>

    <?php
    $id_utilisateur = isset($_GET['id']) ? $_GET['id'] : null;
    ?>

    <div class="auth-wrapper">
        <div class="auth-card glass-card">
            <!-- Header -->
            <div class="auth-header">
                <a href="index.html" class="auth-logo">
                    <img src="image_maquette/logo.png" alt="QALF">
                </a>
                <h1><i class="fas fa-chart-line"></i> Simulation</h1>
                <p>Configurez votre simulation d'épargne</p>
            </div>

            <!-- Steps -->
            <div class="steps-indicator">
                <div class="step completed">
                    <span class="step-number"><i class="fas fa-check" style="font-size:10px"></i></span>
                    <span>Profil</span>
                </div>
                <div class="step-line active"></div>
                <div class="step active">
                    <span class="step-number">2</span>
                    <span>Simulation</span>
                </div>
            </div>

            <!-- Error -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="" method="POST" enctype="multipart/form-data" id="simulationForm">
                <input type="hidden" name="id_utilisateur" value="<?= htmlspecialchars($id_utilisateur) ?>">

                <div class="form-group">
                    <label><i class="fas fa-coins"></i> Montant initial (FCFA)</label>
                    <i class="fas fa-coins input-icon"></i>
                    <input type="number" name="montant_initial" class="form-input" placeholder="Ex: 100000" required min="1" step="any">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Nombre de mois</label>
                    <i class="fas fa-calendar-alt input-icon"></i>
                    <input type="number" name="nombre_mois" class="form-input" placeholder="Ex: 12" required min="1" max="120">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-percentage"></i> Taux d'intérêt (%)</label>
                    <i class="fas fa-percentage input-icon"></i>
                    <select name="taux_interet" class="form-select" required>
                        <option value="">Sélectionnez un taux</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                        <option value="15">15%</option>
                        <option value="20">20%</option>
                    </select>
                </div>

                <!-- Photo Upload -->
                <div class="photo-upload">
                    <label style="display:block; font-size: var(--font-size-sm); font-weight: 500; color: var(--text-secondary); margin-bottom: var(--space-sm);">
                        <i class="fas fa-camera" style="color: var(--accent-blue); margin-right: var(--space-sm);"></i> Photo de profil
                    </label>
                    <div class="photo-upload-area" onclick="document.getElementById('photoInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Cliquez pour <strong>parcourir</strong></span>
                        <img id="photoPreview" class="photo-preview" alt="Preview">
                    </div>
                    <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none">
                </div>

                <div class="btn-row">
                    <a href="inscrit.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" name="valider" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-check"></i> Valider
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => document.getElementById('pageLoader').classList.add('hidden'), 500);
        });

        // Photo preview
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('photoPreview');
                    preview.src = event.target.result;
                    preview.classList.add('visible');
                    // Hide upload icon/text
                    const area = document.querySelector('.photo-upload-area');
                    area.querySelector('i').style.display = 'none';
                    area.querySelector('span').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submit loading
        document.getElementById('simulationForm').addEventListener('submit', function(e) {
            const montant = this.querySelector('[name="montant_initial"]');
            const mois = this.querySelector('[name="nombre_mois"]');
            const taux = this.querySelector('[name="taux_interet"]');
            
            let valid = true;
            [montant, mois, taux].forEach(f => {
                if (!f.value || f.value <= 0) {
                    f.style.borderColor = 'var(--accent-red)';
                    valid = false;
                } else {
                    f.style.borderColor = '';
                }
            });

            if (!valid) {
                e.preventDefault();
                return;
            }
            document.getElementById('submitBtn').classList.add('btn-loading');
        });
    </script>
</body>
</html>