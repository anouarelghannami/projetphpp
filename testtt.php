<?php
// Connexion à la base de données (Exemple vulnérable)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mon_projet";

$conn = new mysqli($servername, $username, $password, $dbname);

// 1. FAILLE : Injection SQL (utilisation directe de $_GET sans préparation de requête)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = " . $id;
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "Nom : " . $row["username"] . "<br>";
        }
    }
}

// 2. FAILLE : Cross-Site Scripting (XSS) non filtré
if (isset($_POST['message'])) {
    $message = $_POST['message'];
    // Affichage direct de l'entrée utilisateur sans échappement (htmlspecialchars)
    echo "<div>Message reçu : " . $message . "</div>";
}

// 3. FAILLE : Utilisation de fonctions cryptographiques faibles ou obsolètes (MD5)
function validerMotDePasse($password) {
    $hashAttendu = "098f6bcd4621d373cade4e832627b4f6"; // hash de "test" en MD5
    if (md5($password) === $hashAttendu) {
        return true;
    }
    return false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test de Sécurité PHP</title>
</head>
<body>
    <h2>Formulaire de test XSS</h2>
    <form method="POST" action="">
        <input type="text" name="message" placeholder="Entrez un message...">
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>


