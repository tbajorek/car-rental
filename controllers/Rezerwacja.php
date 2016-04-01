<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do obsługi rezerwacji użytkownika
 * Class RezerwacjaController
 */
class RezerwacjaController extends Controller
{
    /**
     * Wyświetlenie listy z rezerwacjami użytkownika
     * @throws Exception
     */
    public function listaAction()
    {
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $model = $this->loadModel('Rezerwacja');
        $this->params->rezerwacje = $model->lista(Auth::getLoggedUserId());
    }

    /**
     * Wyświetlenie formularza dodania  nowej rezerwacji
     * @throws Exception
     */
    public function formAction()
    {
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $samochody = $this->loadModel('samochod');
        $zalogowany = Auth::getLoggedUser();
        $wynik = $samochody->lista($zalogowany['uzytkownik_id']);
        $this->params->samochody = $wynik;
        $opcje = array();
        $samochod = array();
        if($this->get('id')!=NULL)
        {

            foreach($wynik as $pojazd)
                if($pojazd['samochod_id']==$this->get('id'))
                {
                    $opcje = $samochody->opcje($pojazd['samochod_id']);
                    $samochod = $pojazd;
                }
        }
        $this->params->opcje = $opcje;
        $this->params->wybrany_samochod = $samochod;
    }

    /**
     * Dodanie nowej rezerwacji
     * @throws Exception
     */
    public function rezerwujAction()
    {
        $this->noRender = true;
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $zalogowany = Auth::getLoggedUser();
        $rezerwacja = $this->loadModel('Rezerwacja');
        try
        {
            $rezultat = $rezerwacja->dodaj($zalogowany['uzytkownik_id'], $this->post('pojazd'), $this->post('data_od'), $this->post('data_do'), $this->post('rabat'), $this->post('akcesorium'));
        }
        catch(Exception $e)
        {
            $this->setMessage('error', $e->getMessage());
            $this->forward();
            die();
        }
        if($rezultat!==false)
        {
            $this->setMessage('success', 'Rezerwacja została zapisana');
            $this->redirect('lista_rezerwacji');
        }
        else
        {
            $this->setMessage('error', 'Nie udało się zapisać rezerwacji');
            $this->forward();
        }
    }

    /**
     * Usunięcie rezerwacji
     * @throws Exception
     */
    public function usunAction()
    {
        $this->noRender = true;
        if(!Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        $zalogowany = Auth::getLoggedUser();
        $rezerwacja = $this->loadModel('Rezerwacja');
        if($rezerwacja->usun($this->get('id'),$zalogowany['uzytkownik_id'], $zalogowany['admin']))
        {
            $this->setMessage('success', 'Rezerwacja została usunięta');
            $this->redirect('lista_rezerwacji');
        }
        else
        {
            $this->setMessage('error', 'Nie udało się usunąć rezerwacji');
            $this->forward();
        }
    }
}