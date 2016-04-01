<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do zarządzania aaktualnymi wypożyczeniami przez administratora
 * Class WypozyczenieController
 * @package Admin
 */
class WypozyczenieController extends \Controller
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

    /** Wyświetlenie listy aktualnych wypożyczeń
     * @throws \Exception
     */
    public function listaAction()
    {
        $wypozyczenia = $this->loadModel('wypozyczenie');
        $this->params->wypozyczenia = $wypozyczenia->lista();
    }

    /**
     * Formularz zakończenia wypożyczenia
     * @throws \Exception
     */
    public function zakformAction()
    {
        $wypozyczenia = $this->loadModel('wypozyczenie');
        $this->params->wypozyczenie = $wypozyczenia->pobierz($this->get('id'));
    }

    /**
     * Zakończenie wypożyczenia - potwierdzenie oddania samochodu
     * @throws \Exception
     */
    public function zakonczAction()
    {
        $wypozyczenia = $this->loadModel('wypozyczenie');
        try
        {
            $wypozyczenia->zakoncz($this->get('id'), $this->post('data_zwrotu'), $this->post('przebieg'));
        }
        catch(\PDOException $e)
        {
            $this->setMessage('error', $e->getMessage());
            $this->forward();
            die();
        }
        $this->setMessage('success', 'Wypożyczenie zostało zakonczone');
        $this->redirect('admin_lista_wypozyczen');
    }

    /**
     * Usuwanie wypożyczenia - nie jest tożsame z oddaniem samochodu
     * @throws \Exception
     */
    public function usunAction()
    {
        $wypozyczenia = $this->loadModel('wypozyczenie');
        try
        {
            $wypozyczenia->usun($this->get('id'));
        }
        catch(\PDOException $e)
        {
            $this->setMessage('error', 'Nie udało się usunąć wypożyczenia');
            $this->forward();
            die();
        }
        $this->setMessage('success', 'Wypożyczenie zostało usunięte');
        $this->redirect('admin_lista_wypozyczen');
    }
}