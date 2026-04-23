<?php
declare(strict_types=1);
session_start();
require_once 'database.php';
require_once 'flash.php';

$_SESSION = [];
flash_set('success', "Vous avez été déconnecté avec succès.");
header("Location: index.php");
exit();