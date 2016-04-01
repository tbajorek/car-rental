<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do przegladania listy użytkowników
 * Class UzytkownicyController
 * @package Admin
 */
class UzytkownicyController extends \Controller
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
     * Wyświetlenie listy użytkowników systemu
     * @throws \Exception
     */
    public function listaAction()
    {
        $model=$this->loadModel('Uzytkownik');
        $this->params->uzytkownicy=$model->lista();
    }
}