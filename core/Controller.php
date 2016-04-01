<?php

require_once('Config.php');
require_once('DBConnector.php');

/**
 * Klasa jądra systemu
 */

define('BASE_URL', '/');
/**
 * Klasa pomocnicza, służąca do przechowywania w kontrolerze parametrów dla szablonu
 * Class ControllerParams
 */
class ControllerParams
{
    /**
     * Tablica z parametrami
     * @var array
     */
    private $params = array();

    /**
     * Setter - służy do ustawiania parametrów
     * @param string $name nazwa parametru
     * @param mixed $value wartość parametru
     */
    public function __set($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Zwraca tablicę z parametrami
     * @return array
     */
    public function get()
    {
        return $this->params;
    }
}

/**
 * Funkcja pomocnicza - odwraca kolejność daty
 * @param string $date data wejściowa
 * @return string
 */
function reverse_date_helper($date)
{
    if($date===NULL)
        return '---';
    $tmp  = explode('-',$date);
    if(count($tmp)<2)
        return $date;
    $a=$tmp[0];
    $tmp[0]=$tmp[2];
    $tmp[2]=$a;
    return implode('-',$tmp);
}

/**
 * Funkcja pomocnicza - generuje adres URL na podstwie nazwy kontrolera i akcji oraz parametrów
 * @param string $controller nazwa kontrolera
 * @param string $action nazwa akcji
 * @param array $params tablica z parametrami
 * @return string
 */
function url_helper_for_mvc_system($controller, $action, $params=array())
{
    if(lcfirst($controller)!='index')
    {
        $tmp = explode('/', $controller);
        foreach($tmp as $key=>$value)
            $tmp[$key] = lcfirst($value);
        $params['controller'] = implode('/', $tmp);
    }
    if($action!='index')
        $params['action'] = lcfirst($action);
    if(count($params)>0)
    {
        $url = BASE_URL.'?';
        $tmp = array();
        foreach ($params as $key => $value)
            $tmp[] = $key . '=' . $value;
        $url .= implode('&', $tmp);
    }
    else
        $url = BASE_URL;
    return $url;
}

/**
 * Funkcja pomocnicza - generuje adres na podstawie nazwy zasobu oraz parametrów
 * @param string $name nazwa zasobu
 * @param Config $config konfiguracja z mapowaniem nazw zasobów na kontrolery i akcje
 * @param array $params tablica z parametrami
 * @return string
 */
function url_helper_from_config_file($name, Config $config, $params=array())
{
    if($config->get($name)!=NULL)
    {
        $tmp = explode(',', $config->get($name));
        $url = url_helper_for_mvc_system($tmp[0], $tmp[1], $params);
        if(!empty($tmp[2]))
        {
            $tmp[2] = str_replace(';', '&', $tmp[2]);
            if(strlen($url)>1)
                $url .= '&'.$tmp[2];
            else
                $url .= $tmp[2];
        }
        return $url;
    }
    return '';
}

/**
 * Funkcja pomocnicza - dodaje do adresu nowe parametry
 * @param array $params parametry do dodania
 * @param string|null $url opcjonalny adres, do którego mają zostać dodane parametry; jeżeli nie podany, to zostaną dodane do bieżącego adresu
 * @return string
 */
function add_params_to_url_helper($params=array(), $url=NULL)
{
    if($url==NULL)
        $url=$_SERVER['REQUEST_URI'];
    $before = substr($url, 0, strpos($url, '?')+1);
    $url=substr($url, strpos($url, '?')+1);
    $tmp=explode('&', $url);
    foreach($params as $key=>$value)
        $tmp[]=$key.'='.$value;
    $url = implode('&',$tmp);
    return $before.$url;
}

/**
 * Funkcja pomocnicza - zamienia wartości podanych parametrów adresu; jeżeli jeszcze nie istnieją, to je dodaje
 * @param array $params parametry do zamiany
 * @param string|null $url opcjonalny adres, w którym mają zostać zamienione parametry; jeżeli nie podany, to zostaną zamienione w bieżącym adresie
 * @return null|string
 */
function replace_params_to_url_helper($params=array(), $url=NULL)
{
    if($url==NULL)
        $url=$_SERVER['REQUEST_URI'];
    $before = substr($url, 0, strpos($url, '?')+1);
    $url=substr($url, strpos($url, '?')+1);
    $tmp_params=explode('&', $url);
    foreach($tmp_params as $key=>$param)
    {
        $tmp=explode('=',$tmp_params[$key]);
        if(isset($params[$tmp[0]]))
        {
            $tmp[1]=$params[$tmp[0]];
            unset($params[$tmp[0]]);
        }
        $tmp_params[$key]=implode('=',$tmp);
    }
    if(count($params)>0)
        $url = add_params_to_url_helper($params, $before.implode('&',$tmp_params));
    else
        $url = $before.implode('&',$tmp_params);
    return $url;
}

/**
 * Funkcja pomocnicza - usuwa parametry o podanych nazwach z adresu
 * @param array $params tablica z nazwami parametrów do usunięcia
 * @param string|null $url opcjonalny adres, z którego mają zostać usunięte parametry; jeżeli nie podany, to zostaną usunięte z  bieżącego adresu
 * @return string
 */
function remove_params_to_url_helper($params, $url=NULL)
{
    if($url==NULL)
        $url=$_SERVER['REQUEST_URI'];
    $before = substr($url, 0, strpos($url, '?')+1);
    $url=substr($url, strpos($url, '?')+1);
    $tmp_params=explode('&', $url);
    foreach($tmp_params as $key=>$param)
    {
        $tmp=explode('=',$tmp_params[$key]);
        if(($index=array_search($tmp[0], $params))!==false)
        {
            unset($params[$index]);
            unset($tmp_params[$key]);
        }
        else
            $tmp_params[$key]=implode('=',$tmp);
    }
    $url = implode('&',$tmp_params);
    return $before.$url;
}

/**
 * Klasa kontrolera, po której dziedziczą wszystkie kontrolery systemu; oferuje potrzebną kontrolerom funkcjonalność
 * Class Controller
 */
abstract class Controller
{
    /**
     * Obiekt klasy parametrów szablonu
     * @var ControllerParams
     */
    protected $params;
    /**
     * Obiekt klasy konfiguracji
     * @var Config
     */
    protected $config;
    /**
     * Obiekt ładowarki systemu szablonów TWIG
     * @var Twig_Loader_Filesystem
     */
    private $twig_loader;
    /**
     * Obiekt środowiska systemu szablonów TWIG
     * @var Twig_Environment
     */
    private $twig_env;
    /**
     * Tablica z komunikatami dla użytkownika
     * @var array
     */
    private $messages;

    /**
     * Nazwa pliku z szablonem, który ma zostać zrenderowany
     * @var string/null
     */
    public $template = NULL;

    /**
     * Flaga pozwalająca zablokować renderowanie szablonu dla uruchomionej akcji
     * @var bool
     */
    public $noRender = false;

    /**
     * Konstruktor ładujący konfigurację oraz inicjujący potrzebne pola
     */
    public function __construct()
    {
        Auth::init();

        if(isset($_SESSION['short_message_for_one_session']))
            $this->messages = unserialize($_SESSION['short_message_for_one_session']);

        $this->params = new ControllerParams();
        $this->twig_loader = new Twig_Loader_Filesystem('../views/');
        $this->twig_env = new Twig_Environment($this->twig_loader, array('debug' => true));
        $this->twig_env->addExtension(new Twig_Extension_Debug());

        $this->config = new Config("../config/config.ini");

        //add predefned vars to TWIG

        $user = Auth::getLoggedUser();
        $app = array('user' => $user, 'messages'=>$this->messages);

        $this->twig_env->addGlobal('app', $app);

        ///---------------------

        $this->messages = array('warning'=>array(),'error'=>array(),'info'=>array(),'success'=>array());

        $function = new Twig_SimpleFunction('count', function ($o) {
            return count($o);
        });
        $this->twig_env->addFunction($function);

        $function = new Twig_SimpleFunction('reverse_date', function ($d) {
            return reverse_date_helper($d);
        });
        $this->twig_env->addFunction($function);

        //add url helpers to TWIG

        $function = new Twig_SimpleFunction('static_url', function ($controller, $action, $params=array()) {
            return url_helper_for_mvc_system($controller, $action, $params);
        });
        $this->twig_env->addFunction($function);

        $function = new Twig_SimpleFunction('url', function ($name, $params=array()) {
            return url_helper_from_config_file($name, new Config("../config/urls.ini"), $params);
        });
        $this->twig_env->addFunction($function);

        $function = new Twig_SimpleFunction('add_param', function ($params=array()) {
            return add_params_to_url_helper($params);
        });
        $this->twig_env->addFunction($function);

        $function = new Twig_SimpleFunction('replace_param', function ($params=array()) {
            return replace_params_to_url_helper($params);
        });
        $this->twig_env->addFunction($function);

        $function = new Twig_SimpleFunction('remove_param', function ($params=array()) {
            return remove_params_to_url_helper($params);
        });
        $this->twig_env->addFunction($function);

        //---------------------

        DBConnector::initialize($this->config);
    }

    /**
     * Destruktor pozwalający zapisać dodane komunikaty do sesji, aby je potem wyświetlić
     */
    public function __destruct()
    {
        $_SESSION['short_message_for_one_session'] = serialize($this->messages);
    }

    /**
     * Pozwala renderować szablon do zmiennej, zwracanej jako wynik
     * @param string $file nazwa pliku z szablonem
     * @param array $params tablica z parametrami
     * @return string
     */
    public function renderHelper($file, Array $params)
    {
        return $this->twig_env->render($file,$params);
    }

    /**
     * Ustawia komunikat do wyświetlenia przy następnym odświeżeniu strony
     * @param string $type typ komunikatu
     * @param string $content treść komunikatu
     */
    protected function setMessage($type, $content)
    {
        if(!isset($this->messages[$type]))
            $this->messages[$type] = array();
        $this->messages[$type][] = $content;
    }

    /**
     * Odczytuje wwszystkie komunikaty danego typu; po ich odczytaniu zostają one skasowane
     * @param string $type typ komunikatów
     * @return array
     */
    protected function getMessages($type)
    {
        if(isset($this->messages[$type]))
        {
            $tmp = $this->messages[$type];
            unset($this->messages[$type]);
            $this->messages[$type] = array();
            return $tmp;
        }
        else
            return array();
    }

    /**
     * Pozwala przekierować przeglądarkę pod adres zasobu podanego w parametrze
     * @param string $name nazwa zasobu
     * @param array $params parametry żądania
     */
    protected function redirect($name, Array $params = array())
    {
        $this->noRender = true;
        header('Location: '.url_helper_from_config_file($name, new Config("../config/urls.ini"), $params));
        die();
    }

    /**
     * Ładuje model i zwraca go już zainicjaizowanego
     * @param string $name nazwa modelu
     * @return \Model
     * @throws Exception
     */
    protected function loadModel($name)
    {
        $filename = '../models/'.ucfirst($name).'.php';
        if(!file_exists($filename))
            throw new Exception("Nie znaleziono pliku " . $filename);
        $classname = ucfirst($name).'Model';
        require_once($filename);
        return new $classname();
    }

    /**
     * Zwraca przefiltrowaną wartość z tablicy $_GET
     * @param string $name nazwa wartości
     * @return string/null
     */
    protected function get($name)
    {
        if(isset($_GET[$name]))
            return addslashes($_GET[$name]);
        else
            return NULL;
    }

    /**
     * Zwraca przefiltrowaną wartość z tablicy $_POST
     * @param string $name nazwa wartości
     * @return string/null
     */
    protected function post($name)
    {
        if(isset($_POST[$name]))
            if(gettype($_POST[$name])=='string')
                return addslashes($_POST[$name]);
            else
                return $_POST[$name];
        else
            return NULL;
    }

    /**
     * Blokuje dalsze wykonywanie akcji i przekierowuje przeglądarkę pd nowy adres z komunikatem błędu
     * @param string $target nazwa docelowego zasobu
     * @param string $msg treść komunikatu
     */
    protected function block($target,$msg)
    {
        $this->noRender = true;
        $this->setMessage('error', $msg);
        $this->redirect($target);
        die();
    }

    /**
     * Przekierowuje przeglądarkę do poprzednio wyświetlanego adresu, z opcjonalnym komunikatem informacyjnym
     * @param string/null $msg treść komunikatu
     */
    protected function forward($msg=NULL)
    {
        $this->noRender = true;
        if($msg!=NULL)
            $this->setMessage('info', $msg);
        header('Location: '.$_SERVER['HTTP_REFERER']);
        die();
    }

    /**
     * Renderuje szablon akcji wraz z ustawionymi parametrami
     * @return string
     */
    public function render()
    {
        $this->params->BASE_URL=BASE_URL;
        return $this->twig_env->render($this->template, $this->params->get());
    }

    /**
     * Generuje adres URL na podstwie nazwy kontrolera i akcji oraz parametrów
     * @param string $controller nazwa kontrolera
     * @param string $action nazwa akcji
     * @param array $params tablica z parametrami
     * @return string
     */
    public function url($controller = "index", $action = "index", $params = array())
    {
        return url_helper_for_mvc_system($controller, $action, $params);
    }
}