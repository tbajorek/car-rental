<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do obsługi akcesoriów
 * Class AkcesoriumController
 * @package Admin
 */
class AkcesoriumController extends \Controller
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
     * Wyświetlenie listy akcesoriów
     * @throws \Exception
     */
    public function listaAction()
    {
        $model = $this->loadModel('Samochod');
        $this->params->akcesoria=$model->wszystkieOpcje();
    }

    /**
     * Formularz dodawania nowego akcesorium
     */
    public function dodajformAction()
    {
        $this->params->tryb='Dodawanie';
        $this->template='admin/akcesorium/form.html.twig';
    }

    /**
     * Dodawanie nowego akcesorium
     * @throws \Exception
     */
    public function dodajAction()
    {
        $this->noRender = true;
        $model = $this->loadModel('Samochod');
        $model->dodajOpcje($this->post('nazwa_akcesorium'),str_replace(',','.',$this->post('cena')),$this->post('cena_dziennie'));
        $this->setMessage('success','Nowe  akcesorium zostało dodane');
        $this->redirect('admin_lista_akcesoriow');
    }

    /**
     * Formularz edycji akcesorium
     * @throws \Exception
     */
    public function edytujformAction()
    {
        $this->params->tryb='Edycja';
        $this->template='admin/akcesorium/form.html.twig';
        $model=$this->loadModel('Samochod');
        $wynik=$model->jednaOpcja($this->get('id'));
        if($wynik==null)
        {
            $this->setMessage('error','Nie znaleziono akcesorium');
            $this->redirect('admin_lista_akcesoriow');
        }
        $this->params->akcesorium=$wynik;
    }

    /**
     * Edycja akcessorium
     * @throws \Exception
     */
    public function edytujAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        $model->edytujOpcje($this->get('id'),$this->post('nazwa_akcesorium'),str_replace(',','.',$this->post('cena')),$this->post('cena_dziennie'));
        $this->setMessage('success','Edycja akcesorium zakończyła się powodzeniem');
        $this->redirect('admin_lista_akcesoriow');
    }

    /**
     * Usunięcie akcesorium
     * @throws \Exception
     */
    public function usunAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        $model->usunOpcje($this->get('id'));
        $this->setMessage('success','Akcesorium zostało usunięte');
        $this->forward();
    }
}