<?php

/**
 * Klasa modelu
 */

/**
 * Obsługuje wypożyczenia
 * Class WypozyczenieModel
 */
class WypozyczenieModel extends Model
{
    /**
     * Pobiera informacje o wypożyczeniu
     * @param int $id numer id wypożyczenia
     * @return array
     */
    public function pobierz($id=0)
    {
        $query = 'SELECT * FROM lista_wypozyczen WHERE wypozyczenie_id='.$id.';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik[0];
    }

    /**
     * Pobiera listę wypożyczeń
     * @return array
     */
    public function lista()
    {
        $query = 'SELECT * FROM lista_wypozyczen;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Kończy wypożyczenie - oddanie samochodu
     * @param int $wypozyczenie numer id wypożyczenia
     * @param string $data_oddania data oddania samochodu
     * @param float $przebieg stan licznika w momencie oddania
     */
    public function zakoncz($wypozyczenie,$data_oddania,$przebieg)
    {
        $query = 'SELECT * FROM zakoncz_wypozyczenie('.$wypozyczenie.',\''.$data_oddania.'\','.str_replace(',','.',$przebieg).');';
        $this->connector->exec($query);
    }

    /**
     * Usunięcie wypożyczenia - bez jego zakończenia
     * @param int $wypozyczenie numer id wypożyczenia
     * @return bool
     */
    public function usun($wypozyczenie=-1)
    {
        $query='DELETE FROM wypozyczenia WHERE wypozyczenie_id=:wypozyczenie';
        $stmt = $this->connector->prepare($query);
        $stmt->bindValue(':wypozyczenie', $wypozyczenie, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Pobiera informacje o aktualnym wypożyczeniu użytkownika
     * @param int $uzytkownik numer id użytkownika
     * @return mixed
     */
    public function aktualnie($uzytkownik=0)
    {
        $query = 'SELECT * FROM lista_wypozyczen WHERE uzytkownik_id='.$uzytkownik.' AND (NOW() BETWEEN data_od AND termin_zwrotu);';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik[0];
    }
}