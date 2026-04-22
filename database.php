<?php
$dsn      = 'mysql:host=localhost;dbname=espace_membres_2026;charset=utf8mb4';
$utilisateur = 'valet';
$motdepasse  = 'valet';

$options = [
    // Lancer des exceptions en cas d'erreur
    PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
    // Retourner des tableaux associatifs par défaut
    PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
    // Ne pas émuler les requêtes préparées
    PDO::ATTR_EMULATE_PREPARES    => false,
];

try {
    $pdo = new PDO($dsn, $utilisateur, $motdepasse, $options);
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int) $e->getCode());
}