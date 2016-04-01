<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do obsługi listy samochodów
 * Class SamochodyController
 */
class SamochodyController extends Controller
{
    /**
     * Wyświetlenie listy samochodów
     * @throws Exception
     */
    public function listaAction()
    {
        $samochody = $this->loadModel('samochod');
        if(Auth::isLogged())
            $zalogowany = Auth::getLoggedUser();
        else
            $zalogowany['uzytkownik_id'] = 0;
        $params = array();
        foreach(array('kategoria', 'marka', 'nazwa', 'dostepne', 'ulubione') as $name)
            if($this->get($name)!=NULL)
                $params[$name]=$this->get($name);
        $this->params->samochody = $samochody->lista($zalogowany['uzytkownik_id'], $params, $this->get('ulubione'));
        $this->params->marki = $samochody->uzyteMarki();
        $this->params->kategorie = $samochody->kategorie();
        $this->params->filters = array('nazwa'=>$this->get('nazwa'), 'dostepne'=>$this->get('dostepne'), 'marka'=>$this->get('marka'), 'ulubione'=>$this->get('ulubione'));
    }
}