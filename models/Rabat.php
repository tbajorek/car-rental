<?php

/**
 * Klasa modelu
 */

/**
 * Obsługuje rabaty
 * Class RabatModel
 */
class RabatModel extends Model
{
    /**
     * Pobranie listy rabatów
     * @return array
     */
    public function lista()
    {
        $query = 'SELECT * FROM lista_rabatow;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }

    /**
     * Pobranie szczegółów rabatu o podanym kodzie
     * @param string $numer kod rabatowy
     * @return mixed
     */
    public function pobierz($numer)
    {
        $query = 'SELECT * FROM lista_rabatow WHERE numer=\''.$numer.'\';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik[0];
    }

    /**
     * Dodanie nowego rabatu
     * @param string $numer kod rabatu
     * @param int/null $samochod numer id samochodu, którego może dotyczyć
     * @param float $obnizka wartość obniżki
     * @param bool $procentowo flaga, czy obniżka jest procentowa, czy kwotowa
     * @param bool $jednorazowy flaga, czy rabat jest jednokrotnego użytku
     * @param string/null $obowiazuje_od opcjonalna data początku okresu jjego obowiązywania
     * @param string/null $obowiazuje_do opcjonalna data końca okresu jjego obowiązywania
     * @return bool
     */
    public function dodaj($numer,$samochod,$obnizka,$procentowo,$jednorazowy,$obowiazuje_od,$obowiazuje_do)
    {
        $stmt = $this->connector->prepare('SELECT * FROM dodaj_rabat(:numer,:samochod,:obnizka,:procentowo,:jednorazowy,:obowiazuje_od,:obowiazuje_do);');
        $stmt->bindValue(':numer', $numer, PDO::PARAM_STR);
        $stmt->bindValue(':samochod', $samochod, PDO::PARAM_INT);
        $stmt->bindValue(':obnizka', $obnizka, PDO::PARAM_INT);
        $stmt->bindValue(':procentowo', $procentowo, PDO::PARAM_BOOL);
        $stmt->bindValue(':jednorazowy', $jednorazowy, PDO::PARAM_BOOL);
        $stmt->bindValue(':obowiazuje_od', $obowiazuje_od, PDO::PARAM_STR);
        $stmt->bindValue(':obowiazuje_do', $obowiazuje_do, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Edycja istniejącego rabatu
     * @param string $numer kod rabatu
     * @param int/null $samochod numer id samochodu, którego może dotyczyć
     * @param float $obnizka wartość obniżki
     * @param bool $procentowo flaga, czy obniżka jest procentowa, czy kwotowa
     * @param bool $jednorazowy flaga, czy rabat jest jednokrotnego użytku
     * @param string/null $obowiazuje_od opcjonalna data początku okresu jjego obowiązywania
     * @param string/null $obowiazuje_do opcjonalna data końca okresu jjego obowiązywania
     * @return bool
     */
    public function edytuj($numer,$samochod,$obnizka,$procentowo,$jednorazowy,$obowiazuje_od,$obowiazuje_do)
    {var_dump($numer);
        $stmt = $this->connector->prepare('SELECT * FROM edytuj_rabat(:numer,:samochod,:obnizka,:procentowo,:jednorazowy,:obowiazuje_od,:obowiazuje_do);');
        $stmt->bindValue(':numer', $numer, PDO::PARAM_STR);
        $stmt->bindValue(':samochod', $samochod, PDO::PARAM_INT);
        $stmt->bindValue(':obnizka', $obnizka, PDO::PARAM_INT);
        $stmt->bindValue(':procentowo', $procentowo, PDO::PARAM_BOOL);
        $stmt->bindValue(':jednorazowy', $jednorazowy, PDO::PARAM_BOOL);
        $stmt->bindValue(':obowiazuje_od', $obowiazuje_od, PDO::PARAM_STR);
        $stmt->bindValue(':obowiazuje_do', $obowiazuje_do, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Usunięcie rabatu
     * @param string $numer kod rabatowy
     * @return bool
     */
    public function usun($numer)
    {
        $stmt = $this->connector->prepare('SELECT * FROM usun_rabat(:numer);');
        $stmt->bindValue(':numer', $numer, PDO::PARAM_STR);
        return $stmt->execute();
    }
}