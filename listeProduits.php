<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des employés</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --danger-color: #dc2626;
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            padding: 2rem 1rem;
            line-height: 1.5;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: var(--surface-color);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.025em;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--primary-color);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .search-box {
            margin-bottom: 1.5rem;
            max-width: 400px;
        }

        .search-box label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.375rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            outline: none;
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .table-container {
            overflow-x: auto;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.925rem;
        }

        thead {
            background-color: #f1f5f9;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        th, td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }
    </style>
</head>
<body>

    <main class="container">
        <header>
            <h1>Liste des employés</h1>
            <a href="ajouterProduit.html" >Ajouter produit</a>
        </header>

        <div class="search-box">
            <label for="recherche">Rechercher un employé</label>
            <input type="text" id="recherche" onkeyup="filtrerEmployes()" placeholder="Filtrer par nom ou prénom...">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableau-employes">
                    <!-- Les lignes seront injectées par le Fetch API JSON -->
                </tbody>
            </table>
        </div>
    </main>

    <script>
    let tousLesEmployes = [];

    // Récupération des données depuis l'API JSON (Méthode GET)
    function chargerEmployes() {
        fetch("controleapi.php")
            .then(function(response) {
                if (!response.ok) {
                    throw new Error("Erreur réseau : " + response.status);
                }
                return response.json(); // Analyse de la réponse en JSON
            })
            .then(function(employes) {
                tousLesEmployes = employes;
                afficherEmployes(tousLesEmployes);
            })
            .catch(function(error) {
                console.error("Erreur lors de la récupération :", error);
            });
    }

    // Affichage dynamique des objets JSON dans le DOM
    function afficherEmployes(liste) {
        const tbody = document.getElementById("tableau-employes");
        tbody.innerHTML = "";

        if (liste.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color: var(--text-muted);">Aucun employé trouvé</td></tr>`;
            return;
        }

        liste.forEach(function(emp) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td><strong>#${emp.id}</strong></td>
                <td>${emp.designation}</td>
                <td>${emp.categorie}</td>
                <td>${emp.prix}</td>
                <td>${emp.quatoite}</td>
                <td>${emp.dateCreation}</td>
                <td>emp.image</td>
                <td>
                    <a href='ajouterProduit' >Modifier</a>
                    <a href='' >Suppremer</a>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Filtrage côté client
    function filtrerEmployes() {
        const motCle = document.getElementById("recherche").value.toLowerCase();
        const filtre = tousLesEmployes.filter(function(emp) {
            return emp.nom.toLowerCase().includes(motCle) || 
                   emp.prenom.toLowerCase().includes(motCle) || 
                   emp.email.toLowerCase().includes(motCle);
        });
        afficherEmployes(filtre);
    }

    // Suppression via API (Méthode DELETE)
    function supprimerEmploye(id) {
        if (!confirm("Voulez-vous vraiment supprimer cet employé ?")) return;

        fetch("api.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            alert(data.message || "Opération effectuée");
            chargerEmployes(); // Recharger la liste après suppression
        })
        .catch(function(error) {
            console.error("Erreur de suppression :", error);
        });
    }

    document.addEventListener("DOMContentLoaded", chargerEmployes);
    </script>

</body>
</html>