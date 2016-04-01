<?php

/**
 * Klasa modelu
 */

/**
 * Obsługuje rezerwacje
 * Class RezerwacjaModel
 */
class RezerwacjaModel extends Model
{
    /**
     * Pobiera listę wszystkich rezerwacji dla wszystkich użytkowników
     * @return array
     */
    public function wszystkie()
    {
        $query = 'SELECT * FROM lista_rezerwacji;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobiera listę wszystkich rezerwacji dla konkretnego użytkownika
     * @param int $uzytkownik_id numer id użytkownika
     * @return array
     */
    public function lista($uzytkownik_id)
    {
        $query = 'SELECT * FROM lista_rezerwacji WHERE uzytkownik_id='.$uzytkownik_id.';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Dodaje nową rezerwację do systemu
     * @param int $uzytkownik numer id użytkownika dodającego rezerwację
     * @param int $pojazd numer id rezerwowanego pojazdu
     * @param string $data_od początek okresu rezerwacji
     * @param string $data_do koniec okresu rezerwacji
     * @param string $rabat kod rabatowy
     * @param array $akcesoria tablica z akcesoriami rezerwowanymi razem z samochodem
     * @return bool
     * @throws Exception
     */
    public function dodaj($uzytkownik, $pojazd, $data_od, $data_do, $rabat='', $akcesoria=array())
    {
        if(empty($rabat))$rabat=NULL;
        $this->connector->exec('BEGIN');
        $stmt = $this->connector->prepare('SELECT * FROM dodaj_rezerwacje(:uzytkownik,:samochod,:data_od,:data_do,:rabat);');
        $stmt->bindValue(':uzytkownik', $uzytkownik, PDO::PARAM_INT);
        $stmt->bindValue(':samochod', $pojazd, PDO::PARAM_INT);
        $stmt->bindValue(':data_od', $data_od, PDO::PARAM_STR);
        $stmt->bindValue(':data_do', $data_do, PDO::PARAM_STR);
        if($rabat===NULL)
            $stmt->bindValue(':rabat', $rabat, PDO::PARAM_INT);
        else
            $stmt->bindValue(':rabat', $rabat, PDO::PARAM_STR);
        try
        {
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            $this->connector->exec('ROLLBACK');
            throw new Exception('Nie udało się zapisać rezerwacji tego samochodu w podanym okresie');
        }
        $wynik = $stmt->fetchAll();
        $id = $wynik[0]['dodaj_rezerwacje'];
        if($id > 0)
        {
            foreach($akcesoria as $akcesorium)
            {
                $stmt = $this->connector->prepare('INSERT INTO rezerwacje_akcesoria VALUES(:rezerwacja,:akcesorium);');
                $stmt->bindValue(':rezerwacja', $id, PDO::PARAM_INT);
                $stmt->bindValue(':akcesorium', $akcesorium, PDO::PARAM_INT);
                $ilosc = $stmt->execute();
                if ($ilosc < 1)
                {
                    $this->connector->exec('ROLLBACK');
                    return false;
                }
            }
            $this->connector->exec('COMMIT');
            return $id;
        }
        else
        {
            $this->connector->exec('ROLLBACK');
            return false;
        }
    }

    /**
     * Usuwa rezerwację z bazy
     * @param int $rezerwacja_id numer id rezerwacji
     * @param int $uzytkownik numer id użytkownika
     * @param bool $admin
     * @return bool
     */
    public function usun($rezerwacja_id, $uzytkownik, $admin=false)
    {
        $query='DELETE FROM rezerwacje WHERE rezerwacja_id=:rezerwacja';
        if($admin===false)
            $query.=' AND uzytkownik=:uzytkownik';
        $query.=';';
        $stmt = $this->connector->prepare($query);
        $stmt->bindValue(':rezerwacja', $rezerwacja_id, PDO::PARAM_INT);
        if($admin===false)
            $stmt->bindValue(':uzytkownik', $uzytkownik, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Akceptuje rezerwację - ustawia ją jako wypożyczenie
     * @param int $rezerwacja_id numer id rezerwacji
     * @return bool
     */
    public function akceptuj($rezerwacja_id=-1)
    {
        $query='SELECT * FROM wypozycz_samochod(:rezerwacja);';
        $stmt = $this->connector->prepare($query);
        $stmt->bindValue(':rezerwacja', $rezerwacja_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}