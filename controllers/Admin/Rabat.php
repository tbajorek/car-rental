<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do obsługi rabatów
 * Class RabatController
 * @package Admin
 */
class RabatController extends \Controller
{
    /**
     * zmienna przechowująca instancję załadowanego modelu
     * @var
     */
    private $model;

    /**
     * Konstruktor kontrolera, sprawdzający uprawienia administratora
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $zalogowany = \Auth::getLoggedUser();
        if(!\Auth::isLogged())
            $this->block('strona_glowna', 'Nie jesteś zalogowany');
        else if($zalogowany['admin']==false)
            $this->block('strona_glowna', 'Nie masz praw do oglądania tej zawartości');
        $this->model = $this->loadModel('Rabat');
    }

    /**
     * Wyświetlenie listy rabatów
     */
    public function listaAction()
    {
        $this->params->rabaty=$this->model->lista();
    }

    /**
     * Formularz dodawania nowego rabatu
     * @throws \Exception
     */
    public function dodajformAction()
    {
        $this->params->tryb='Dodawanie';
        $this->template='admin/rabat/form.html.twig';
        $this->params->rabaty=$this->model->lista();
        $this->params->samochody=$this->loadModel('Samochod')->lista();
    }

    /**
     * Dodawanie nowego rabatu
     */
    public function dodajAction()
    {
        $this->noRender=true;
        if($this->post('samochod')!=0)
            $samochod=$this->post('samochod');
        else
            $samochod=null;
        if($this->post('jednorazowy')!=null)
            $jednorazowy=true;
        else
            $jednorazowy=false;
        if($this->post('obowiazuje_od')!='')
            $obowiazuje_od=$this->post('obowiazuje_od');
        else
            $obowiazuje_od=null;
        if($this->post('obowiazuje_do')!='')
            $obowiazuje_do=$this->post('obowiazuje_do');
        else
            $obowiazuje_do=null;
        $this->model->dodaj($this->post('numer'),$samochod,str_replace(',','.',$this->post('obnizka')),$this->post('procentowo'),$jednorazowy,$obowiazuje_od,$obowiazuje_do);
        $this->setMessage('success','Dodawanie rabatu zakończyło się powodzeniem');
        $this->redirect('admin_lista_rabatow');
    }

    /**
     * Formularz edycji istniejącego rabatu
     * @throws \Exception
     */
    public function edytujformAction()
    {
        $this->params->tryb='Edycja';
        $this->template='admin/rabat/form.html.twig';
        $this->params->rabat=$this->model->pobierz($this->get('id'));
        $this->params->samochody=$this->loadModel('Samochod')->lista();
    }

    /**
     * Edycja istniejącego rabatu
     */
    public function edytujAction()
    {
        $this->noRender=true;
        if($this->post('samochod')!=0)
            $samochod=$this->post('samochod');
        else
            $samochod=null;
        if($this->post('jednorazowy')!=null)
            $jednorazowy=true;
        else
            $jednorazowy=false;
        if($this->post('obowiazuje_od')!='')
            $obowiazuje_od=$this->post('obowiazuje_od');
        else
            $obowiazuje_od=null;
        if($this->post('obowiazuje_do')!='')
            $obowiazuje_do=$this->post('obowiazuje_do');
        else
            $obowiazuje_do=null;
        $this->model->edytuj($this->get('id'),$samochod,str_replace(',','.',$this->post('obnizka')),$this->post('procentowo'),$jednorazowy,$obowiazuje_od,$obowiazuje_do);
        $this->setMessage('success','Edycja rabatu zakończyła się powodzeniem');
        $this->redirect('admin_lista_rabatow');
    }

    /**
     *  Usuwanie rabatu
     */
    public function usunAction()
    {
        $this->noRender=true;
        $this->model->usun($this->get('id'));
        $this->setMessage('success','Rabat został usunięty');
        $this->redirect('admin_lista_rabatow');
    }
}