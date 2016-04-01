<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler obsługi strony kontaktowej
 * Class KontaktController
 */
class KontaktController extends Controller
{
    /**
     * Wyświetlenie informacji kontaktowych
     */
    public function indexAction()
    {
        $this->params->nazwa = $this->config->get('info.nazwa');
        $this->params->ulica = $this->config->get('info.ulica');
        $this->params->kod_pocztowy = $this->config->get('info.kod_pocztowy');
        $this->params->miasto = $this->config->get('info.miasto');
    }
}