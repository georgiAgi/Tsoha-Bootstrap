<?php

class Aanestys extends BaseModel {

    public $id, $nimi, $jarjestaja_id, $lisatieto, $alkamisaika, $loppumisaika, $anonyymi;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_nimi', 'validate_alkamisaika', 'validate_loppumisaika', 'validate_lisatieto');
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Aanestys');

        $query->execute();

        $rows = $query->fetchAll();
        $aanestykset = array();


        foreach ($rows as $row) {
            // Tämä on PHP:n hassu syntaksi alkion lisäämiseksi taulukkoon :)
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

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Aanestys WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $aanestys = new Aanestys(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'jarjestaja_id' => $row['jarjestaja_id'],
                'lisatieto' => $row['lisatieto'],
                'alkamisaika' => $row['alkamisaika'],
                'loppumisaika' => $row['loppumisaika'],
                'anonyymi' => $row['anonyymi'],
            ));

            return $aanestys;
        }

        return null;
    }

    public static function find_jarjestaja($id) {
        $query = DB::connection()->prepare('SELECT Kayttaja.nimi FROM Kayttaja, Aanestys WHERE Aanestys.id = :id AND Kayttaja.id = Aanestys.jarjestaja_id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $jarjestaja = $row['nimi'];

            return $jarjestaja;
        }

        return null;
    }
    
    public static function anonyymi($id) {
        $aanestys = self::find($id);
        if ($aanestys->anonyymi) {
            return 'Anonyymi';
        }
        return 'Rekisteröityneet käyttäjät';
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Aanestys (nimi, jarjestaja_id, lisatieto, alkamisaika, loppumisaika, anonyymi) VALUES (:nimi, :jarjestaja_id, :lisatieto, :alkamisaika, :loppumisaika, :anonyymi) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'jarjestaja_id' => $this->jarjestaja_id, 'lisatieto' => $this->lisatieto, 'alkamisaika' => $this->alkamisaika, 'loppumisaika' => $this->loppumisaika, 'anonyymi' => $this->anonyymi));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Aanestys SET nimi = :nimi, lisatieto = :lisatieto, alkamisaika = :alkamisaika, loppumisaika = :loppumisaika, anonyymi = :anonyymi WHERE id = :id');
        $query->execute(array('nimi' => $this->nimi, 'lisatieto' => $this->lisatieto, 'alkamisaika' => $this->alkamisaika, 'loppumisaika' => $this->loppumisaika, 'anonyymi' => $this->anonyymi, 'id' => $this->id));
    }

    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM Aanestys WHERE id = :id');
        $query->execute(array('id' => $this->id));
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

    public function validate_alkamisaika() {
        return self::validate_date($this->alkamisaika);
    }

    public function validate_loppumisaika() {
        return self::validate_date($this->loppumisaika);
    }

    public function validate_date($date) {
        $errors = array();
        
        if ($date == '') {
            $errors[] = 'Päivämäärä täytyy päättää';
        }

        $d = DateTime::createFromFormat('Y-m-d', $date);
        if ($d && $d->format('Y-m-d') !== $date) {
            $errors[] = 'Päivämäärä täytyy päättää!';
        }

        return $errors;
    }
    
}
