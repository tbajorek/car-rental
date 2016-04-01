<?php

/**
 * Klasa jądra systemu
 */

/**
 * Klasa obsługująca konfigurację zapisaną w pliikach INI
 * Class Config
 */
class Config
{
    /**
     * Załadowana konfiguracja
     * @var array
     */
    private $data = array();

    /**
     * Zwraca wartość konfiguracji, jeśli jest ona ustawiona
     * @param string $name nazwa parametru konfiguracji
     * @return mixed|null
     */
    public function get($name)
	{
        if(isset($this->data[$name]))
            return $this->data[$name];
        else
            return NULL;
	}

    /**
     * Ustawia nową wartość parametru konfiguracji
     * @param string $name nazwa parametru konfiguracji
     * @param mixed $value ustawiana wartość
     * @return $this
     */
    public function set($name, $value)
	{
		$this->data[$name] = $value;
		return $this;
	}

    /**
     * Getter - zwraca wartość konfiguracji, jeśli jest ona ustawiona
     * @param string $name nazwa parametru konfiguracji
     * @return mixed|null
     */
    public function __get($name)
	{
		$this->get($name);
	}

    /**
     * Setter - utawia nową wartość parametru konfiguracji
     * @param string $name nazwa parametru konfiguracji
     * @param mixed $value ustawiana wartość
     */
    public function __set($name, $value)
	{
		$this->set($name, $value);
	}

    /**
     * Konstruktor, ładujący konfigurację z pliku
     * @param string $file nazwa pliku z konfiguracją
     * @throws Exception
     */
    public function __construct($file)
	{
		if(!file_exists($file))
			throw new Exception("Nie znaleziono pliku ".$file);
		$this->data = parse_ini_file($file);
	}
}