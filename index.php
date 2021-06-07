<?php


$bdd = new PDO('mysql:host=localhost;dbname=pavin;charset=utf8', 'pavin', 'WtAgs8VP5m');
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
include 'main.php';


$content = getFormLogin();
echo '<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>';

    echo '<div class="container">';
    $message = getServiceMessage();
    if ($message) {
    echo '<div class="alert alert-danger" role="alert">
            '.$message.'
    </div>
    ';
    }
    echo $content;
    echo '</body></html>';
    