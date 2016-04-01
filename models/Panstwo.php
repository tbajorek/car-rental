<?php

/**
 * Klasa modelu
 */

/**
 * Obsługuje państwa, z których pochodzą marki
 * Class PanstwoModel
 */
class PanstwoModel extends Model
{
    /**
     * Pobiera listę państw
     * @return array
     */
    public function lista()
    {
        $query = 'SELECT * FROM panstwa;';
        $stmt = $this->connector->query($query);
        $wynik = $stmt->fetchAll();
        $stmt->closeCursor();
        return $wynik;
    }
}