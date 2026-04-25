<?php

declare(strict_types=1);
session_start();
require_once 'database.php';
require_once 'flash.php';

$erreur = null;
$msg = null;

if (!isset($_SESSION['id'])) {
    header('Location: connexion.php');
    exit();
}

/**
 * Utility: Check if a value is already taken
 */
function isTaken(PDO $pdo, string $col, string $val, int $id): bool
{
    $stmt = $pdo->prepare("SELECT id FROM membres WHERE $col = :val AND id != :id");
    $stmt->execute([':val' => $val, ':id' => $id]);
    return (bool)$stmt->fetch();
}

function handlePseudo(PDO $pdo,  string $val, int $id): ?string
{
    if (empty($val)) return null;
    if (strlen($val) > 255) return "Pseudo trop long";
    if (isTaken($pdo, 'pseudo', $val, $id)) return "Pseudo déjà utilisé";

    $stmt = $pdo->prepare("UPDATE membres SET pseudo = :pseudo WHERE id = :id");
    $stmt->execute([':pseudo' => $val, ':id' => $id]);
    $_SESSION['pseudo'] = $val;
    return 'success';
}
function handleEmail(PDO $pdo, int $id, ?string $val, string $current): ?string
{
    if (empty($val) || $val === $current) return null;
    if (!filter_var($val, FILTER_VALIDATE_EMAIL)) return "Email invalide";
    if (isTaken($pdo, 'mail', $val, $id)) return "Email déjà utilisé";

    $stmt = $pdo->prepare("UPDATE membres SET mail = :val WHERE id = :id");
    $stmt->execute([':val' => $val, ':id' => $id]);
    $_SESSION['mail'] = $val;
    return 'success';
}
function handlePassword(PDO $pdo, int $id, string $p1, string $p2): ?string
{
    if (empty($p1)) return null;
    if ($p1 !== $p2) return "Les mots de passe ne correspondent pas";
    if (strlen($p1) < 8) return "Le mot de passe doit faire au moins 8 caractères";


    $stmt = $pdo->prepare("UPDATE membres SET motdepasse = :val WHERE id = :id");
    $stmt->execute([':val' => password_hash($p1, PASSWORD_DEFAULT), ':id' => $id]);
    return 'success';
}
function handleAvatar(PDO $pdo, int $id, array $file): ?string
{
    $file = $_FILES['avatar'];
    if (empty($file['name'])) return null;
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $path = "membres/avatars/";
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) return "Format de fichier non supporté";
    if ($file['size'] > 2 * 1024 * 1024) return "Le fichier est trop volumineux (max 2Mo)";

    if (!is_dir($path)) mkdir($path, 0755, true);
    $filename = "$id.$extension";

    if (move_uploaded_file($file['tmp_name'], $path . $filename)) {
        $pdo->prepare("UPDATE membres SET avatar = :avatar WHERE id = :id")
            ->execute([':avatar' => $filename, ':id' => $id]);
        $_SESSION['avatar'] = $filename;
        return 'success';
    };

    return 'Erreur Upload';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = (int)$_SESSION['id'];
    $modifPseudo = handlePseudo($pdo, $_POST['newpseudo'] ?? '', $userid);
    $modifEmail = handleEmail($pdo, $userid, filter_input(INPUT_POST, 'newmail', FILTER_SANITIZE_EMAIL), $_SESSION['mail'] ?? '');
    $modifPassword = handlePassword($pdo, $userid, $_POST['newmdp1'] ?? '', $_POST['newmdp2'] ?? '');
    $resAvatar = handleAvatar($pdo, $userid, $_FILES['avatar'] ?? []);
    $erreur = match (true) {
        is_string($modifPseudo) && $modifPseudo !== 'success' => $modifPseudo,
        is_string($modifEmail) && $modifEmail !== 'success' => $modifEmail,
        is_string($modifPassword) && $modifPassword !== 'success' => $modifPassword,
        is_string($resAvatar) && $resAvatar !== 'success' => $resAvatar,
        default => null
    };

    if (!$erreur && ($modifPseudo || $modifEmail || $modifPassword) &&  $resAvatar) {
        flash_set('success', 'Profil mis à jour avec succès');
        header("Location: editionprofil.php");
        exit();
    }
    if ($erreur) {
        flash_set('error', $erreur);
        header("Location: editionprofil.php");
        exit();
    }
}
?><?php include 'header.php'; ?>

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
                        <input type="text" name="newpseudo" placeholder="Pseudo" value="<?= isset($_SESSION['pseudo']) ? $_SESSION['pseudo'] : '' ?>" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="newmail" placeholder="Mail" value="<?= isset($_SESSION['mail']) ? $_SESSION['mail'] : '' ?>" />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="newmdp1" placeholder="Laissez vide pour ne pas changer" />
                    </div>
                    <div class="form-group">
                        <label>Confirmation</label>
                        <input type="password" name="newmdp2" placeholder="Confirmez le nouveau mot de passe" />
                    </div>
                </div>

                <div class="form-group">
                    <label>Avatar</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <?php if (!empty($_SESSION['avatar'])): ?>
                            <img src="membres/avatars/<?= $_SESSION['avatar'] ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php endif; ?>
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