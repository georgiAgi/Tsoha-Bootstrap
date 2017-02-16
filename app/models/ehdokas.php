<?php

class Ehdokas extends BaseModel {

    public $id, $nimi, $lisatieto, $aanestys_id;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_nimi', 'validate_lisatieto');
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Ehdokas');

        $query->execute();

        $rows = $query->fetchAll();
        $ehdokkaat = array();


        foreach ($rows as $row) {
            // Tämä on PHP:n hassu syntaksi alkion lisäämiseksi taulukkoon :)
            $ehdokkaat[] = new Ehdokas(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'lisatieto' => $row['lisatieto'],
                'aanestys_id' => $row['aanestys_id']
            ));
        }

        return $ehdokkaat;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Ehdokas WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $ehdokas = new Ehdokas(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'lisatieto' => $row['lisatieto'],
                'aanestys_id' => $row['aanestys_id']
            ));

            return $ehdokas;
        }

        return null;
    }

    public static function findAanimaara($id) {
        $query = DB::connection()->prepare('SELECT COUNT(*) FROM Aani WHERE ehdokas_id = :id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            return $row[0];
        }
        
        return null;
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Ehdokas (nimi, lisatieto, aanestys_id) VALUES (:nimi, :lisatieto, :aanestys_id) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'lisatieto' => $this->lisatieto, 'aanestys_id' => $this->aanestys_id));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Ehdokas SET nimi = :nimi, lisatieto = :lisatieto WHERE id = :id');
        $query->execute(array('nimi' => $this->nimi, 'lisatieto' => $this->lisatieto, 'id' => $this->id));
    }

    public function validate_nimi() {
        $errors = array();
        $errors = array_merge($errors, $this->validate_string_minimum_length($this->nimi, 3));
        $errors = array_merge($errors, $this->validate_string_maximum_length($this->nimi, 100));
        return $errors;
    }

    public function validate_lisatieto() {
        $errors = array();
        $errors = array_merge($errors, $this->validate_string_maximum_length($this->lisatieto, 1000));
        return $errors;
    }

    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM Ehdokas WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

}
