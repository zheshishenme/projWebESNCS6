<?php

/**
 * Fichier contrôleur
 * 
 * Inspiré du framework de M. Patrick Etcheverry - Enseignant à l'IUT de Bayonne et du Pays Basque
 *
 * @author: Guillaume CARAYON 
 * @version : 1.1.0
 * 
 */
include "config.php";
foreach ($libs as $nomLib => $emplacementlib) {
    include "$emplacementlib";
}
// On démarre ou on relance la session précédente
session_start();

// On définit au préalable l'action et l'état en cours à l'action d'initialisation et à l'état initial
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = $initAct;
}
if (!isset($_SESSION['state'])) {
    $_SESSION['state'] = $initState;
}

$userQuery = $_REQUEST['action'];        // requête utilisateur
$actState = $_SESSION['state'];  // état actuel
// On vérifie que la requête utilisateur est autorisée pour l'état actuel
$allowedQueries = $states[$actState]['allowedActs'];
$isAllowed = in_array($userQuery, $allowedQueries);

// Si l'action n'est pas autorisée, alors l'action est considérée comme un enchaînement invalide
if (!$isAllowed) {
    $userQuery = $falseAct;
}

// On fait appel à l'action demandée en incluant le(s) modèle(s) utilisés
if (!file_exists($acts[$userQuery])) {
    echo "Veuillez v&eacute;rifier que l'action demand&eacute;e $userQuery est bien existante <br/>";
} else {
    include $acts[$userQuery];
}

// On récupère le nouvel état de l'application et on affiche le template correspondant
$newState = $_SESSION['state'];
$tplToDisplay = $states[$newState]['displayTpl'];
$fileTpl = $tpls[$tplToDisplay];
if (!file_exists($fileTpl)) {
    echo "Veuillez v&eacute;rifier que le template demand&eacute; $tplToDisplay est bien configur&eacute; <br/>";
} else {
    include $fileTpl;
}
?>
