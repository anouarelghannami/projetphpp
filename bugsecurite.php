<?php
// Démarrage de la session pour la gestion des utilisateurs
session_start();

// --- INITIALISATION D'UNE BASE DE DONNÉES SQLite (en mémoire) ---
try {
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création d'une table utilisateurs et insertion d'un admin
    $db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
    $db->exec("INSERT INTO users (username, password) VALUES ('admin', 'secret123')");

    // Création d'une table commentaires
    $db->exec("CREATE TABLE comments (id INTEGER PRIMARY KEY, author TEXT, message TEXT)");
    $db->exec("INSERT INTO comments (author, message) VALUES ('Alice', 'Bienvenue sur le site !')");
} catch (Exception $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// --- GESTION DE LA CONNEXION (VULNERABLE vs SECURISE) ---
$loginError = "";
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // -----------------------------------------------------------------
    // ⚠️ EXEMPLE VULNÉRABLE (Injection SQL) :
    // Concaténation directe des variables dans la requête SQL
    // Test d'injection dans le champ username : admin' -- 
    // -----------------------------------------------------------------
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $stmt = $db->query($sql);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /* 
    // 🔒 EXEMPLE SÉCURISÉ (À décommenter pour corriger la faille) :
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    */

    if ($user) {
        $_SESSION['user'] = $user['username'];
    } else {
        $loginError = "Identifiants incorrects.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- GESTION DES COMMENTAIRES ---
if (isset($_POST['comment'])) {
    $author = $_POST['author'];
    $message = $_POST['message'];

    // ⚠️ VULNÉRABLE : Insertion directe sans échappement (Risque XSS)
    // 🔒 SECURISÉ : Utiliser htmlspecialchars() lors de l'affichage ou du stockage
    $stmt = $db->prepare("INSERT INTO comments (author, message) VALUES (?, ?)");
    $stmt->execute([$author, $message]);
    
    header("Location: index.php");
    exit;
}

// Récupération des commentaires
$comments = $db->query("SELECT * FROM comments")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projet Vulnérable Pédagogique (All-in-One)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f9; }
        .box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .danger { border-left: 5px solid #ff4d4d; }
        input, textarea { display: block; margin-bottom: 10px; padding: 8px; width: 100%; max-width: 400px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>

    <h1>Laboratoire de Sécurité Web (Single-File)</h1>

    <!-- SECTION CONNEXION -->
    <div class="box danger">
        <h2>1. Test d'Authentification (Vulnérable aux Injections SQL)</h2>
        <?php if (isset($_SESSION['user'])): ?>
            <p>Bonjour, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong> ! (<a href="?logout=1">Se déconnecter</a>)</p>
        <?php else: ?>
            <?php if($loginError) echo "<p style='color:red;'>$loginError</p>"; ?>
            <form method="POST">
                <label>Nom d'utilisateur :</label>
                <input type="text" name="username" placeholder="ex: admin">
                <label>Mot de passe :</label>
                <input type="password" name="password" placeholder="ex: secret123">
                <button type="submit" name="login">Se connecter</button>
            </form>
            <p><small><em>Astuce d'attaque (Injection SQL) :</em> Essayez de taper <code>admin' -- </code> dans le champ utilisateur.</small></p>
        <?php endif; ?>
    </div>

    <!-- SECTION COMMENTAIRES (XSS) -->
    <div class="box danger">
        <h2>2. Espace Commentaires (Vulnérable au XSS)</h2>
        <form method="POST">
            <label>Auteur :</label>
            <input type="text" name="author" required>
            <label>Message :</label>
            <textarea name="message" required></textarea>
            <button type="submit" name="comment">Publier</button>
        </form>

        <h3>Commentaires récents :</h3>
        <hr>
        <?php foreach ($comments as $c): ?>
            <div style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                <strong><?php echo htmlspecialchars($c['author']); ?> :</strong>
                <!-- 
                  ⚠️ FAILLE XSS ICI : 
                  Si on affiche $c['message'] sans htmlspecialchars(), un script JS injecté s'exécutera.
                  Test XSS : <script>alert('XSS !');</script>
                -->
                <div><?php echo $c['message']; ?></div> 
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>