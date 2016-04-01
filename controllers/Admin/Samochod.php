<?php

/**
 * Klasa kontrolera panelu administracyjnego
 */

namespace Admin;

/**
 * Kontroler do zarządzania samochodami przez administratora
 * Class SamochodController
 * @package Admin
 */
class SamochodController extends \Controller
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
     * Formularz dodawania nowego samochodu
     * @throws \Exception
     */
    public function dodajformAction()
    {
        $this->params->tryb='Dodawanie';
        $model = $this->loadModel('Samochod');
        $this->commonForm();
        $this->params->opcje=$model->wszystkieOpcje();
    }

    /**
     * Dodawanie nowego samochodu
     * @throws \Exception
     */
    public function dodajAction()
    {
        $this->noRender = true;
        $model = $this->loadModel('Samochod');
        if($_FILES['zdjecie']['size'] > 0)
        {
            $fp = fopen($_FILES['zdjecie']['tmp_name'], 'r');
            $zdjecie = fread($fp, filesize($_FILES['zdjecie']['tmp_name']));
            fclose($fp);
            $typ = $_FILES['zdjecie']['type'];
        }
        else
        {
            $zdjecie = NULL;
            $typ = NULL;
        }
        $model->dodaj($this->post('marka'),$this->post('nazwa'),$this->post('kategoria'),$this->post('rocznik'),$this->post('pojemnosc'),$this->post('liczba_drzwi'),str_replace(',','.',$this->post('przebieg')),str_replace(',','.',$this->post('cena')),$this->post('opis'),$zdjecie,$typ,$this->post('opcje'));
        $this->setMessage('success','Samochod został dodany');
        $this->redirect('lista_samochodow');
    }

    /**
     * Formularz edycji istniejącego samochodu
     * @throws \Exception
     */
    public function edytujformAction()
    {
        $this->params->tryb='Edycja';
        $this->commonForm();
        $model=$this->loadModel('Samochod');
        $this->params->opcje=$model->wszystkieOpcje($this->get('id'));
        if(\Auth::isLogged())
            $zalogowany = \Auth::getLoggedUser();
        else
            $zalogowany['uzytkownik_id'] = 0;
        $samochod = $model->dane($this->get('id'), $zalogowany['uzytkownik_id']);
        if($samochod===NULL)
            $this->block('Nie znaleziono samochodu', 'strona_glowna');

        $this->params->samochod = $samochod;
    }

    /**
     * Edycja istniejącego samochodu
     * @throws \Exception
     */
    public function edytujAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        if($_FILES['zdjecie']['size'] > 0)
        {
            $fp = fopen($_FILES['zdjecie']['tmp_name'], 'r');
            $zdjecie = fread($fp, filesize($_FILES['zdjecie']['tmp_name']));
            fclose($fp);
            $typ = $_FILES['zdjecie']['type'];
        }
        else if($this->post('usun_zdjecie')==true)
        {
            $zdjecie = -1;
            $typ = NULL;
        }
        else
        {
            $zdjecie = NULL;
            $typ = NULL;
        }
        $model->edytuj($this->get('id'),$this->post('marka'),$this->post('nazwa'),$this->post('kategoria'),$this->post('rocznik'),$this->post('pojemnosc'),$this->post('liczba_drzwi'),str_replace(',','.',$this->post('przebieg')),str_replace(',','.',$this->post('cena')),$this->post('opis'),$zdjecie,$typ,$this->post('opcje'));
        $this->setMessage('success','Edycja samochodu zakończyła się powodzeniem');
        $this->redirect('profil_samochodu',array('id'=>$this->get('id')));
    }

    /**
     * Usuwanie samochodu
     * @throws \Exception
     */
    public function usunAction()
    {
        $this->noRender=true;
        $model = $this->loadModel('Samochod');
        $model->usun($this->get('id'));
        $this->setMessage('success','Samochod został usunięty');
        $this->forward();
    }

    /**
     * Pomocnicza funkcja do formularzy
     * @throws \Exception
     */
    private function commonForm()
    {
        $this->template='admin/samochod/form.html.twig';
        $model=$this->loadModel('Samochod');
        $this->params->marki=$model->wszystkieMarki();
        $this->params->kategorie=$model->kategorie();
    }
}