<?php

/**
 * Klasa jądra systemu
 */

/**
 * Statyczna klasa służąca do obsługi zalogowanego użytkownika
 * Class Auth
 */
class Auth
{
    /**
     * Flaga inicjalizacji klasy
     * @var bool
     */
    private static $init = false;

    /**
     * Zmienna przechowująca tablicę z danymi użytkownika, jeśli zalogowany
     * @var array|null
     */
    private static $auth = NULL;

    /**
     * Inicjalizuje klasę
     */
    public static function init()
    {
        if(isset($_SESSION['auth_data']))
            self::$auth = unserialize($_SESSION['auth_data']);
        self::$init = true;
    }

    /**
     * Sprawdza, czy aktualnie jest zalogowany użytkownik
     * @return bool
     */
    public static function isLogged()
    {
        if(self::$auth!=NULL)
            return true;
        else
            return false;
    }

    /**
     * Ustawia podanego użytkownika jako zalogowanego
     * @param array $user informacje o użytkowniku
     */
    public static function login(Array $user)
    {
        self::$auth = $user;
        self::save();
    }

    /**
     * Usuwa informacje o zalogowanym użytkowniku
     */
    public static function logout()
    {
        self::$auth = NULL;
        self::save();
    }

    /**
     * Zwraca informacje o zalogowanym użytkowniku, jeśli jest on zalogowany
     * @return array|null
     */
    public static function getLoggedUser()
    {
        if(self::isLogged())
            return self::$auth;
        else
            return NULL;
    }

    /**
     * Zwraca id zalogowanego użytkownika, jeśli jest on zalogowany
     * @return int|null
     */
    public static function getLoggedUserId()
    {
        if(self::isLogged())
            return self::$auth['uzytkownik_id'];
        else
            return NULL;
    }

    /**
     * Zapisuje w sesji dane zalogowanego użytkownika
     */
    private static function save()
    {
        $_SESSION['auth_data'] = serialize(self::$auth);
    }
}