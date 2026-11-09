

<?php
 
$host = "localhost";
$dbname = "gestion_produits";
$username = "root";
$password = "anouar12345";
 
try {
 
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
 
    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
 
} catch (PDOException $e) {
 
    http_response_code(500);
 
    echo json_encode([
        "error" => "Erreur de connexion à la base de données"
    ]);
 
    exit;
}