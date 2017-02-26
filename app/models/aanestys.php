<?php

class Aanestys extends BaseModel {

    public $id, $nimi, $jarjestaja_id, $lisatieto, $alkamisaika, $loppumisaika, $anonyymi, $julkisettulokset;

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
            $aanestykset[] = new Aanestys(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'jarjestaja_id' => $row['jarjestaja_id'],
                'lisatieto' => $row['lisatieto'],
                'alkamisaika' => $row['alkamisaika'],
                'loppumisaika' => $row['loppumisaika'],
                'anonyymi' => $row['anonyymi'],
                'julkisettulokset' => $row['julkisettulokset']
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
                'julkisettulokset' => $row['julkisettulokset']
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

    public static function find_by_name($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Aanestys WHERE nimi = :nimi LIMIT 1');
        $query->execute(array('nimi' => $nimi));
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
                'julkisettulokset' => $row['julkisettulokset']
            ));

            return $aanestys;
        }

        return null;
    }

    public static function find_ehdokkaat($id) {
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

    public static function find_and_sort_ehdokkaat($id) {
        $query = DB::connection()->prepare('SELECT ehdokas.*, COUNT(aani.id) AS aanimaara
    FROM ehdokas LEFT JOIN aani 
    ON ehdokas.id = aani.ehdokas_id
    WHERE ehdokas.aanestys_id = :id
    GROUP BY ehdokas.nimi, ehdokas.lisatieto, ehdokas.id, ehdokas.aanestys_id
    ORDER BY aanimaara DESC');
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

    public static function find_top5_ehdokkaat($id) {
        $query = DB::connection()->prepare('SELECT ehdokas.nimi, ehdokas.lisatieto, ehdokas.id, COUNT(aani.id) AS aanimaara
    FROM ehdokas LEFT JOIN aani 
    ON ehdokas.id = aani.ehdokas_id
    WHERE ehdokas.aanestys_id = :id
    GROUP BY ehdokas.nimi, ehdokas.lisatieto, ehdokas.id
    ORDER BY aanimaara DESC
    LIMIT 5');
        $query->execute(array('id' => $id));

        $rows = $query->fetchAll();
        $ehdokkaat = array();

        foreach ($rows as $row) {
            $ehdokkaat[] = new Ehdokas(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'lisatieto' => $row['lisatieto']
            ));
        }

        return $ehdokkaat;
    }

    public static function find_vastanneet_kayttajat($id) {
        $query = DB::connection()->prepare('SELECT Kayttaja.* FROM Aanestajalista, Kayttaja WHERE Aanestajalista.aanestys_id = :id AND Kayttaja.id = Aanestajalista.kayttaja_id');
        $query->execute(array('id' => $id));

        $rows = $query->fetchAll();
        $kayttajat = array();

        foreach ($rows as $row) {
            $kayttajat[] = new Kayttaja(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'tiedot' => $row['tiedot'],
            ));
        }

        return $kayttajat;
    }

    public static function anonyymi($id) {
        $aanestys = self::find($id);
        if ($aanestys->anonyymi) {
            return 'Anonyymi';
        }
        return 'Rekisteröityneet käyttäjät';
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Aanestys (nimi, jarjestaja_id, lisatieto, alkamisaika, loppumisaika, anonyymi, julkisettulokset) VALUES (:nimi, :jarjestaja_id, :lisatieto, :alkamisaika, :loppumisaika, :anonyymi, :julkisettulokset) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'jarjestaja_id' => $this->jarjestaja_id, 'lisatieto' => $this->lisatieto, 'alkamisaika' => $this->alkamisaika, 'loppumisaika' => $this->loppumisaika, 'anonyymi' => $this->anonyymi, 'julkisettulokset' => $this->julkisettulokset));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Aanestys SET nimi = :nimi, lisatieto = :lisatieto, alkamisaika = :alkamisaika, loppumisaika = :loppumisaika, anonyymi = :anonyymi, julkisettulokset = :julkisettulokset WHERE id = :id');
        $query->execute(array('nimi' => $this->nimi, 'lisatieto' => $this->lisatieto, 'alkamisaika' => $this->alkamisaika, 'loppumisaika' => $this->loppumisaika, 'anonyymi' => $this->anonyymi, 'julkisettulokset' => $this->julkisettulokset, 'id' => $this->id));
    }

    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM Aanestys WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    public function validate_nimi() {
        $errors = array();
        $errors = array_merge($errors, $this->validate_string_minimum_length($this->nimi, 3, 'nimi'));
        $errors = array_merge($errors, $this->validate_string_maximum_length($this->nimi, 100, 'nimi'));
        return $errors;
    }

    public function validate_lisatieto() {
        $errors = array();
        $errors = array_merge($errors, $this->validate_string_maximum_length($this->lisatieto, 1000, 'lisatieto'));
        return $errors;
    }

    public function validate_alkamisaika() {
        $errors = array();
        $errors = array_merge($errors, self::validate_date($this->alkamisaika, 'Alkamisaika'));
        return $errors;
    }

    public function validate_loppumisaika() {
        $errors = array();
        $errors = array_merge($errors, self::validate_date($this->loppumisaika, 'Päättymisaika'));
        if (strtotime($this->loppumisaika) < strtotime($this->alkamisaika)) {
            $errors[] = 'Äänestys ei voi loppua ennen alkamista!';
        }
        if (strtotime($this->loppumisaika) < strtotime(date("Y-m-d"))) {
            $errors[] = 'Äänestys ei voi olla päättynyt vielä!';
        }
        return $errors;
    }

}
