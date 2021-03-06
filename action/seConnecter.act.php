<?php

/**
 * Se connecter
 * 
 * Action qui permet de se connecter
 * 
 * @author: Guillaume CARAYON
 * @version: 1.0.0
 * 
 * @todo ajouter la connexion administrateur
 * 
 */
/* -------------------------------------------------------
 * Recuperer les donnees d'entree nécessaires à l'action
 * ------------------------------------------------------- */

// Modèles utilisés pour les données
include $models['Modele'];
include $models['Adresse'];
include $models['Utilisateur'];
include $models['Organisateur'];
include $models['Participant'];

$user = new Utilisateur();
$infosConnexion = recupererInfosConnexion();
//var_dump($infosConnexion);
$infosUser = $user->getUtilisateur($infosConnexion["login"]);
//var_dump($infosUser);

/* ---------------------------------------------------------
 * Executer l'action
/* --------------------------------------------------------- */

// On vérifie que le mot de passe récupéré selon le login est correct
$identifiant = checkPassword($infosConnexion["password"], $infosUser["mdpUser"]);

if ($identifiant)
{
    $participant = new Participant();
    $isParticipant = $participant->getParticipant($infosConnexion["login"]);

    if (!$isParticipant) {
        $organisateur = new Organisateur();
        $isOrganisateur = $organisateur->getOrganisateur($infosConnexion["login"]);

        // On récupère les informations nécessaires à l'affichage de la vue
        $infosVuesOrganisateur = array("nomOrganisateur" => $isOrganisateur["nomOrganisateur"], 
            "nbEventPending" => $organisateur->getNbEvenementEnCours($infosConnexion['login']), 
            "nbSubscribing" => $organisateur->getNbInscriptionEnCours($infosConnexion['login']));
    } else {
        // On récupère les informations nécessaires à l'affichage de la vue'
        $infosVuesParticipant = array("nomParticipant" => $isParticipant["nomParticipant"],
            "prenomParticipant" => $isParticipant["prenomParticipant"],
            "nbEventToCome" => $participant->getNbEvenementAVenir($infosConnexion["login"]),
            "nbEventWaiting" => $participant->getNbEvenementEnAttente($infosConnexion['login']));
    }
    $message = utf8_encode("Vous &ecirc;tes bien connect&eacute; !");
} else { 
    $message = utf8_encode("Vos identifiants sont incorrects !");
}

/* -------------------------------------------------------
 *  Definir le nouvel etat de l'application
 * ------------------------------------------------------- */

if ($identifiant) {
    if ($isParticipant) {
        $_SESSION['state'] = 'connecteParticipant_accueil';
    } else {
        $_SESSION['state'] = 'connecteOrganisateur_accueil';
    }
} else {
    $_SESSION['state'] = 'nonConnecte_accueil';
}
/* -------------------------------------------------------
 * Preparer les donnees de la vue resultante
 * ------------------------------------------------------- */

// Definition des donnees structurelles de la vue dans tous les cas
$dataView['zoneHaute'] = $views['banniere'];
$dataView['zoneRecherche'] = $views['recherche'];
$dataView['message'] = $message;
$dataView['css'] = $css['stylePrincipal'];
$dataView['zoneCentrale'] = $views['accueil'];

if ($_SESSION['state'] === "connecteParticipant_accueil") {
    $dataView['nomParticipant'] = $infosVuesParticipant['nomParticipant'];
    $dataView['prenomParticipant'] = $infosVuesParticipant['prenomParticipant'];
    $dataView['nbEventToCome'] = $infosVuesParticipant["nbEventToCome"];
    $dataView['nbEventWaiting'] = $infosVuesParticipant["nbEventWaiting"];
    $dataView['title'] = TITLE." - Bienvenue";
    $dataView['zoneMenu'] = $views["menuConnecteParticipant"];
} elseif ($_SESSION["state"] === "connecteOrganisateur_accueil") {
    $dataView['title'] = TITLE . " - Bienvenue !";
    $dataView['nomOrganisateur'] = $infosVuesOrganisateur["nomOrganisateur"];
    $dataView['nbEventPending'] = $infosVuesOrganisateur['nbEventPending'];
    $dataView['nbSubscribing'] = $infosVuesOrganisateur["nbSubscribing"];
    $dataView['zoneMenu'] = $views['menuConnecteOrganisateur'];
} else {
    $dataView['title'] = TITLE." - Acc&egrave;s refus&eacute;";
    $dataView['zoneMenu'] = $views['menuNonConnecte'];
}

// Enregistrement des donnees de la vue dans la session
$_SESSION['dataView'] = $dataView;



