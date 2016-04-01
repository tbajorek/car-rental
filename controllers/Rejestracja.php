<?php

/**
 * Klasa kontrolera systemu
 */

/**
 * Kontroler do obsługi rejestracji użytkownika
 * Class RejestracjaController
 */
class RejestracjaController extends Controller
{
    /**
     * Wyświetlenie formularza rejestracji
     */
    public function indexAction()
    {

    }

    /**
     * Rejestracja nowego użytkownika
     * @throws Exception
     */
    public function rejestrujAction()
    {
        $this->noRender = true;
        $uzytkownik = $this->loadModel('Uzytkownik');
        $kod=$uzytkownik->nowy($this->post('email'),hash('sha256', $this->post('haslo')),$this->post('imie'),$this->post('nazwisko'),$this->post('pesel'),$this->post('ulica'),$this->post('kod_pocztowy'),$this->post('miejscowosc'));
        $wiadomosc = $this->renderHelper('rejestracja/mail.html.twig', array('imie'=>$this->post('imie'), 'nazwisko'=>$this->post('nazwisko'),'kod'=>$kod,'email'=>$this->post('email')));
        $naglowki = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n";
        if(mail($this->post('email'), "Potwierdzenie rejestracji", $wiadomosc, $naglowki))
                $this->setMessage('success', 'Zostałeś pomyślnie zarejestrowany. Potwierdź adres e-mail i zacznij korzystać z wypożyczalni.');
        else
        {
                $uzytkownik->aktywuj($this->post('email'), $kod);
                $this->setMessage('warning', 'Nie udało się wysłać wiadomości potwierdzającej. Być może na serwerze jest źle skonfigurowane oprogramowanie do wysyłania wiadomości e-mail. Tymczasowo wyłączono mechanizm potwierdzania adresu - możesz się już zalogować.');
        }
        $this->redirect('strona_glowna');
    }

    /**
     * Potwierdzenie adresu e-mail kodem aktywacyjnym
     * @throws Exception
     */
    public  function potwierdzAction()
    {
        $this->noRender = true;
        $uzytkownik = $this->loadModel('Uzytkownik');
        $uzytkownik->aktywuj($this->get('email'),$this->get('kod'));
        $this->setMessage('success', 'Email potwierdzony, możesz się zalogować');
        $this->redirect('login_form');
    }
}
