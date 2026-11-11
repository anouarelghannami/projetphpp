
<?php
$conn = mysqli_connect("localhost", "root", "", "security_db");

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérification utilisateur
    $sql = "SELECT * FROM users
            WHERE username = '$username'
            AND password = '$password'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        session_start();

        $_SESSION['username'] = $username;

        echo "Bienvenue " . $username;

    } else {
        echo "Login ou mot de passe incorrect";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <button type="submit" name="login">Se connecter</button>
</form>
