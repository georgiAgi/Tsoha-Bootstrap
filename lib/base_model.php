<?php

class BaseModel {

    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null) {
        // Käydään assosiaatiolistan avaimet läpi
        foreach ($attributes as $attribute => $value) {
            // Jos avaimen niminen attribuutti on olemassa...
            if (property_exists($this, $attribute)) {
                // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
                $this->{$attribute} = $value;
            }
        }
    }

    public function errors() {
        // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
        $errors = array();

        foreach ($this->validators as $validator) {
            // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
            $errors = array_merge($errors, $this->$validator());
        }

        return $errors;
    }

    public function validate_string_minimum_length($string, $length, $name) {
        $errors = array();
        if ($string == '' || $string == null) {
            $errors[] = 'Ei saa olla tyhjä ' . $name . '.';
        }
        if (strlen($string) < $length) {
            $errors[] = 'Liian lyhyt ' . $name . '. Pituuden tulee olla vähintään ' . $length . ' merkkiä!';
        }

        return $errors;
    }

    public function validate_string_maximum_length($string, $length, $name) {
        $errors = array();

        if (strlen($string) > $length) {
            $errors[] = 'Liian pitkä ' . $name . '. Pituus saa olla korkeintaan ' . $length . ' merkkiä!';
        }

        return $errors;
    }

    public function validate_date($date, $name) {
        $errors = array();

        if ($date == '') {
            $errors[] = $name . ' täytyy päättää!';
        }

        $d = DateTime::createFromFormat('Y-m-d', $date);
        if ($d && $d->format('Y-m-d') !== $date) {
            $errors[] = $name . ' väärässä muodossa!';
        }
        
        return $errors;
    }

}
