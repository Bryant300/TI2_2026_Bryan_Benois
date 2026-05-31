<?php
# public/index.php

/*
 * Front Controller de la gestion du livre d'or.
 * Ce fichier recoit la requete, appelle le modele, puis charge la vue.
 */

require_once "../config.php";
require_once URL_BASE . "/model/guestbookModel.php";

$info = "";
$error = "";
$messages = [];
$pageActu = 1;
$nbTotalMessages = 0;
$pagination = "";

try {
    $db = new PDO(
        DB_DRIVER . ":host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_LOGIN,
        DB_PWD
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $success = addGuestbook(
        $db,
        $_POST["firstname"] ?? "",
        $_POST["lastname"] ?? "",
        $_POST["usermail"] ?? "",
        $_POST["phone"] ?? "",
        $_POST["postcode"] ?? "",
        $_POST["message"] ?? ""
    );

    if ($success) {
        $info = "Merci pour votre nouveau message.";
        header("Location: ./");
        exit;
    } else {
        $error = "Probleme lors de l'envoi du message. Verifiez les champs du formulaire.";
    }
}

if (isset($_GET[PAGINATION_GET]) && ctype_digit($_GET[PAGINATION_GET])) {
    $pageActu = (int) $_GET[PAGINATION_GET];
    if ($pageActu < 1) {
        $pageActu = 1;
    }
}

$nbTotalMessages = getNbTotalGuestbook($db);
$nbPages = (int) ceil($nbTotalMessages / PAGINATION_NB);

if ($nbPages > 0 && $pageActu > $nbPages) {
    $pageActu = $nbPages;
}

$messages = getGuestbookPagination($db, $pageActu, PAGINATION_NB);
$pagination = pagination($nbTotalMessages, "./", PAGINATION_GET, $pageActu, PAGINATION_NB, "#messages-section");

include URL_BASE . "/view/guestbookView.php";

$db = null;
