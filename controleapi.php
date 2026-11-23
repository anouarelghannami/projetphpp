<?php
 
header("Content-Type: application/json; charset=UTF-8");
 
require_once "connexion.php";
 
$method = $_SERVER["REQUEST_METHOD"];
 
switch ($method) {
 
    // =========================
    // GET
    // =========================
    case "GET":
 
        $stmt = $conn->query(
            "SELECT * FROM produit ORDER BY id DESC"
        );
 
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        echo json_encode($employees);
 

        break;
 
 
    // =========================
    // POST
    // =========================
    case "POST":
 
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );
 
        if (
            empty($data["designation"]) ||
            empty($data["categorie"]) ||
            empty($data["prix"]) ||
            empty($data['quantite']) ||
            empty($data['dateCreation'])
        ) {
 
            http_response_code(400);
 
            echo json_encode([
                "error" => "Tous les champs sont obligatoires"
            ]);
 
            exit;
        }
 
        $sql = "
            INSERT INTO produit
            (designation, categorie, prix ,quantite ,dateCreation)
            VALUES (?, ?, ? ,?,?)
        ";
 
        $stmt = $conn->prepare($sql);
 
        $stmt->execute([
            $data["designation"],
            $data["categorie"],
            $data["prix"] ,
            $data['quantite'] ,
            $data['dateCreation']
        ]);
 
        echo json_encode([
            "message" => "Employé ajouté avec succès",
            "id" => $conn->lastInsertId()
        ]);
 
        break;


    // =========================
    // PUT
    // =========================
    case "PUT":
 
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );
 
        if (empty($data["id"])) {
 
            http_response_code(400);
 
            echo json_encode([
                "error" => "ID obligatoire"
            ]);
 
            exit;
        }
 
        $sql = "
            UPDATE employees
            SET nom = ?,
                prenom = ?,
                email = ?
            WHERE id = ?
        ";
 
        $stmt = $conn->prepare($sql);
 
        $stmt->execute([
            $data["nom"],
            $data["prenom"],
            $data["email"],
            $data["id"]
        ]);
 
        echo json_encode([
            "message" => "Employé modifié avec succès"
        ]);
 
        break;
 
 
    // =========================
    // DELETE
    // =========================
    case "DELETE":
 
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );
 
        if (empty($data["id"])) {
 
            http_response_code(400);
 
            echo json_encode([
                "error" => "ID obligatoire"
            ]);
 
            exit;
        }
 
        $stmt = $conn->prepare(
            "DELETE FROM produit WHERE id = ?"
        );
 
        $stmt->execute([
            $data["id"]
        ]);
 
        echo json_encode([
            "message" => "Employé supprimé avec succès"
        ]);
 
        break;
 
 
    // =========================
    // METHOD NOT ALLOWED
    // =========================
    default:
 
        http_response_code(405);
 
        echo json_encode([
            "error" => "Méthode HTTP non autorisée"
        ]);
 
        break;
}