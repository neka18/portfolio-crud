<?php

$bdd = new PDO('mysql:host=localhost;dbname=pavin;charset=utf8', 'pavin', 'WtAgs8VP5m');
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
include('main.php');


if(isset($_POST['name']) && isset($_POST['password'])){ //si le formulaire est bien rempli, on rentre dans la condition
    $pseudo = $_POST['name'];
    $mdp = $_POST['password'];
    $regex = "/^[A-Za-z\-]+$/";
    if(preg_match($regex, $mdp)){ //verification du regex
        $request = $bdd->prepare("SELECT * from `connexion` WHERE `pseudo` = '$pseudo'"); 
        $request->execute();
        if($request->rowCount() === 1){ 
            $login = $request->fetch(PDO::FETCH_ASSOC);
            if($pseudo ===  $login['pseudo'] && password_verify($mdp, $login['mdp']) === true){ //password_verify permet de verifier le mdp crypté
                $_SESSION['pseudo'] = $login['pseudo'];
                $_SESSION['id'] = $login['id'];
                header("location: /portfolio/main.php"); //renvoie sur la page main ou il y a le crud
                exit();
            }
            else{
                writeServiceMessage("pseudo ou mot de passe incorect"); //en cas d'erreur renvoie sur la page avec un message expliquant la cause
                header("location: /portfolio/index.php");
                exit();
            }   
        }
        else{
            writeServiceMessage("pseudo ou mot de passe incorect"); 
            header("location: /portfolio/index.php");
            exit();
        }
    }
    else{
        writeServiceMessage("mot de passe hors norme.");
        header("location: /portfolio/index.php");
        exit();
    } 
}
else{
    writeServiceMessage("pseudo ou mot de passe incorect");
    header("location: /portfolio/index.php");
    exit();
}
    