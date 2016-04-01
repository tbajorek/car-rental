<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do obsługi kategorii
 * Class KategoriaController
 * @package Admin
 */
class KategoriaController extends \Controller
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
     * Wyświetla listę kategorii
     * @throws \Exception
     */
    public function listaAction()
    {
        $model = $this->loadModel('Samochod');
        $this->params->kategorie=$model->kategorie();
    }

    /**
     * Formularz dodawania nowej kategorii
     */
    public function dodajformAction()
    {
        $this->params->tryb='Dodawanie';
        $this->template='admin/kategoria/form.html.twig';
    }

    /**
     * Dodawanie nowej kategorii
     * @throws \Exception
     */
    public function dodajAction()
    {
        $this->noRender = true;
        $model = $this->loadModel('Samochod');
        $model->dodajKategorie($this->post('nazwa_kategorii'));
        $this->setMessage('success','Nowa kategoria została dodana');
        $this->redirect('admin_lista_kategorii');
    }

    /**
     * Formularz edycji istniejącej kategorii
     * @throws \Exception
     */
    public function edytujformAction()
    {
        $this->params->tryb='Edycja';
        $this->template='admin/kategoria/form.html.twig';
        $model=$this->loadModel('Samochod');
        $wynik=$model->kategoria($this->get('id'));
        if($wynik==null)
        {
            $this->setMessage('error','Nie znaleziono kategorii');
            $this->redirect('admin_lista_kategorii');
        }
        $this->params->kategoria=$wynik;
    }

    /**
     * Edycja istniejącej kategorii
     * @throws \Exception
     */
    public function edytujAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        $model->edytujKategorie($this->get('id'),$this->post('nazwa_kategorii'));
        $this->setMessage('success','Edycja kategorii zakończyła się powodzeniem');
        $this->redirect('admin_lista_kategorii');
    }

    /**
     * Usunięcie kategorii
     * @throws \Exception
     */
    public function usunAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        try
        {
            $model->usunKategorie($this->get('id'));
        }
        catch(\PDOException $e)
        {
            $this->setMessage('error','Nie udało się usunąć kategorii. Być może w bazie istnieją samochody, do których jest przypisana.');
            $this->forward();
        }
        $this->setMessage('success','Kategoria została usunięta');
        $this->forward();
    }
}