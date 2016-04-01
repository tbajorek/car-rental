<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do obsługi profilu samochodu od strony zwykłego użytkownika
 * Class SamochodController
 */
class SamochodController extends Controller
{
    /**
     * Wyświetlenie profilu samochodu
     * @throws Exception
     */
    public function indexAction()
    {
        $model_samochodu = $this->loadModel('samochod');
        if(Auth::isLogged())
            $zalogowany = Auth::getLoggedUser();
        else
            $zalogowany['uzytkownik_id'] = 0;
        $samochod = $model_samochodu->dane($this->get('id'), $zalogowany['uzytkownik_id']);
        if($samochod===NULL)
            $this->block('strona_glowna','Nie znaleziono samochodu');

        $this->params->samochod = $samochod;

        $this->params->opcje = $model_samochodu->uzyteOpcje($samochod['samochod_id']);
    }

    /**
     * Wyświetlenie zdjęcia samochodu
     * @throws Exception
     */
    public function zdjecieAction()
    {
        $this->noRender = true;
        $samochod=$this->loadModel('Samochod');
        $zdjecie=$samochod->zdjecie($this->get('id'));
        header('Content-Type: '.$zdjecie['typ']);
        echo $zdjecie['zdjecie'];
    }

    /**
     * Dodanie nowej oceny samochodu
     * @throws Exception
     */
    public function ocenAction()
    {
        $this->noRender = true;
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $model = $this->loadModel('samochod');
        $wynik=$model->ocen(Auth::getLoggedUserId(), $this->get('id'), $this->get('ocena'));
        if($wynik==true)
            $this->setMessage('success', 'Oceniłeś samochód');
        else
            $this->setMessage('error', 'Nie możesz ocenić tego samochodu');
        $this->forward();
    }
}