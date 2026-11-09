<?php

require_once 'connexion.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {

    $id = $_GET['id'];

    try {
       
        $sql = "DELETE FROM Produit WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        header("Location: listeProduits.php");
        exit; 
        
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
    
} else {
    // Si quelqu'un essaie d'accéder à la page sans ID (ex: localhost/supprimerProduit.php)
    // On le redirige sagement vers la liste
    header("Location: listeProduits.php");
    exit;
}
?>