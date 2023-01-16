<?php 
    session_start();
    include "connectDB.php";

    if (isset($_POST['connect'])) {
        $matricule = $_POST['matricule'];

        $con = $pdo->open();
        $stmt = $con->prepare("SELECT matricule, COUNT(*) as numrow FROM etudiants WHERE matricule=:mat");
        $stmt->execute(['mat'=>$matricule]);
        $row = $stmt->fetch();

        if ($row['numrow'] > 0) {
            $_SESSION['user'] = $row['matricule'];
            header('Location: inscription.html');
            exit();
        } else {
            header('Location: connexion.html');
            exit();
        }
    }
?>