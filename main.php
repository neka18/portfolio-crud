<?php

session_start();
$bdd = new PDO('mysql:host=localhost;dbname=pavin;charset=utf8', 'pavin', 'WtAgs8VP5m');
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_SESSION['id'] )&& isset($_SESSION['pseudo'])){
    $action = 'list';
    $table = '';
    if (isset($_GET['table'])) {
        $table = $_GET['table'];
        if(isset($_GET['action'])){
            $action = $_GET['action'];
        }
    }

    /******CRUD******/
    $content = '';

    if ($action == 'list' && ($table == 'realisation' || $table =='service')) {
        $content = display($table, $bdd);
    }

    else if ($action == 'create' && ($table == 'realisation' || $table =='service')) {
        $content = create($table, $bdd);
    }

    else if ($action == 'delete' && ($table == 'realisation' || $table =='service')) {
        delete($table, $bdd);
    }

    else if ($action == 'update' && ($table == 'realisation' || $table =='service')) {
        $content = update($table, $bdd);
    }

    /******HTML******/



    echo '<html>
        <head>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
        <br>';
    echo getNav($table);

    echo '<br>
    <div class="container">';
    $message = getServiceMessage();
    if ($message) {
    echo '<div class="alert alert-primary" role="alert">
            '.$message.'
    </div>
    ';
    }
    echo $content;
    echo '</div>';
    echo '</body></html>';

}

/*******************************************************************FONCTIONS************************************************************************************* */

function display($table, $bdd){

    $request = $bdd->prepare('SELECT * FROM '.$table.'');
    $request->execute();
    $lines = $request->fetchAll();

    if($table == 'realisation'){
        $content = getTableRealisation($lines);
    }
    else if($table == 'service'){
        $content = getTableService($lines);
     }

    return $content;
}

function create($table, $bdd){
    if (isFormSubmit()){
        if (isFormValid()){
            $filePath = uploadFile();

            if($table=='realisation'){
                $request = $bdd->prepare('INSERT INTO `realisation` (`nom`, `image`, `description`) VALUES (:name, :image, :description)');
                $params = [
                    'name' => $_POST['name'],
                    'image' => $filePath,
                    'description' => $_POST['description']
                ]; 
            } 
            else if($table=='service'){
                $request = $bdd->prepare('INSERT INTO `service` (`nom`, `image`, `description`) VALUES (:name, :image, :description)');
                $params = [
                    'name' => $_POST['name'],
                    'image' => $filePath,
                    'description' => $_POST['description']
                    ];

            }
            if ($request->execute($params)) 
            {
                if($table=="realisation"){
                    writeServiceMessage('La réalisation à été créée avec succès \\o/');
                header('Location: /portfolio/main.php?table=realisation&action=list');
                die();
                }else if($table=='service'){
                    writeServiceMessage('Le service à été créée avec succès \\o/');
                    header('Location: /portfolio/main.php?table=service&action=list');
                }
            }
        }
    }
    if($table=='realisation'){
        $content=getFormRealisation(null);
    } else if($table=='service'){
        $content=getFormService(null);
    } 
    return $content;
}
    
function delete($table, $bdd){
    if (!isset($_GET['id'])) {
        http_response_code(400);
        $content = 'Mauvaise requête, impossible de supprimer sans avoir un id. <a href="/action=list&table='.$table.'">Retour à la liste</a>';
    }else{
        $request = $bdd->prepare('DELETE FROM '.$table.' WHERE `id`=:id');
        $params = ['id' => $_GET['id']];
    
        if($request->execute($params)){
            if($table=='realisation') {
                writeServiceMessage('La réalisation à été supprimée avec succès');
                header('Location: /portfolio/main.php?table=realisation&action=list');
                die();

            } else if($table=='service') {
            
                writeServiceMessage('Le service à été supprimé avec succès');
                header('Location: /portfolio/main.php?table=service&action=list');
                die();
            } 
        }
    }
}

function update($table, $bdd){
    if (!isset($_GET['id'])) {
        http_response_code(400);
        $content = 'Mauvaise requête, impossible de mettre à jour sans avoir un id. <a href="/action=list&table='.$table.'">Retour à la liste</a>';
    } else {
        if (isFormSubmit()) {
            if (isFormValid()) {
                $filePath = uploadFile();
                $imageUpdate ='';
                if($filePath !== null) {
                    $imageUpdate = ' `image`=:image,';
                }

                if($table=='realisation'){
                    $request = $bdd->prepare('UPDATE `realisation` SET `nom`=:name, '.$imageUpdate.' `description`=:description WHERE `id`=:id');
                    $params = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'id' => $_GET['id']
                ];
                }
                else if($table=='service'){
                    $request = $bdd->prepare('UPDATE `service` SET `nom`=:name, '.$imageUpdate.' `description`=:description WHERE `id`=:id');
                    $params = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'id' => $_GET['id']
                ];
                }

                if ($filePath !== null) {
                    $params['image'] = $filePath;
                }

                if ($request->execute($params)) {
                    if($table=='realisation'){
                    writeServiceMessage('La réalisation à été mise à jour avec succès \\o/');
                    header('Location: /portfolio/main.php?table=realisation&action=list');
                    die();
                    }
                    else if($table == 'service'){
                    writeServiceMessage('Le service à été mis à jour avec succès \\o/');
                    header('Location: /portfolio/main.php?table=service&action=list');
                    die();
                    }
                }
            }
        }
        if($table=='realisation'){
            $request = $bdd->prepare('SELECT * from `realisation` WHERE `id`=:id');
            $request->execute(['id' => $_GET['id']]);
            $lines = $request->fetchAll();
            if (!count($lines)){
                http_response_code(404);
                $content = 'Données introuvables <a href="/portfolio/realisation">Retour à la liste</a>';
            }else {
                $content = getFormRealisation($lines[0]);
            }
        } else if($table=='service'){
            $request = $bdd->prepare('SELECT * from `service` WHERE `id`=:id');
            $request->execute(['id' => $_GET['id']]);
            $lines = $request->fetchAll();
            if (!count($lines)){
                http_response_code(404);
                $content = 'Données introuvables <a href="/portfolio/service">Retour à la liste</a>';
            }else {
                $content = getFormService($lines[0]);
            }
        }
    }
    return $content;
}




function getTableRealisation($lines) {
    $table = '<h1>Liste des réalisations</h1>';
    $table .= '<table class="table">';
    $table .= '<thead><tr><th>id</th><th>nom</th><th>réalisation</th><th>date</th></tr></thead>';
    $table .= '<tbody>';
    foreach ($lines as $line) {
        $table .= '<tr>';
        $table .= '<td>'.$line['id'].'</td>';
        $table .= '<td>'.$line['nom'].'</td>';
        $table .= '<td>'.($line['image'] !== null ? '<img class="img-realisation" src="/portfolio/'.$line['image'].'" />' : '').'</td>';
        $table .= '<td>'.$line['description'].'</td>';
        $table .= '<td>
                        <a class="btn btn-danger" href="?table=realisation&action=delete&id='.$line['id'].'"><i class="fa fa-times"></i></a>
                        <a class="btn btn-primary" href="?table=realisation&action=update&id='.$line['id'].'"><i class="fa fa-edit"></i></a>
                    </td>';
        $table .= '</tr>';
    }
    $table .= '</tbody>';
    $table .= '</table>';

    return $table;
}

function getTableService($lines){
    $table = '<h1>Liste des services</h1>';
    $table .= '<table class="table">';
    $table .= '<thead><tr><th>id</th><th>nom</th><th>réalisation</th><th>description</th></tr></thead>';
    $table .= '<tbody>';
    foreach ($lines as $line) {
        $table .= '<tr>';
        $table .= '<td>'.$line['id'].'</td>';
        $table .= '<td>'.$line['nom'].'</td>';
        $table .= '<td>'.($line['image'] !== null ? '<img class="img-realisation" src="/portfolio/'.$line['image'].'" />' : '').'</td>';
        $table .= '<td>'.$line['description'].'</td>';
        $table .= '<td>
                        <a class="btn btn-danger" href="?table=service&action=delete&id='.$line['id'].'"><i class="fa fa-times"></i></a>
                        <a class="btn btn-primary" href="?table=service&action=update&id='.$line['id'].'"><i class="fa fa-edit"></i></a>
                    </td>';
        $table .= '</tr>';
    }
    $table .= '</tbody>';
    $table .= '</table>';

    return $table;
}

function isFormSubmit() : bool {
    return isset($_POST['name']);
}

function isFormValid() : bool {
    $valid = 
        !empty($_POST['name'])
        && !empty($_POST['description']);
 

        // Validation du fichier
        if ($valid && isset($_FILES['image']) AND $_FILES['image']['error'] == 0) {
            // Testons si le fichier n'est pas trop gros
            if ($_FILES['image']['size'] > 1000000) {
                writeServiceMessage("File too large");
                $valid = false;
            }
            // Test de l'extension
            $infosfichier = pathinfo($_FILES['image']['name']);
            $extension_upload = $infosfichier['extension'];
            $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
            if (!in_array($extension_upload, $extensions_autorisees))
            {
                writeServiceMessage("Only ".implode(", ", $extensions_autorisees). " files are allowed");
                $valid = false;
            }
        }
        return $valid;
}

function getFormRealisation($realisation){
    $form = '
        <h1>Ajouter une réalisation</h1>
        <form method="post" enctype="multipart/form-data">
        <br>
            <div class="form-group">
                <label for="name">Nom</label>
                <input class="form-control" type="text" name="name" value="'.($realisation ? $realisation['nom'] : '').'" required>
            </div>
            <br>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" name="image" id="image" '.($realisation ? 'disabled="true"': '').' />';
            if ($realisation) {
                $form.= '<img class="img-realisation" src="/portfolio/'.$realisation['image'].'" />';
            }$form.='
            <br>
            <script src="/portfolio/images.js"></script>
            <br>
            <br>
            <div class="form-group">
                <label for="description">Date</label>
                <input class="form-control" name="description" type="date" id="description" value="'.($realisation ? $realisation['description'] : '').'" required>
            </div>
            <div class="form-group">
            <br>
                <button class="btn btn-primary" type="submit">Envoyer</button>
            </div>
            
        </form>
    ';

    return $form;
}

function getFormService($service){
    $form = '
        <h1>Ajouter un service</h1>
        <form method="post" enctype="multipart/form-data">
        <br>
            <div class="form-group">
                <label for="name">Nom</label>
                <input class="form-control" type="text" name="name" value="'.($service ? $service['nom'] : '').'" required>
            </div>
            <br>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" name="image" id="image" '.($service ? 'disabled="true"': '').' />';
            if ($service) {
                $form.= '<img class="img-realisation" src="/portfolio/'.$service['image'].'" />';
            }$form.='
            <br>
            <script src="/portfolio/images.js"></script>
            <br>
            <br>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" id="description">'.($service ? $service['description'] : '').'</textarea>
            </div>
            <div class="form-group">
            <br>
                <button class="btn btn-primary" type="submit">Envoyer</button>
            </div>
            
        </form>
    ';

    return $form;
}

function getNav($table){
    $nav='
    <nav>
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="?table=realisation&action=list">Voir la liste des réalisations</a>
            </li>          
            <li class="nav-item">
                <a class="nav-link" href="?table=service&action=list">Voir la liste des services</a>
            </li>';
            
            if ($table=='realisation') {
                $nav.= '<li class="nav-item">
                        <a class="nav-link" href="?table=realisation&action=create">Ajouter une réalisation</a>
                    </li>';
            }
            else if($table=='service'){
                $nav.='<li class="nav-item">
                <a class="nav-link" href="?table=service&action=create">Ajouter un service</a>
              </li>';
            }

            $nav.= '
          <li class="nav-item">
            <a class="nav-link" id="logout" href="/portfolio/deconnexion.php">se deconnecter</a>
        </li>
      </ul>
    </nav>
    ';

    return $nav;
}

function writeServiceMessage($message) {
    $_SESSION['serviceMessage'] = $message;
}

function getServiceMessage() {
    $message = null;
    if (isset($_SESSION['serviceMessage'])) {
        $message = $_SESSION['serviceMessage'];
        unset($_SESSION['serviceMessage']);
    }

    return $message;
}

function uploadFile() {
    $filePath = 'uploads/'.uniqid().basename($_FILES['image']['name']);
    if(move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        return $filePath;
    }
    return null;
}

function getFormLogin(){
    $form='
    <form method="post" action="auth.php">
        <br>
        <h1>Se connecter</h1>
        <br>
        <div class="form-group">
            <label for="name">Pseudo</label>
            <input class="form-control" type="text" name="name" id="name" required>
        </div>
        <br>
        <div class="form-group">
            <label for="password">mot de passe</label>
            <input class="form-control" type="password" name="password" id="password" required>
        <br>
        </div>
        <div>
            <button class="btn btn-primary" type="submit">Envoyer</button>
        </div>
    </form>';

    return $form;
}

