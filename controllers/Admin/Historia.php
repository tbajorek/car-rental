<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do obsługi historii - z poziomu administratora
 * Class HistoriaController
 * @package Admin
 */
class HistoriaController extends \Controller
{
    /**
     * Konstruktor kontrolera, sprawdzający uprawienia administratora
     */
    public function __construct()
    {
        parent::__construct();
        $zalogowany = \Auth::getLoggedUser();
        if(!\Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        else if($zalogowany['admin']==false)
            $this->block('strona_glowna', 'Nie masz praw do oglądania tej zawartości');
    }

    /**
     * Wyśswietlenie listy historii wypożyczeń dla wszystkich użytkowników
     * @throws \Exception
     */
    public function listaAction()
    {
        $historia=$this->loadModel('Historia');
        $this->params->wypozyczenia=$historia->lista();
    }
}