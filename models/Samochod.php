<?php

/**
 * Klasa modelu
 */

/**
 * Obsługuje dane samochodów oraz inne informacje z nimi związane
 * Class SamochodModel
 */
class SamochodModel extends Model
{
    /**
     * Pobiera listę samochodów według podanych kryteriów
     * @param int $uzytkownik numer id użytkownika, dla którego są pobierane samochody
     * @param array $params parametry filtrów
     * @param bool $ulubione flaga, czy mają być wyświetlone samochody tylko spośród ulubionych użytkownika
     * @return array
     */
    public function lista($uzytkownik=0, $params=array(), $ulubione=false)
    {
        if($ulubione!=true)
            $query = 'SELECT * FROM lista_samochodow LEFT JOIN ulubione ON samochod=samochod_id AND uzytkownik='.$uzytkownik;
        else
            $query = 'SELECT * FROM lista_ulubionych';
        if(count($params)>0)
        {
            $warunki = array();
            if(isset($params['ulubione']))
                $warunki[] = 'uzytkownik='.$uzytkownik;
            if(isset($params['kategoria']))
                $warunki[] = 'kategoria='.$params['kategoria'];
            if(isset($params['marka']))
                $warunki[] = 'marka='.$params['marka'];
            if(isset($params['dostepne'])&&$params['dostepne']=='1')
                $warunki[] = 'dostepny=true';
            if(isset($params['nazwa']))
                $warunki[] = '(nazwa_samochodu LIKE \'%'.$params['nazwa'].'%\' OR nazwa_kategorii LIKE \'%'.$params['nazwa'].'%\')';
            $query.=' WHERE '.implode(' AND ', $warunki);
        }
        $query.=';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobliera listę marek, które mają przypisany chociaż jeden pojazd
     * @return array
     */
    public function uzyteMarki()
    {
        $query = 'SELECT * FROM uzyte_marki;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobiera listę wszystkich marek z bazy
     * @return array
     */
    public function wszystkieMarki()
    {
        $query = 'SELECT * FROM lista_marek;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobiera listę akcesoriów(opcji) dostępnych dla danego samochodu
     * @param int $samochod numer id samochodu
     * @return array
     */
    public function uzyteOpcje($samochod)
    {
        $query = 'SELECT * FROM akcesoria_samochodu WHERE samochod='.$samochod.';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobiera listę wsyzstkich akcesoriów, pozwala odczytać, czy samochód posiada daną opcję
     * @param int/null $samochod numer id samochodu
     * @return array
     */
    public function wszystkieOpcje($samochod=NULL)
    {
        if($samochod===NULL)
            $query = 'SELECT * FROM akcesoria;';
        else
            $query = 'SELECT akcesorium_id,nazwa_akcesorium,cena,cena_dziennie,czy_samochod_ma_opcje('.$samochod.',akcesorium_id) AS selected FROM akcesoria;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobiera listę kategorii
     * @return array
     */
    public function kategorie()
    {
        $query = 'SELECT * FROM kategorie;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobiera dane o samochodzie dla użytkownika
     * @param int $id numer id samochodu
     * @param int $uzytkownik numer id użytkownika
     * @return array/null
     */
    public function dane($id, $uzytkownik)
    {
        $query = 'SELECT * FROM lista_samochodow LEFT JOIN ulubione ON samochod=samochod_id AND uzytkownik=' . $uzytkownik . ' WHERE samochod_id=' . $id . ';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        if (count($wynik) == 1)
            return $wynik[0];
        else
            return NULL;
    }

    /**
     * Pobiera informacje o akcesorium (opcji)
     * @param int $opcja numer id akcesorium
     * @return array/null
     */
    public function jednaOpcja($opcja)
    {
        $stmt = $this->connector->query('SELECT * FROM akcesoria WHERE akcesorium_id=' . $opcja . ';');
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        if (count($wynik)>0)
            return $wynik[0];
        else
            return NULL;
    }

    /**
     * Dodaje nowe akcesorium (opcję) do bay
     * @param string $nazwa nazwa akccesorium
     * @param float $cena cena za wypozyczenie
     * @param bool $cena_dziennie flaga, czy cena ma być rozliczana dziennie, czy jednorazowo
     */
    public function dodajOpcje($nazwa,$cena,$cena_dziennie)
    {
        $query = 'SELECT * FROM dodaj_opcje(\''.$nazwa.'\', '.$cena.','.$cena_dziennie.');';
        $this->connector->exec($query);
    }

    /**
     * Edytuje istniejące akcesorium (opcję)
     * @param int $opcja numer id akcesorium
     * @param string $nazwa nazwa akcesorium
     * @param float $cena cena za wypozyczenie
     * @param bool $cena_dziennie flaga, czy cena ma być rozliczana dziennie, czy jednorazowo
     */
    public function edytujOpcje($opcja,$nazwa,$cena,$cena_dziennie)
    {
        $query = 'SELECT * FROM edytuj_opcje(\''.$opcja.'\', \''.$nazwa.'\', '.$cena.', '.$cena_dziennie.');';
        $this->connector->exec($query);
    }

    /**
     * Usuwa podane akcesorium (opcję)
     * @param int $opcja numer id akcesorium
     */
    public function usunOpcje($opcja)
    {
        $query = 'SELECT * FROM usun_opcje('.$opcja.');';
        $this->connector->exec($query);
    }

    /**
     * Pobiera informację o danej marce
     * @param int $marka numer id marki
     * @return array/null
     */
    public function jednaMarka($marka)
    {
        $stmt = $this->connector->query('SELECT * FROM lista_marek WHERE marka_id=' . $marka . ';');
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        if (count($wynik)>0)
            return $wynik[0];
        else
            return NULL;
    }

    /**
     * Dodaje nową markę do bazy
     * @param string $nazwa nazwa marki
     * @param int $kraj numer id kraju, z którego pochodzi marka
     */
    public function dodajMarke($nazwa,$kraj)
    {
        $query = 'SELECT * FROM dodaj_marke(\''.$nazwa.'\', '.$kraj.');';
        $this->connector->exec($query);
    }

    /**
     * Edytuje istniejącą markę
     * @param int $marka numer id marki
     * @param string $nazwa nazwa marki
     * @param int $kraj numer id kraju, z którego pochodzi marka
     */
    public function edytujMarke($marka,$nazwa,$kraj)
    {
        $query = 'SELECT * FROM edytuj_marke('.$marka.', \''.$nazwa.'\', '.$kraj.');';
        $this->connector->exec($query);
    }

    /**
     * Usuwa markę
     * @param int $marka numer id marki
     */
    public function usunMarke($marka)
    {
        $query = 'SELECT * FROM usun_marke('.$marka.');';
        $this->connector->exec($query);
    }

    /**
     * Pobiera informacje o kategorii
     * @param int $kategoria numer id kategorii
     * @return array/null
     */
    public function kategoria($kategoria)
    {
        $stmt = $this->connector->query('SELECT * FROM kategorie WHERE kategoria_id=' . $kategoria . ';');
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        if (count($wynik)>0)
            return $wynik[0];
        else
            return NULL;
    }

    /**
     * Dodaje nową kategorię
     * @param string $nazwa_kategorii nazwa kategorii
     */
    public function dodajKategorie($nazwa_kategorii)
    {
        $query = 'SELECT * FROM dodaj_kategorie(\''.$nazwa_kategorii.'\');';
        $this->connector->exec($query);
    }

    /**
     * Edytuje istniejącą kategorię
     * @param int $kategoria numer id kategorii
     * @param string $nazwa_kategorii nazwa kategorii
     */
    public function edytujKategorie($kategoria,$nazwa_kategorii)
    {
        $query = 'SELECT * FROM edytuj_kategorie('.$kategoria.', \''.$nazwa_kategorii.'\');';
        $this->connector->exec($query);
    }

    /**
     * Usuwa kategorię z bazy
     * @param int $kategoria numer id kategorii
     */
    public function usunKategorie($kategoria)
    {
        $query = 'SELECT * FROM usun_kategorie('.$kategoria.');';
        $this->connector->exec($query);
    }

    /**
     * Pobiera z bazy zdjęcie samochodu
     * @param int $samochod_id numer id samochodu
     * @return array
     */
    public function zdjecie($samochod_id)
    {
        $query = 'SELECT * FROM zdjecia WHERE samochod_id=' . $samochod_id . ';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        if (count($wynik)>0)
        {
            $wynik[0]['zdjecie']=base64_decode(stream_get_contents($wynik[0]['zdjecie']));
            return $wynik[0];
        }
        else
            return NULL;
    }

    /**
     * Zapisuje polubienie ssamochodu przez użytkownika
     * @param int $uzytkownik_id numer id użytkownika
     * @param int $samochod_id numer id samochodu
     */
    public function polub($uzytkownik_id, $samochod_id)
    {
        $query = 'SELECT polub(\''.$uzytkownik_id.'\', \''.$samochod_id.'\');';
        $this->connector->exec($query);
    }

    /**
     * Usuwa polubienie ssamochodu przez użytkownika
     * @param int $uzytkownik_id numer id użytkownika
     * @param int $samochod_id numer id samochodu
     */
    public function odlub($uzytkownik_id, $samochod_id)
    {
        $query = 'SELECT odlub(\''.$uzytkownik_id.'\', \''.$samochod_id.'\');';
        $this->connector->exec($query);
    }

    /**
     * Dodaje nowy samochód
     * @param int $marka marka samochodu
     * @param string $nazwa_samochodu nazwa dodawanego samochodu
     * @param int $kategoria numer id kategorii, do której należy samochód
     * @param int $rocznik rok produkcji samochodu
     * @param int $pojemnosc pojemność silnika w cm3
     * @param int $liczba_drzwi liczba drzwi w samochodze
     * @param float $przebieg dotychczasowy przebieg w km
     * @param float $cena cena za jeden dzień wypożyczenia w PLN
     * @param string/null $opis opis samochodu
     * @param string/null $zdjecie zawartość pliku zdjęcia samochodu
     * @param string/null $typ typ MIME zdjęcia
     * @param array $opcje tablica z akcesoriami (opcjami), które są dostepne dla tego samochodu
     */
    public function dodaj($marka,$nazwa_samochodu,$kategoria,$rocznik,$pojemnosc,$liczba_drzwi,$przebieg,$cena,$opis,$zdjecie,$typ,$opcje)
    {
        if ($zdjecie != NULL)
            $zdjecie=base64_encode($zdjecie);
        $this->connector->prepare('BEGIN;')->execute();
        $query='SELECT * FROM dodaj_samochod(:marka,:nazwa_samochodu,:kategoria,:rocznik,:pojemnosc,:liczba_drzwi,:przebieg,:cena,:opis,:zdjecie,:typ)';
        $stmt = $this->connector->prepare($query);
        $stmt->bindValue(':marka', $marka, PDO::PARAM_INT);
        $stmt->bindValue(':nazwa_samochodu', $nazwa_samochodu, PDO::PARAM_STR);
        $stmt->bindValue(':kategoria', $kategoria, PDO::PARAM_INT);
        $stmt->bindValue(':rocznik', $rocznik, PDO::PARAM_INT);
        $stmt->bindValue(':pojemnosc', $pojemnosc, PDO::PARAM_INT);
        $stmt->bindValue(':liczba_drzwi', $liczba_drzwi, PDO::PARAM_INT);
        $stmt->bindValue(':przebieg', $przebieg, PDO::PARAM_INT);
        $stmt->bindValue(':cena', $cena, PDO::PARAM_STR);
        $stmt->bindValue(':opis', $opis, PDO::PARAM_STR);
        $stmt->bindValue(':zdjecie', $zdjecie, PDO::PARAM_STR);
        $stmt->bindValue(':typ', $typ, PDO::PARAM_STR);
        $stmt->execute();
        if(count($opcje))
        {
            $wynik=$stmt->fetchAll();
            foreach($opcje as $opcja)
            {
                $stmt = $this->connector->prepare('SELECT * FROM dodaj_opcje_do_samochodu(:samochod, '.$opcja.');');
                $stmt->bindValue(':samochod', $wynik[0]['dodaj_samochod'], PDO::PARAM_INT);
                $stmt->execute();
            }

        }
        $this->connector->prepare('END;')->execute();
    }

    /**
     * Edytuje istniejący samochód
     * @param int $samochod numer id samochodu
     * @param int $marka marka samochodu
     * @param string $nazwa_samochodu nazwa edytowanego samochodu
     * @param int $kategoria numer id kategorii, do której należy samochód
     * @param int $rocznik rok produkcji samochodu
     * @param int $pojemnosc pojemność silnika w cm3
     * @param int $liczba_drzwi liczba drzwi w samochodze
     * @param float $przebieg dotychczasowy przebieg w km
     * @param float $cena cena za jeden dzień wypożyczenia w PLN
     * @param string/null $opis opis samochodu
     * @param string/null $zdjecie zawartość pliku zdjęcia samochodu
     * @param string/null $typ typ MIME zdjęcia
     * @param array $opcje tablica z akcesoriami (opcjami), które są dostepne dla tego samochodu
     */
    public function edytuj($samochod,$marka,$nazwa_samochodu,$kategoria,$rocznik,$pojemnosc,$liczba_drzwi,$przebieg,$cena,$opis,$zdjecie,$typ,$opcje)
    {
        if($zdjecie===-1)
        {
            $query='SELECT * FROM usun_zdjecie(:samochod);';
            $stmt = $this->connector->prepare($query);
            $stmt->bindValue(':samochod', $samochod, PDO::PARAM_INT);
            $stmt->execute();
            $zdjecie=NULL;
        }
        else if ($zdjecie != NULL)
            $zdjecie=base64_encode($zdjecie);
        $query='SELECT * FROM edytuj_samochod(:samochod,:marka,:nazwa_samochodu,:kategoria,:rocznik,:pojemnosc,:liczba_drzwi,:przebieg,:cena,:opis,:zdjecie,:typ)';
        $stmt = $this->connector->prepare($query);
        $stmt->bindValue(':samochod', $samochod, PDO::PARAM_INT);
        $stmt->bindValue(':marka', $marka, PDO::PARAM_INT);
        $stmt->bindValue(':nazwa_samochodu', $nazwa_samochodu, PDO::PARAM_STR);
        $stmt->bindValue(':kategoria', $kategoria, PDO::PARAM_INT);
        $stmt->bindValue(':rocznik', $rocznik, PDO::PARAM_INT);
        $stmt->bindValue(':pojemnosc', $pojemnosc, PDO::PARAM_INT);
        $stmt->bindValue(':liczba_drzwi', $liczba_drzwi, PDO::PARAM_INT);
        $stmt->bindValue(':przebieg', $przebieg, PDO::PARAM_INT);
        $stmt->bindValue(':cena', $cena, PDO::PARAM_STR);
        $stmt->bindValue(':opis', $opis, PDO::PARAM_STR);
        $stmt->bindValue(':zdjecie', $zdjecie, PDO::PARAM_STR);
        $stmt->bindValue(':typ', $typ, PDO::PARAM_STR);
        $stmt->execute();
        $stmt = $this->connector->prepare('SELECT * FROM usun_wszystkie_opcje(:samochod);');
        $stmt->bindValue(':samochod', $samochod, PDO::PARAM_INT);
        $stmt->execute();
        if(count($opcje))
        {
            foreach($opcje as $opcja)
            {
                $stmt = $this->connector->prepare('SELECT * FROM dodaj_opcje(:samochod, '.$opcja.');');
                $stmt->bindValue(':samochod', $samochod, PDO::PARAM_INT);
                $stmt->execute();
            }

        }
    }

    /**
     * Usuwa istniejący samochód
     * @param int $samochod numer id samochodu
     * @return bool
     */
    public function usun($samochod)
    {
        $stmt = $this->connector->prepare('SELECT * FROM usun_samochod(:samochod);');
        $stmt->bindValue(':samochod', $samochod, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Dodaje ocenę samochodu przez użytkownika
     * @param int $uzytkownik numer id użytkownika
     * @param int $samochod numer id samochodu
     * @param int $ocena wystawiona ocena
     * @return bool
     */
    public function ocen($uzytkownik, $samochod, $ocena)
    {
        $query = 'SELECT * FROM ocen_samochod(' . $uzytkownik. ', '.$samochod . ', '.$ocena.');';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetch();
        $stmt->closeCursor();
            return $wynik['ocen_samochod'];
    }
}