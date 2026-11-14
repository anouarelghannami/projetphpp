
<?php
// Démarrage de la session
session_start();

// --- INITIALISATION DE LA BASE DE DONNÉES ---
try {
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la table utilisateurs
    $db->exec("
        CREATE TABLE users (
            id INTEGER PRIMARY KEY,
            username TEXT,
            password TEXT
        )
    ");

    // Création du compte admin avec mot de passe hashé
    $passwordHash = password_hash('secret123', PASSWORD_DEFAULT);

    $stmt = $db->prepare(
        "INSERT INTO users (username, password) VALUES (?, ?)"
    );
    $stmt->execute(['admin', $passwordHash]);

    // Création de la table commentaires
    $db->exec("
        CREATE TABLE comments (
            id INTEGER PRIMARY KEY,
            author TEXT,
            message TEXT
        )
    ");

    $db->exec("
        INSERT INTO comments (author, message)
        VALUES ('Alice', 'Bienvenue sur le site !')
    ");

} catch (Exception $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// --- GESTION DE LA CONNEXION ---
$loginError = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Requête sécurisée contre SQL Injection
    $stmt = $db->prepare(
        "SELECT * FROM users WHERE username = :username"
    );

    $stmt->execute([
        'username' => $username
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe hashé
    if ($user && password_verify($password, $user['password'])) {

        // Régénération de l'identifiant de session
        session_regenerate_id(true);

        $_SESSION['user'] = $user['username'];

    } else {
        $loginError = "Identifiants incorrects.";
    }
}

// --- DÉCONNEXION ---
if (isset($_GET['logout'])) {

    $_SESSION = [];

    session_destroy();

    header("Location: index.php");
    exit;
}

// --- GESTION DES COMMENTAIRES ---
if (isset($_POST['comment'])) {

    $author = $_POST['author'] ?? '';
    $message = $_POST['message'] ?? '';

    // Requête préparée contre les injections SQL
    $stmt = $db->prepare(
        "INSERT INTO comments (author, message) VALUES (?, ?)"
    );

    $stmt->execute([$author, $message]);

    header("Location: index.php");
    exit;
}

// --- RÉCUPÉRATION DES COMMENTAIRES ---
$comments = $db->query(
    "SELECT * FROM comments"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Projet Sécurisé</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f4f9;
        }

        .box {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .danger {
            border-left: 5px solid #28a745;
        }

        input, textarea {
            display: block;
            margin-bottom: 10px;
            padding: 8px;
            width: 100%;
            max-width: 400px;
        }

        button {
            padding: 8px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>

</head>

<body>

    <h1>Laboratoire de Sécurité Web (Version Sécurisée)</h1>

    <!-- SECTION CONNEXION -->
    <div class="box danger">

        <h2>1. Authentification Sécurisée</h2>

        <?php if (isset($_SESSION['user'])): ?>

            <p>
                Bonjour,
                <strong>
                    <?php echo htmlspecialchars(
                        $_SESSION['user'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
                !
                (<a href="?logout=1">Se déconnecter</a>)
            </p>

        <?php else: ?>

            <?php if ($loginError): ?>
                <p style="color:red;">
                    <?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>

            <form method="POST">

                <label>Nom d'utilisateur :</label>
                <input type="text" name="username" required>

                <label>Mot de passe :</label>
                <input type="password" name="password" required>

                <button type="submit" name="login">
                    Se connecter
                </button>

            </form>

        <?php endif; ?>

    </div>

    <!-- SECTION COMMENTAIRES -->
    <div class="box danger">

        <h2>2. Espace Commentaires Sécurisé</h2>

        <form method="POST">

            <label>Auteur :</label>
            <input type="text" name="author" required>

            <label>Message :</label>
            <textarea name="message" required></textarea>

            <button type="submit" name="comment">
                Publier
            </button>

        </form>

        <h3>Commentaires récents :</h3>

        <hr>

        <?php foreach ($comments as $c): ?>

            <div style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">

                <strong>
                    <?php echo htmlspecialchars(
                        $c['author'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?> :
                </strong>

                <div>
                    <?php echo htmlspecialchars(
                        $c['message'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

</body>
</html>
