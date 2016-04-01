<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do obsługi marek pojazdów
 * Class MarkaController
 * @package Admin
 */
class MarkaController extends \Controller
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
     * Wyświetlenie listy marek
     * @throws \Exception
     */
    public function listaAction()
    {
        $model = $this->loadModel('Samochod');
        $this->params->marki=$model->wszystkieMarki();
    }

    /**
     * Formularz dodawania nowej marki
     * @throws \Exception
     */
    public function dodajformAction()
    {
        $this->params->tryb='Dodawanie';
        $this->template='admin/marka/form.html.twig';
        $this->params->panstwa=$this->loadModel('Panstwo')->lista();
    }

    /**
     * Dodawanie nowej marki
     * @throws \Exception
     */
    public function dodajAction()
    {
        $this->noRender = true;
        $model = $this->loadModel('Samochod');
        $model->dodajMarke($this->post('nazwa_marki'),$this->post('panstwo'));
        $this->setMessage('success','Nowa marka została dodana');
        $this->redirect('admin_lista_marek');
    }

    /**
     * Formularz edycji istniejącej marki
     * @throws \Exception
     */
    public function edytujformAction()
    {
        $this->params->tryb='Edycja';
        $this->template='admin/marka/form.html.twig';
        $model=$this->loadModel('Samochod');
        $wynik=$model->jednaMarka($this->get('id'));
        if($wynik==null)
        {
            $this->setMessage('error','Nie znaleziono marki');
            $this->redirect('admin_lista_marek');
        }
        $this->params->marka=$wynik;
        $this->params->panstwa=$this->loadModel('Panstwo')->lista();
    }

    /**
     * Edycja istniejącej marki
     * @throws \Exception
     */
    public function edytujAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        $model->edytujMarke($this->get('id'),$this->post('nazwa_marki'),$this->post('panstwo'));
        $this->setMessage('success','Edycja marki zakończyła się powodzeniem');
        $this->redirect('admin_lista_marek');
    }

    /**
     * Usunięcie marki
     * @throws \Exception
     */
    public function usunAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        try
        {
            $model->usunMarke($this->get('id'));
        }
        catch(\PDOException $e)
        {
            $this->setMessage('error','Nie udało się usunąć marki. Być może w bazie istnieją samochody, do których jest przypisana.');
            $this->forward();
        }
        $this->setMessage('success','Marka została usunięta');
        $this->forward();
    }
}