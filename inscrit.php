<?php
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

$error = '';
if (isset($_POST['envoyer'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    // Hash the password
    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    try {
        // Check if email already exists
        $check = $connec->prepare("SELECT id_utilisateurs FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $stmt = $connec->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $hashed_password]);

            $id_utilisateur = $connec->lastInsertId();
            header("Location: inscrit2.php?id=$id_utilisateur");
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
    <title>Inscription — QALF</title>
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

    <div class="auth-wrapper">
        <div class="auth-card glass-card">
            <!-- Header -->
            <div class="auth-header">
                <a href="index.html" class="auth-logo">
                    <img src="image_maquette/logo.png" alt="QALF">
                </a>
                <h1><i class="fas fa-user-plus"></i> Inscription</h1>
                <p>Créez votre compte en quelques secondes</p>
            </div>

            <!-- Steps -->
            <div class="steps-indicator">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span>Profil</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
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
            <form action="inscrit.php" method="POST" id="inscriptionForm">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nom</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="nom" class="form-input" placeholder="Entrez votre nom" required autocomplete="off"
                           value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> Prénom</label>
                    <i class="fas fa-id-card input-icon"></i>
                    <input type="text" name="prenom" class="form-input" placeholder="Entrez votre prénom" required autocomplete="off"
                           value="<?= isset($prenom) ? htmlspecialchars($prenom) : '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-input" placeholder="Entrez votre email" required autocomplete="off"
                           value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="mot_de_passe" class="form-input" placeholder="Créez un mot de passe" required autocomplete="new-password" id="passwordField">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <div class="btn-row">
                    <a href="index.html" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" name="envoyer" class="btn btn-primary" id="submitBtn">
                        Suivant <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="auth-footer">
                <p>Déjà inscrit ? <a href="connecte.php">Connectez-vous</a></p>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => document.getElementById('pageLoader').classList.add('hidden'), 500);
        });

        function togglePassword() {
            const field = document.getElementById('passwordField');
            const icon = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
            const fields = this.querySelectorAll('[required]');
            let valid = true;
            fields.forEach(f => {
                if (!f.value.trim()) {
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