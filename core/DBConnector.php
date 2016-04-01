<?php

/**
 * Klasa jądra systemu
 */

require_once("Config.php");

/**
 * Klasa odpowiedzialna za obsługę połączenia z bazą danych
 * Class DBConnector
 */
abstract class DBConnector
{
    /**
     * Instancja obiektu biblioteki PDO
     * @var PDO/null
     */
    private static $instance = NULL;

    /**
     * Inicjalizuje połączenie
     * @param Config $config obiekt zawierający konfigurację aplikacji
     */
    public static function initialize(Config $config)
    {
        $dsn = "pgsql:host=".$config->get('db.host').";port=".$config->get('db.port').";dbname=".$config->get('db.name').";user=".$config->get('db.username').";password=".$config->get('db.password');
        try
        {
            self::$instance = new PDO($dsn);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->exec('SET NAMES \'UTF8\'');
            if($config->get('db.schema')!=NULL)
                self::$instance->exec('SET SCHEMA \''.$config->get('db.schema').'\'');
        }
        catch(PDOException $e)
        {
            echo 'Połączenie z bazą danych nie mogło zostać utworzone: ' . $e->getMessage();
        }
    }

    /**
     * Zwraca obiekt połączenia
     * @return PDO/null
     * @throws Exception
     */
    public static function get()
    {
        if(self::$instance!=NULL)
            return self::$instance;
        throw new Exception("Uzywasz niezainicjalizowanej klasy DBConnector");
    }
}