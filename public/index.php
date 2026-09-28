<?php
// Front Controller : réception, modèle, puis vue.
session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$config = __DIR__ . '/../config.php';
if (!is_file($config)) {
    http_response_code(503);
    exit('Configuration manquante : copiez config.php.ini en config.php.');
}
require_once $config;
require_once URL_BASE . '/model/guestbookModel.php';
$info = $_SESSION['guestbook_info'] ?? '';
unset($_SESSION['guestbook_info']);
$error = '';
$messages = [];
$pageActu = 1;
$nbTotalMessages = 0;
$pagination = '';
$values = array_fill_keys(['firstname', 'lastname', 'usermail', 'phone', 'postcode', 'message'], '');
try {
    $db = new PDO(
        DB_DRIVER . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_LOGIN, DB_PWD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES => false]
    );
} catch (PDOException $e) {
    error_log('Livre d’or : connexion impossible.');
    http_response_code(503);
    exit('La base est indisponible. Vérifiez la configuration et le service MariaDB.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validShape = true;
    foreach ($values as $key => $_) {
        $raw = $_POST[$key] ?? '';
        if (!is_string($raw)) $validShape = false;
        $values[$key] = is_string($raw) ? trim($raw) : '';
    }
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        $error = 'Le formulaire a expiré. Rechargez la page puis réessayez.';
    } elseif (!$validShape) {
        http_response_code(422);
        $error = 'Les champs doivent contenir du texte.';
    } elseif (addGuestbook($db, ...array_values($values))) {
        $_SESSION['guestbook_info'] = 'Merci pour votre nouveau message.';
        header('Location: ./#messages-section', true, 303);
        exit;
    } else {
        http_response_code(422);
        $error = 'Vérifiez les champs : noms de 2 à 100 caractères, email valide, mobile belge, code postal à 4 chiffres et message de 10 à 300 caractères.';
    }
}
$requestedPage = $_GET[PAGINATION_GET] ?? '1';
if (is_string($requestedPage) && ctype_digit($requestedPage)) {
    $pageActu = max(1, (int) $requestedPage);
}
try {
    $nbTotalMessages = getNbTotalGuestbook($db);
    $nbPages = max(1, (int) ceil($nbTotalMessages / PAGINATION_NB));
    $pageActu = min($pageActu, $nbPages);
    $messages = getGuestbookPagination($db, $pageActu, PAGINATION_NB);
    $pagination = pagination($nbTotalMessages, './', PAGINATION_GET, $pageActu, PAGINATION_NB, '#messages-section');
} catch (PDOException $e) {
    http_response_code(503);
    $error = 'Impossible de lire les messages. Vérifiez que la table a été importée.';
}
require URL_BASE . '/view/guestbookView.php';
$db = null;
