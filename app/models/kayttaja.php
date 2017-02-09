<?php

class Kayttaja extends BaseModel {

    public $id, $nimi, $tiedot, $salasana;

    public function __construct($attributes) {
        parent::__construct($attributes);
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

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Kayttaja (nimi, tiedot) VALUES (:nimi, :tiedot) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'tiedot' => $this->tiedot));
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

}
