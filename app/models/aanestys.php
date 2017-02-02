<?php

class Aanestys extends BaseModel {

    public $id, $nimi, $jarjestaja_id, $lisatieto, $alkamisaika, $loppumisaika, $anonyymi;

    public function __construct($attributes) {
        parent::__construct($attributes);
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

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Aanestys (nimi, jarjestaja_id, lisatieto, alkamisaika, loppumisaika, anonyymi) VALUES (:nimi, :jarjestaja_id, :lisatieto, :alkamisaika, :loppumisaika, :anonyymi) RETURNING id');
        $query->execute(array('nimi' => $this->nimi, 'jarjestaja_id' => $this->jarjestaja_id, 'lisatieto' => $this->lisatieto, 'alkamisaika' => $this->alkamisaika, 'loppumisaika' => $this->loppumisaika, 'anonyymi' => $this->anonyymi));
        $row = $query->fetch();
        $this->id = $row['id'];
    }
}
    