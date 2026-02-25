<?php
session_start();
require_once 'connexion.php';
$conn = new connecte();
$connec = $conn->conexion();

// Security: check for valid id parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: connecte.php");
    exit();
}

$id_utilisateur = $_GET['id'];

// User info
$stmt = $connec->prepare("SELECT * FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->execute([$id_utilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    header("Location: connecte.php");
    exit();
}

// Last simulation
$stmt2 = $connec->prepare("SELECT * FROM similitude WHERE id_utilisateurs = ? ORDER BY id_simulation DESC LIMIT 1");
$stmt2->execute([$id_utilisateur]);
$simulation = $stmt2->fetch(PDO::FETCH_ASSOC);

if (!$simulation) {
    header("Location: compte.php");
    exit();
}

// Simulation details
$stmt3 = $connec->prepare("SELECT * FROM details_simulation WHERE id_simulation = ? ORDER BY mois ASC");
$stmt3->execute([$simulation['id_simulation']]);
$details = $stmt3->fetchAll(PDO::FETCH_ASSOC);

// Calculate gain
$montant_final = !empty($details) ? end($details)['montant_cumuler'] : $simulation['montant_initial'];
$gain = $montant_final - $simulation['montant_initial'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif — QALF</title>
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

    <div class="recap-wrapper">
        <div class="recap-card glass-card fade-in">
            <!-- Header with photo -->
            <div class="recap-header">
                <?php if (!empty($simulation['photo'])):
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->buffer($simulation['photo']);
                    $imgData = base64_encode($simulation['photo']);
                ?>
                    <div class="recap-avatar">
                        <img src="data:<?= $mime ?>;base64,<?= $imgData ?>" alt="Photo">
                    </div>
                <?php endif; ?>
                <h2><i class="fas fa-file-invoice-dollar"></i> Compte de <span><?= htmlspecialchars($utilisateur['nom']) ?></span></h2>
            </div>

            <!-- Summary Stats -->
            <div class="recap-summary">
                <div class="recap-stat">
                    <i class="fas fa-coins" style="color: var(--accent-blue)"></i>
                    <span class="recap-label">Montant initial</span>
                    <span class="recap-val"><?= number_format($simulation['montant_initial'], 0, ',', ' ') ?> <small>FCFA</small></span>
                </div>
                <div class="recap-stat">
                    <i class="fas fa-calendar-alt" style="color: var(--accent-purple)"></i>
                    <span class="recap-label">Durée</span>
                    <span class="recap-val"><?= $simulation['dureee_moi'] ?> <small>mois</small></span>
                </div>
                <div class="recap-stat">
                    <i class="fas fa-percentage" style="color: var(--accent-gold)"></i>
                    <span class="recap-label">Taux</span>
                    <span class="recap-val"><?= $simulation['taux_interet'] ?><small>%</small></span>
                </div>
            </div>

            <!-- Final Amount -->
            <div class="recap-final">
                <div class="label"><i class="fas fa-trophy"></i> Montant final estimé</div>
                <div class="amount"><?= number_format($montant_final, 2, ',', ' ') ?> <small>FCFA</small></div>
                <div style="color: var(--accent-green); margin-top: var(--space-sm); font-size: var(--font-size-sm);">
                    <i class="fas fa-arrow-up"></i> +<?= number_format($gain, 2, ',', ' ') ?> FCFA de gain
                </div>
            </div>

            <!-- Monthly Details Table -->
            <h3 class="section-title-sm"><i class="fas fa-table"></i> Détail par mois</h3>
            <div class="detail-table-wrapper">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> Mois</th>
                            <th><i class="fas fa-wallet"></i> Montant cumulé (FCFA)</th>
                            <th><i class="fas fa-arrow-trend-up"></i> Intérêt gagné</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $prev = $simulation['montant_initial'];
                        foreach ($details as $detail): 
                            $interet_mois = $detail['montant_cumuler'] - $prev;
                        ?>
                            <tr>
                                <td>Mois <?= $detail['mois'] ?></td>
                                <td><?= number_format($detail['montant_cumuler'], 2, ',', ' ') ?></td>
                                <td style="color: var(--accent-green);">+<?= number_format($interet_mois, 2, ',', ' ') ?></td>
                            </tr>
                        <?php 
                            $prev = $detail['montant_cumuler'];
                        endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="recap-actions">
                <a href="index.html" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Accueil
                </a>
                <a href="connecte.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </a>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => document.getElementById('pageLoader').classList.add('hidden'), 600);
        });

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