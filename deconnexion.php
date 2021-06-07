<?php
session_start();
session_unset();
session_destroy(); //On détruit le cookie de l'identifiant.
header('location:/portfolio/index.php'); //On revient au départ.
