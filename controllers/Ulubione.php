<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do obsługi ulubionych
 * Class UlubioneController
 */
class UlubioneController extends Controller
{
    /**
     * Polubienie samochodu
     * @throws Exception
     */
    public function polubAction()
    {
        $this->noRender = true;
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $zalogowany = Auth::getLoggedUser();
        $model = $this->loadModel('samochod');
        $model->polub($zalogowany['uzytkownik_id'], $this->get('id'));
        $this->setMessage('success', 'Samochód został dodany do ulubionych');
        $this->forward();
    }

    /**
     * Cofnięcie polubienia samochodu
     * @throws Exception
     */
    public function odlubAction()
    {
        $this->noRender = true;
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $zalogowany = Auth::getLoggedUser();
        $model = $this->loadModel('samochod');
        $model->odlub($zalogowany['uzytkownik_id'], $this->get('id'));
        $this->setMessage('success', 'Samochód został usunięty z ulubionych');
        $this->forward();
    }
}