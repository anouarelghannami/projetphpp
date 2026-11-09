<?php

session_start();
include 'connexion.php';

$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

$user = $stmt->fetch();

if ($user && password_verify($password, $user["password"])) {

    // Email + mot de passe corrects
    $_SESSION["user_id"] = $user["id"];

    header("Location:listeProduits.php");
    exit;

} else {

    // Email ou mot de passe incorrect
    header("Location:listeProduits.php?error=Email ou mot de passe incorrect");
    exit;
}
?>