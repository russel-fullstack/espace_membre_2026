<?php
declare(strict_types=1);
session_start();
require 'database.php';
require 'flash.php';

$erreur = "";
$msg = "";

if (!isset($_SESSION['id'])) {
    header('Location: connexion.php');
    exit();
}
include 'header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <ul class="sidebar-nav">
            <li><a href="profil.php?id=<?= $_SESSION['id'] ?>">Tableau de bord</a></li>
            <li><a href="#" class="active">Paramètres du profil</a></li>
            <li><a href="deconnexion.php" style="color: var(--error-red);">Déconnexion</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="card" style="margin: 0; max-width: none;">
            <h2 style="margin-bottom: 2rem;">Édition du profil</h2>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><?= $erreur ?></div>
            <?php endif; ?>

            <?php if ($msg): ?>
                <div class="alert alert-success"><?= $msg ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>Pseudo</label>
                        <input type="text" name="newpseudo" placeholder="Pseudo" value="" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="newmail" placeholder="Mail" value="" />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="newmdp1" placeholder="Laissez vide pour ne pas changer" />
                    </div>
                    <div class="form-group">
                        <label>Confirmation</label>
                        <input type="password" name="newmdp2" placeholder="Confirmez le nouveau mdp" />
                    </div>
                </div>

                <div class="form-group">
                    <label>Avatar</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <?php  ?>
                            <img src="membres/avatars/" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php ?>
                        <input type="file" name="avatar" style="flex: 1;" />
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="profil.php?id=<?= $_SESSION['id'] ?>" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>
<?php include 'footer.php'; ?>