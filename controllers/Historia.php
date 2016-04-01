<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do wyświetlania historii wypozyczeń użytkownika
 * Class HistoriaController
 */
class HistoriaController extends Controller
{
    /**
     * Wyświetlenie historii wypozyczeń użytkownika
     * @throws Exception
     */
    public function listaAction()
    {
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $historia=$this->loadModel('Historia');
        $this->params->wypozyczenia=$historia->lista(\Auth::getLoggedUserId());
    }
}