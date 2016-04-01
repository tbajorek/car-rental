<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do wyświetlania informacji o aktualnie wypożyczonym przez użytkowika samochodzie
 * Class AktualnieController
 */
class AktualnieController extends Controller
{
    /**
     * Wyświetlenie informacji o aktualnie wypożyczonym przez użytkowika samochodzie
     * @throws Exception
     */
    public function indexAction()
    {
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $aktualne=$this->loadModel('Wypozyczenie');
        $this->params->aktualnie=$aktualne->aktualnie(Auth::getLoggedUserId());
    }
}