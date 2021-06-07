<?php

$bdd = new PDO('mysql:host=localhost;dbname=pavin;charset=utf8', 'pavin', 'WtAgs8VP5m');
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// autorisations
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
// on récupère la source de la page 
$resource = null;
if (isset($_GET['resource'])) {
    $resource = $_GET['resource'];
}

// on rentre si c'est un post
if($_SERVER['REQUEST_METHOD']==='POST'){
    $inputJSON = file_get_contents('php://input');
    $contactForm = json_decode($inputJSON, true);

    // on stock les post dans des variables
    $name = $contactForm['name'];
    $email = $contactForm['email'];
    $message = $contactForm['message'];

    // requête sql
    $request = $bdd->prepare("INSERT INTO `contact`(`name`, `email`, `message`) VALUES ('$name', '$email', '$message')");
    $request->execute();
}
else{
    // on rentre si c'est un get et qu'on se situe dans realisations
    if($resource == 'realisation' && $_SERVER['REQUEST_METHOD']==='GET'){
        $request = $bdd->prepare("SELECT * FROM `realisation`");
        $request->execute();
        $data = $request->fetchAll();
        echo json_encode($data);
    }
    else{
        // on rentre si c'est un get et qu'on se situe dans services
        if($resource == 'service' && $_SERVER['REQUEST_METHOD']==='GET'){
            $request = $bdd->prepare("SELECT * FROM `service`");
            $request->execute();
            $data = $request->fetchAll();
            echo json_encode($data);
        }
    }
}
