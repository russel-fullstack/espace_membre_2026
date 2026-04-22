<?php
declare(strict_types=1);
session_start();


include 'header.php';
?>

<div class="card card-full">
    <?php if (isset($_SESSION['id'])): ?>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="margin: 0;">Bon retour, <?= htmlspecialchars($_SESSION['pseudo']) ?> !</h2>
                <p style="color: #64748b; margin: 0.5rem 0 0 0;">Vous êtes connecté à votre espace personnel.</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="profil.php?id=<?= $_SESSION['id'] ?>" class="btn btn-primary">Mon Profil</a>
                <a href="deconnexion.php" class="btn btn-outline" style="border-color: #cbd5e1; color: #64748b;">Déconnecter</a>
            </div>
        </div>
    <?php else: ?>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="margin: 0; font-size: 1.4rem;">Espace Membre — PHP 8.4</h1>
                <p style="color: #64748b; margin: 0.5rem 0 0 0;">Modernisation d'un espace membre en PHP procédural moderne.</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="connexion.php" class="btn btn-primary">Connexion</a>
                <a href="inscription.php" class="btn btn-outline">Inscription</a>
            </div>
        </div>
    <?php endif; ?>

   
</div>

<?php include 'footer.php'; ?>
