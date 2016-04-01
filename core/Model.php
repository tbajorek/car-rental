<?php

/**
 * Klasa jądra systemu
 */

require_once('DBConnector.php');

/**
 * Klasa modelu, doarczająca potomkom ustanowione połączenie z bazą danych
 * Class Model
 */
abstract class Model
{
    /**
     * Obiekt połączenia z bazą danych
     * @var PDO/null
     */
    protected $connector = NULL;

    /**
     * Inicjalizacja modelu
     * @throws Exception
     */
    public function __construct()
    {
        $this->connector = DBConnector::get();
    }
}