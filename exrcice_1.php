<?php 
// 1 faire la connexion a la base de donnee 
$servername = "localhost";
$dbname = "";  //RK si dbname n'est pas encore creer laisse vide
$username = "root";
$password = "";

try{
    $conn = new PDO("mysql:host=$servername",$username,$password);
    // set the PDO error mode to exeption 
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully !<br>";
}catch(PDOException $e) {
   // echo "Connexion failed : " .$e->getMessage() ."<br>";
    exit; // si la connexion est echouee il faut arreter ici
}

// creation de dbname et de table 

try {
    // craetion de dbname 

    $sql = "CREATE DATABASE IF NOT EXISTS gestionStock ";
    $conn->exec($sql);
    //echo "dbname gestionStock Created successfully !<br>";
    $conn->exec("USE gestionStock");
    //echo "Base de donnees selectionnee !<br>";

    // craeation de table 

    $sql1 = "CREATE TABLE IF NOT EXISTS Produit (
    Ref varchar(45) primary key not null,
    designation varchar(50) not null,
    categorie ENUM('Nettoyage', 'Cosmétique', 'Electrique') NOT NULL,
    prix decimal(10,2) not null,
    quantite int not null,
    dateCreation Date not null
    )";

    $conn->exec($sql1);
    // echo "la table est bien creerer dans vottre dbname ! <br><br>";

} catch (PDOException $e) {
    echo "Error lors de la creation de data base :" .$e->getMessage() ."<br>";
}

// traitement de html 
if (isset($_POST["critere"]) && isset($_POST["valeur_recherche"])) {

    $cretere = $_POST["critere"] ;
    $text = $_POST["valeur_recherche"];
    $result = null; // pour eviter les erreures
    try{
    if (empty(($text)) == false){
        $requete = "SELECT * FROM Produit ";
        $result = $conn->query($requete);
    }
    }catch(PDOException $e){
        echo "Eroor : " .$e->getMessage() ."<br>" ;
    }
    
    try{
        if ($cretere == "Ref") {
            try{
                $requete = "SELECT * FROM Produit WHERE $cretere = $text ";
                $result = $conn->query($requete);
            }catch(PDOException $e){
                echo "Error : Ce produit n'existe pas  <br> ". $e->getMessage() ."<br>";
            }
        }elseif($cretere == "quantite") {
            $requete = "SELECT * FROM Produit WHERE $cretere = $text ";
            $result = $conn->query($requete);
            if (!empty($result)){
                    echo "Ce produit n'existe pas !" ;
                }

        }elseif($cretere == "designation") {
            try{
                $requete = "SELECT * FROM Produit WHERE $cretere LIKE '%$text%' ";
                $result = $conn->query($requete);
            }catch(PDOException $e){
                echo "Error Ce produit n'existe pas <br>: " .$e->getMessage() ."<br>" ;
            }
        }elseif($cretere == "prix") {
            try{
                $requete = "SELECT * FROM Produit WHERE $cretere = $text ";
                $result = $conn->query($requete);
                if (!empty($result)){
                    echo "Ce produit n'existe pas !" ;
                }
            }catch(PDOException $e){
                echo "Error ce produit n'existe  pas :<br>:".$e->getMessage()."<br>";
            }
        }elseif($cretere == "dateCreation") {

            $requete = "SELECT * FROM Produit WHERE $cretere = $text ";
            $result = $conn->query($requete);
        }else{
            echo "ce cretere n'existe pas ";
        }
    }catch(PDOException $e){
        echo "Error : " .$e->getMessage() ."<br>" ;
    }

    try{
        // mainteant faire le vrai travail 
        if ($result != null && $result->rowCount() > 0 ){
            echo " <br><br><h1>--- Resultat De Rechrche ---- </h1><br><br>";
            echo "<button class='btn btn-primary' onclick=\"window.location.href='exercice_1.html'\">Ajouter Produit</button> <br><br>";
            echo "<table border=1 cellspacing=0>";
            echo "<thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation</th>
                        <th>Catégorie</th>
                        <th>Prix (MAD)</th>
                        <th>Quantité</th>
                        <th>Date de Création</th>
                    </tr>
                    </thead>";
            echo "<tbody>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                echo "<tr>";
                echo "<td>".$row["Ref"] ."</td>";
                echo "<td>".$row["designation"] ."</td>";
                echo "<td>".$row["categorie"] ."</td>";
                echo "<td>".$row["prix"] ."</td>";
                echo "<td>".$row["quantite"] ."</td>";
                echo "<td>".$row["dateCreation"] ."</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";

        }else{
            "No record found (Aucun produit trouvé).";
        }

    }catch(PDOException $e) {
        echo "Eror : " .$e->getMessage();
    }
}


// traitement de question 4 
try {
    if (isset($_POST["categorie"])) {

        $choix = $_POST["categorie"];

        $sql3 = "SELECT * FROM Produit WHERE categorie = '$choix'";
        $result = $conn->query($sql3);
        
        if ($result->rowCount() > 0) {
            echo " <br><br><h1>--- Resultat De Rechrche ---- </h1><br><br>";
             echo "<button class='btn btn-primary' onclick=\"window.location.href='exercice_1.html'\">BACK</button> <br><br>";
            echo "<table border='1' cellspacing='0'>";
            echo "<thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation</th>
                        <th>Catégorie</th>
                        <th>Prix (MAD)</th>
                        <th>Quantité</th>
                        <th>Date de Création</th>
                    </tr>
                  </thead>";
            echo "<tbody>";
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                echo "<tr>";
                echo "<td>".$row["Ref"] ."</td>";
                echo "<td>".$row["designation"] ."</td>";
                echo "<td>".$row["categorie"] ."</td>";
                echo "<td>".$row["prix"] ."</td>";
                echo "<td>".$row["quantite"] ."</td>";
                echo "<td>".$row["dateCreation"] ."</td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
            
        } else {
            
            echo "No record found (Aucun produit trouvé dans cette catégorie).";
        }
    }
} catch(PDOException $e) {
    echo "Error : " .$e->getMessage();
}
?>