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

    public static function findEhdokkaat($id) {
        $query = DB::connection()->prepare('SELECT * FROM Ehdokas WHERE Ehdokas.aanestys_id = :id');
        $query->execute(array('id' => $id));

        $rows = $query->fetchAll();
        $ehdokkaat = array();

        foreach ($rows as $row) {
            $ehdokkaat[] = new Ehdokas(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'lisatieto' => $row['lisatieto'],
                'aanestys_id' => $row['aanestys_id']
            ));
        }

        return $ehdokkaat;
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
        $errors = array();
        $errors = array_merge($errors, self::validate_date($this->alkamisaika));
//        if (strtotime($this->alkamisaika) < strtotime(date("Y-m-d"))) {
//            $errors[] = 'Äänestys voi alkaa aikaisintaan tänään!';
//        }
        return $errors;
    }

    public function validate_loppumisaika() {
        $errors = array();
        $errors = array_merge($errors, self::validate_date($this->loppumisaika));
        if (strtotime($this->loppumisaika) < strtotime($this->alkamisaika)) {
            $errors[] = 'Äänestys ei voi loppua ennen alkamista!';
        }
        if (strtotime($this->loppumisaika) < strtotime(date("Y-m-d"))) {
            $errors[] = 'Äänestys ei voi olla päättynyt vielä!';
        }
        return $errors;
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

        //Tämä estää asdfghjkl syötteet, mutta estää myös chromen päivämääräavustuksen toiminnan
//        if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-])$/", $date, $matches)) {
//            if (checkdate($matches[2], $matches[3], $matches[1])) {
//                return $errors;
//            }
//        } else {
//            $errors[] = 'Päivämäärä täytyy päättää!';
//        }
        return $errors;
    }

}
