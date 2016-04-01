<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do obsługi logowania
 * Class LoginController
 */
class LoginController extends Controller
{
    /**
     * Wyświetlenie formularza logowania
     */
    public function formAction()
    {

    }

    /**
     * Zalogowanie użytkownika
     * @return bool
     * @throws Exception
     */
    public function zalogujAction()
    {
        $this->noRender = true;
        if(Auth::isLogged())
        {
            $this->setMessage('error', 'Jesteś już zalogowany');
            $this->redirect('strona_glowna');
            return true;
        }
        $uzytkownik = $this->loadModel('Uzytkownik');
        if(($zalogowany = $uzytkownik->czyDobreHaslo($this->post('email'), hash('sha256', $this->post('haslo'))))!==false)
        {
            if($zalogowany['email']===$this->config->get('admin.email'))
                $zalogowany['admin'] = true;
            else
                $zalogowany['admin'] = false;
            Auth::login($zalogowany);
            $this->setMessage('success', 'Zostałeś pomyślnie zalogowany');
            $this->redirect('strona_glowna');
        }
        else
        {
            $this->setMessage('error', 'Podałeś błędne dane');
            $this->redirect('login_form');
        }
    }

    /**
     * Wylogowanie użytkownika
     */
    public function wylogujAction()
    {
        $this->noRender = true;
        Auth::logout();
        $this->setMessage('success', 'Zostałeś pomyślnie wylogowany');
        $this->redirect('strona_glowna');
    }
}