<?php

/**
 * Klasa jądra systemu
 */

require_once("Controller.php");

/**
 * Klasa routera, zarządzającego tym, jaka akcja którego kontrolera ma być uruchomiona
 * Class Router
 */
abstract class Router
{
    /**
     * Załadowanie kontrolera
     * @return Controller
     */
    public static function getController()
    {
        try
        {
            if (isset($_GET['controller'])) {
                $tmp = explode('/', $_GET['controller']);
                foreach ($tmp as $key => $value)
                    $tmp[$key] = ucfirst($value);
                $clname = implode('\\', $tmp);
            } else
                $clname = "Index";
            $file = '../controllers/' . str_replace('\\', '/', $clname) . '.php';
            if (!file_exists($file))
                die("Nie znaleziono pliku klasy kontrolera ".$clname);
            require_once($file);
            $clname = $clname . 'Controller';
            return new $clname();
        }
        catch(Exception $e)
        {
            header('Content-Type: text/html; charset=utf-8');
            die('Wystąpił błąd podczas ładowania kontrolera. Spróbuj przejść na stronę główną.');
        }
    }

    /**
     * Wykonanie akcji na kontrolerze
     * @param Controller $controller obiekt kontrolera
     */
    public static function execAction(Controller $controller)
    {
        if(isset($_GET['action']))
            $action = $_GET['action'].'Action';
        else
            $action = "indexAction";
        try
        {
            $controller->$action();
            if ($controller->noRender == false) {
                if ($controller->template == NULL) {
                    $controller->template = strtolower(str_replace("Controller", "", str_replace('\\', '/', get_class($controller)))) . '/' . str_replace("Action", "", $action) . '.html.twig';
                    if (!file_exists('../views/' . $controller->template))
                        throw new Exception("Nie znaleziono pliku " . $controller->template);
                }
                echo $controller->render();
            }
        }
        catch(Exception $e)
        {
            header('Content-Type: text/html; charset=utf-8');
            echo $controller->renderHelper('../views/error.html.twig', array('message'=>$e->getMessage()));
        }
    }
}
