<?php
session_start();
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

$error = '';
if (isset($_POST['connexion'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $connec->prepare("SELECT * FROM utilisateurs WHERE nom = ? AND email = ?");
    $stmt->execute([$nom, $email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur) {
        // Support both hashed and plain text passwords for migration
        $passwordMatch = false;
        if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $passwordMatch = true;
        } elseif ($mot_de_passe === $utilisateur['mot_de_passe']) {
            $passwordMatch = true;
            // Upgrade to hashed password
            $hashed = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $update = $connec->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id_utilisateurs = ?");
            $update->execute([$hashed, $utilisateur['id_utilisateurs']]);
        }

        if ($passwordMatch) {
            $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateurs'];
            header("Location: compte.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Utilisateur non trouvé.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — QALF</title>
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
                <h1><i class="fas fa-sign-in-alt"></i> Connexion</h1>
                <p>Accédez à votre espace personnel</p>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="" method="POST" id="loginForm" autocomplete="off">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nom</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="nom" class="form-input" placeholder="Entrez votre nom" required
                           value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-input" placeholder="Entrez votre email" required
                           value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="mot_de_passe" class="form-input" placeholder="Entrez votre mot de passe" required id="passwordField">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <button type="submit" name="connexion" class="btn btn-primary btn-full" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <!-- Footer -->
            <div class="auth-footer">
                <p>Pas encore de compte ? <a href="inscrit.php">Inscrivez-vous</a></p>
                <a href="index.html" class="back-link"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>

    <script>
        // Loader
        window.addEventListener('load', () => {
            setTimeout(() => document.getElementById('pageLoader').classList.add('hidden'), 500);
        });

        // Toggle password visibility
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

        // Form submit loading
        document.getElementById('loginForm').addEventListener('submit', function(e) {
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