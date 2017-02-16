<?php

class Aani extends BaseModel {

    public $id, $ehdokas_id, $aika;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public function save($ehdokasID) {
        $query = DB::connection()->prepare('INSERT INTO Aani (ehdokas_id, aika) VALUES (:ehdokas_id, :aika) RETURNING id');
        $query->execute(array('ehdokas_id' => $ehdokasID, 'aika' => date("Y-m-d H:i:s")));
    }

}
