<?php 

require_once 'connexion.php';

$produit = null; // Variable pour stocker les infos du produit

//isset($_GET['ref']) vérifie seulement que le "tiroir" existe dans l'URL.
// !empty($_GET['ref']) vérifie que le "tiroir" contient bien quelque chose.

if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $id = $_GET['ref'];

    $sql = "SELECT * FROM Produit WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        echo "Erreur : Ce produit n'existe pas.";
    }
}


// on modifier les valeures dans la base de donnes
if (isset($_POST['id'], $_POST['designation'], $_POST['categorie'], $_POST['prix'], $_POST['quantite'], $_POST['date'])) {
    
    $id_update = $_POST['id']; 
    $designation = $_POST['designation'];
    $categorie = $_POST['categorie'];
    $prix = $_POST['prix'];
    $quantite = $_POST['quantite'];
    $date = $_POST['date'];

    try {
        // UPDATE pour modifier les donnees
        $sqlUpdate = "UPDATE Produit SET designation=?, categorie=?, prix=?, quantite=?, dateCreation=? WHERE id=?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->execute([$designation, $categorie, $prix, $quantite, $date, $id_update]);
        
        // Redirection vers la liste
        header("Location: listeProduits.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modifier un produit</title>
</head>
<body>
    <section>
        <div class="container mt-4">
            <div class="d-flex flex-column justify-content-center">
                
                <h1 class="title">Modification de produit de reference : 
                    <span id="ref"><?php echo htmlspecialchars($produit['id']); ?></span>
                </h1>

                <form action="modifierProduit.php" method="post">
                    
                    <!-- CHAMP TRÈS IMPORTANT : L'ID CACHÉ -->
                    <input type="hidden" name="id" value="<?php echo $produit['id']; ?>">

                    <div class="row mt-4 mb-2">
                        <div class="col-md-6"><label for="des">Designation</label></div>
                        <div class="col-md-6">
                            <!-- On utilise l'attribut value="" pour pré-remplir -->
                            <input type="text" class="form-control" name="designation" id="des" value="<?php echo htmlspecialchars($produit['designation']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6"><label for="Cat">Categorie</label></div>
                        <div class="col-md-6">
                            <select name="categorie" id="Cat" class="form-control">
                                <!-- On ajoute l'attribut 'selected' si la catégorie correspond à celle du produit -->
                                <option value="Nettoyage" <?php if($produit['categorie'] == 'Nettoyage') echo 'selected'; ?>>Nettoyage</option>
                                <option value="Cosmétique" <?php if($produit['categorie'] == 'Cosmétique') echo 'selected'; ?>>Cosmétique</option>
                                <option value="Electrique" <?php if($produit['categorie'] == 'Electrique') echo 'selected'; ?>>Eléctrique</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6"><label for="prix">Prix</label></div>
                        <div class="col-md-6">
                            <input type="number" step="0.01" class="form-control" name="prix" id="prix" value="<?php echo htmlspecialchars($produit['prix']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6"><label for="quant">Quantite</label></div>
                        <div class="col-md-6">
                            <input type="number" class="form-control" name="quantite" id="quant" value="<?php echo htmlspecialchars($produit['quantite']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6"><label for="date">Date de creation</label></div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="date" id="date" value="<?php echo htmlspecialchars($produit['dateCreation']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <input type="submit" class="form-control bg-primary text-white" value="Mettre a jour">
                    </div>
                    
                </form>
                
            </div>
        </div>
    </section>
</body>
</html>