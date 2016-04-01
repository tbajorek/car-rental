<?php

/**
 * Główny kontroler aplikacji
 */

try
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();

    require_once '../core/Config.php';
    require_once '../core/DBConnector.php';
    require_once '../core/Model.php';
    require_once '../core/Controller.php';
    require_once '../core/Router.php';
    require_once '../core/Auth.php';

    require_once '../lib/Twig/Autoloader.php';
    Twig_Autoloader::register(true);

    $controller = Router::getController();
    Router::execAction($controller);

}
catch(Exception $e)
{
    header('Content-Type: text/html; charset=utf-8');
    die('Wystąpił poważny błąd systemu, uniemożliwiający mu dalszą pracę. Spróbuj przejść na stronę główną. Jeżeli aplikacja nadal nie działa, skontaktuj się z administratorem.');
}