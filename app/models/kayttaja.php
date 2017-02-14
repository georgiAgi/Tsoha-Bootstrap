<?php

class Kayttaja extends BaseModel {

    public $id, $nimi, $tiedot, $salasana;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_nimi', 'validate_tiedot');
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja');

        $query->execute();

        $rows = $query->fetchAll();
        $kayttajat = array();


        foreach ($rows as $row) {
            // Tämä on PHP:n hassu syntaksi alkion lisäämiseksi taulukkoon :)
            $kayttajat[] = new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'tiedot' => $row['tiedot'],
            ));
        }

        return $kayttajat;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $kayttaja = new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'tiedot' => $row['tiedot'],
            ));

            return $kayttaja;
        }

        return null;
    }

    public static function findKayttajanAanestykset($id) {
        $query = DB::connection()->prepare('SELECT * FROM Aanestys WHERE jarjestaja_id = :id');
        $query->execute(array('id' => $id));

        $rows = $query->fetchAll();
        $aanestykset = array();

        foreach ($rows as $row) {
            $aanestykset[] = new Aanestys(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'jarjestaja_id' => $row['jarjestaja_id'],
                'lisatieto' => $row['lisatieto'],
                'alkamisaika' => $row['alkamisaika'],
                'loppumisaika' => $row['loppumisaika'],
                'anonyymi' => $row['anonyymi'],
            ));
        }

        return $aanestykset;
    }

    public static function findByName($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE nimi = :nimi LIMIT 1');
        $query->execute(array('nimi' => $nimi));
        $row = $query->fetch();

        if ($row) {
            $kayttaja = new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'tiedot' => $row['tiedot'],
            ));

            return $kayttaja;
        }

        return null;
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Kayttaja (nimi, tiedot, salasana) VALUES (:nimi, :tiedot, :salasana) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'tiedot' => $this->tiedot, 'salasana' => $this->salasana));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public static function authenticate($nimi, $salasana) {
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE nimi = :nimi AND salasana = :salasana LIMIT 1');
        $query->execute(array('nimi' => $nimi, 'salasana' => $salasana));
        $row = $query->fetch();
        if ($row) {
            $kayttaja = new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'tiedot' => $row['tiedot'],
                'salasana' => $row['salasana']
            ));

            return $kayttaja;
        } else {
            return NULL;
        }
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Kayttaja SET nimi = :nimi, tiedot = :tiedot WHERE id = :id');
        $query->execute(array('nimi' => $this->nimi, 'tiedot' => $this->tiedot, 'id' => $this->id));
    }

    public function validate_nimi() {
        $errors = array();
        $errors = array_merge($errors, $this->validate_string_minimum_length($this->nimi, 3));
        $errors = array_merge($errors, $this->validate_string_maximum_length($this->nimi, 100));
        return $errors;
    }

    public function validate_tiedot() {
        $errors = array();
        $errors = array_merge($errors, $this->validate_string_maximum_length($this->tiedot, 1000));
        return $errors;
    }

}
