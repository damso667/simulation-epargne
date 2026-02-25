<?php
session_start();
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connecte.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

$stmt = $connec->prepare("SELECT * FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->execute([$id_utilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Last simulation
$stmt2 = $connec->prepare("SELECT * FROM similitude WHERE id_utilisateurs = ? ORDER BY id_simulation DESC LIMIT 1");
$stmt2->execute([$id_utilisateur]);
$simulation = $stmt2->fetch(PDO::FETCH_ASSOC);

// Get final amount if simulation exists
$montant_final = 0;
if ($simulation) {
    $stmtFinal = $connec->prepare("SELECT montant_cumuler FROM details_simulation WHERE id_simulation = ? ORDER BY mois DESC LIMIT 1");
    $stmtFinal->execute([$simulation['id_simulation']]);
    $finalRow = $stmtFinal->fetch(PDO::FETCH_ASSOC);
    if ($finalRow) {
        $montant_final = $finalRow['montant_cumuler'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte — QALF</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="dashboard.css">
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

    <div class="dashboard-wrapper">
        <!-- Header Bar -->
        <div class="dashboard-header glass-card">
            <a href="index.html" class="dash-logo">
                <img src="image_maquette/logo.png" alt="QALF">
            </a>
            <div class="dash-user">
                <span class="dash-greeting">
                    <i class="fas fa-hand-sparkles"></i>
                    Bienvenue, <strong><?= htmlspecialchars($utilisateur['nom']) ?></strong>
                </span>
                <a href="deconnexion.php" class="btn btn-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="dashboard-grid">
            <div class="profile-section glass-card fade-in">
                <div class="profile-header">
                    <?php if (!empty($simulation['photo'])): ?>
                        <?php
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mime = $finfo->buffer($simulation['photo']);
                        $imgData = base64_encode($simulation['photo']);
                        ?>
                        <div class="profile-avatar">
                            <img src="data:<?= $mime ?>;base64,<?= $imgData ?>" alt="Photo de profil">
                        </div>
                    <?php else: ?>
                        <div class="profile-avatar placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <h2><?= htmlspecialchars($utilisateur['nom']) ?> <?= htmlspecialchars($utilisateur['prenom'] ?? '') ?></h2>
                    <p class="profile-email"><i class="fas fa-envelope"></i> <?= htmlspecialchars($utilisateur['email']) ?></p>
                </div>
            </div>

            <!-- Simulation Summary -->
            <?php if ($simulation): ?>
                <div class="simulation-section glass-card fade-in">
                    <h3 class="section-title-sm">
                        <i class="fas fa-chart-line"></i> Dernière simulation
                    </h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-coins"></i></div>
                            <div class="stat-info">
                                <span class="stat-label">Montant initial</span>
                                <span class="stat-value"><?= number_format($simulation['montant_initial'], 0, ',', ' ') ?> FCFA</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon green"><i class="fas fa-piggy-bank"></i></div>
                            <div class="stat-info">
                                <span class="stat-label">Montant final</span>
                                <span class="stat-value highlight"><?= number_format($montant_final, 2, ',', ' ') ?> FCFA</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
                            <div class="stat-info">
                                <span class="stat-label">Durée</span>
                                <span class="stat-value"><?= $simulation['dureee_moi'] ?> mois</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon gold"><i class="fas fa-percentage"></i></div>
                            <div class="stat-info">
                                <span class="stat-label">Taux d'intérêt</span>
                                <span class="stat-value"><?= $simulation['taux_interet'] ?>%</span>
                            </div>
                        </div>
                    </div>
                    <a href="recap.php?id=<?= $id_utilisateur ?>" class="btn btn-accent btn-full" style="margin-top: var(--space-lg);">
                        <i class="fas fa-eye"></i> Voir le détail complet
                    </a>
                </div>
            <?php else: ?>
                <div class="simulation-section glass-card fade-in">
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h3>Aucune simulation</h3>
                        <p>Vous n'avez pas encore effectué de simulation d'épargne.</p>
                        <a href="inscrit2.php?id=<?= $id_utilisateur ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer une simulation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => document.getElementById('pageLoader').classList.add('hidden'), 500);
        });

        // Scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    </script>
</body>
</html>