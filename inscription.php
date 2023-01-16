<?php
    session_start();
    include "connectDB.php";

    if (isset($_POST['update'])) {
        $email = $_POST['email'];
        $tel = $_POST['tel'];
        $filename = $_FILES['photo']['name'];
        $filiere = $_POST['filiere'];
        $niveau = $_POST['niveau'];

        if(!empty($filename)){
            $new_filename = $_SESSION['user'].'-'.$filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], './images/'.$new_filename);

            $conn = $pdo->open();
            $stmt = $conn->prepare("UPDATE etudiants SET email=:email, telephone=:tel, image=:img, filiere=:fil, niveau=:niv WHERE matricule=:mat");
            $stmt->execute(['mat'=>$_SESSION['user'] ,'email'=>$email, 'tel'=>$tel, 'img'=>$new_filename, 'niv'=>$niveau, 'fil'=>$filiere]);

            header('Location: connexion.html');
            exit();
        }
    }


?>