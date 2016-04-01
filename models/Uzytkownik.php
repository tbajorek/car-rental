<?php

/**
 * Klasa modelu
 */

/**
 * Obsługuje dane o użytkownikach systemu
 * Class UzytkownikModel
 */
class UzytkownikModel extends Model
{
    /**
     * Sprawdza, czy użytkownik podał dobre hasło
     * @param string $email email yżytkownika
     * @param string $haslo zahaszowane hasło
     * @return bool
     */
    public function czyDobreHaslo($email, $haslo)
    {
        $query = 'SELECT czy_dobre_haslo(\''.$email.'\', \''.$haslo.'\');';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetch();
        $stmt->closeCursor();
        if($wynik['czy_dobre_haslo'])
        {
            $query = 'SELECT uzytkownik_id, pesel, email, haslo, imie, nazwisko, ulica, kod_pocztowy, miejscowosc FROM uzytkownicy WHERE email=\''.$email.'\';';
            $stmt = $this->connector->query($query);
            $wynik = $stmt->fetchAll();
            $stmt->closeCursor();
            return $wynik[0];
        }
        return false;
    }

    /**
     * Dodaje nowego użytkownika
     * @param string $email adres e-mail użytkownika
     * @param string $haslo zahaszowane hasło
     * @param string $imie imię
     * @param string $nazwisko nazwisko
     * @param string $pesel numer PESEL
     * @param string $ulica nazwa ulicy wraz z numerem domu
     * @param string $kod_pocztowy kod pocztowy
     * @param string $miejscowosc nazwa miejscowości
     * @return mixed
     */
    public function nowy($email, $haslo,$imie,$nazwisko,$pesel,$ulica,$kod_pocztowy,$miejscowosc)
    {
        $query = 'SELECT * FROM dodaj_uzytkownika(\''.$pesel.'\', \''.$email.'\', \''.$haslo.'\', \''.$imie.'\', \''.$nazwisko.'\', \''.$ulica.'\', \''.$kod_pocztowy.'\', \''.$miejscowosc.'\');';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetch();
        $stmt->closeCursor();
        return $wynik[0];
    }

    /**
     * Aktywuje konto użytkownika
     * @param string $email adres e-mail użytkownika
     * @param string $kod kod aktywacyjny
     * @return bool
     */
    public function aktywuj($email,$kod)
    {
        $query='SELECT * FROM aktywuj_konto(:email,:kod)';
        $stmt = $this->connector->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':kod', $kod, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Pobiera listę użytkowników
     * @return array
     */
    public function lista()
    {
        $query = 'SELECT * FROM lista_uzytkownikow;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }
}