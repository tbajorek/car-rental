<?php

/**
 * Klasa modelu
 */

/**
 * Obsługuje historię wypożyczeń
 * Class HistoriaModel
 */
class HistoriaModel extends Model
{
    /**
     * Pobiera listę zakończonych wypożyczeń
     * @param int $uzytkownik id użytkownika, dla któ©ego ma być pobrana lista
     * @return array
     */
    public function lista($uzytkownik=-1)
    {
        $query = 'SELECT * FROM lista_historii';
        if($uzytkownik>0)
            $query.=' WHERE uzytkownik='.$uzytkownik;
        $query.=';';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }
}