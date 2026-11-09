<?php
include 'connexion.php';

// Récupération de la recherche si elle existe
$recherche = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    // Requête préparée avec filtre LIKE sur la désignation ou la catégorie
    $sql = "SELECT * FROM Produit WHERE designation LIKE :q OR categorie LIKE :q";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['q' => '%' . $recherche . '%']);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($produits) > 0) {
        foreach ($produits as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["designation"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["categorie"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["prix"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["quantite"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["dateCreation"]) . "</td>";
            echo "<td>
                    <a href='modifierProduit.php?ref=" . $row["id"] . "'>Modifier</a> &nbsp;&nbsp;&nbsp;
                    <a href='supprimerProduit.php?id=" . $row["id"] . "' onclick=\"return confirm('Êtes-vous sûr ?');\">Supprimer</a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7' style='text-align:center;'>Aucun produit trouvé !</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7'>Erreur : " . $e->getMessage() . "</td></tr>";
}
?>