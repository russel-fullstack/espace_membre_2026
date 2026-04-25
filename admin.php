<?php

declare(strict_types=1);
session_start();
require_once 'database.php';
require_once 'flash.php';
require_once 'role.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] !== Role::ADMIN->value) {
    // flash_set('error', "Accès refusé.");
    header('Location: index.php');
    exit();
}

$stmt = $pdo->prepare("SELECT *FROM membres ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();

?><?php include 'header.php'; ?>
<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <main class="main-content">
        <div class="profile-header">
            <div class="user-meta">
                <h2>Dashboard Administrateur</h2>
                <p>Gestion des membres de l'espace.</p>
            </div>
            <div style="margin-left: auto;">
                <span class="btn btn-outline" style="cursor: default; border-color: var(--vue-green); color: var(--vue-green);">
                    Total: <?= count($users) ?> membres
                </span>
            </div>
        </div>

        <div class="card" style="margin: 2rem 0; max-width: none; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 1rem;">Avatar</th>
                        <th style="padding: 1rem;">Pseudo</th>
                        <th style="padding: 1rem;">Email</th>
                        <th style="padding: 1rem;">Rôle</th>
                        <th style="padding: 1rem;">Inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td style="padding: 1rem;">
                                <?php if ($user['avatar']): ?>
                                    <img src="membres/avatars/<?= htmlspecialchars($user['avatar']) ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #64748b;">
                                        <?= strtoupper(substr($user['pseudo'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; font-weight: 500;"><?= htmlspecialchars($user['pseudo']) ?></td>
                            <td style="padding: 1rem; color: #64748b;"><?= htmlspecialchars($user['mail']) ?></td>
                            <td style="padding: 1rem;">
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: <?= $user['role'] === 'admin' ? '#f0fdf4' : '#f1f5f9' ?>; color: <?= $user['role'] === 'admin' ? '#16a34a' : '#64748b' ?>; border: 1px solid <?= $user['role'] === 'admin' ? '#bbf7d0' : '#e2e8f0' ?>;">
                                    <?= Role::from($user['role'])->label() ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: #94a3b8; font-size: 0.85rem;">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php include 'footer.php'; ?>