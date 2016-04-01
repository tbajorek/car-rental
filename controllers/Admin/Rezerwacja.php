<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do zarządzania rezerwacjami przez administratora
 * Class RezerwacjaController
 * @package Admin
 */
class RezerwacjaController extends \Controller
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
     * Wyświetlenie listy rezerwacji wszystkich użytkowników
     * @throws \Exception
     */
    public function listaAction()
    {
        $rezerwacje = $this->loadModel('rezerwacja');
        $this->params->rezerwacje = $rezerwacje->wszystkie();
    }

    /**
     * Akceptacja rezerwacji - zamienia się ona w wypożyczenie
     * @throws \Exception
     */
    public function akceptujAction()
    {
        $rezerwacje = $this->loadModel('rezerwacja');
        try
        {
            $rezerwacje->akceptuj($this->get('id'));
        }
        catch(\PDOException $e)
        {
            $this->setMessage('error', $e->getMessage());
            $this->forward();
            die();
        }
        $this->setMessage('success', 'Wypożyczenie zostało zatwierdzone');
        $this->redirect('admin_lista_rezerwacji');
    }

    /**
     * Usunięcie rezerwacji z bazy
     * @throws \Exception
     */
    public function usunAction()
    {
        $this->noRender = true;
        $zalogowany = \Auth::getLoggedUser();
        $rezerwacja = $this->loadModel('Rezerwacja');
        if($rezerwacja->usun($this->get('id'),$zalogowany['uzytkownik_id'], $zalogowany['admin']))
        {
            $this->setMessage('success', 'Rezerwacja została usunięta');
            $this->redirect('admin_lista_rezerwacji');
        }
        else
        {
            $this->setMessage('error', 'Nie udało się usunąć rezerwacji');
            $this->forward();
        }
    }
}