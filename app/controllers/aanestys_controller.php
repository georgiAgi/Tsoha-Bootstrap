<?php

class AanestysController extends BaseController {

    public static function index() {
        $aanestykset = Aanestys::all();
        View::make('vote_list.html', array('aanestykset' => $aanestykset));
    }

    public function show($id) {
        $aanestys = Aanestys::find($id);
        View::make('vote_show.html', array('aanestys' => $aanestys)); //voiko olla käyttämättä array
    }

    public function newVote() {
        View::make('vote_new.html');
    }

    public static function store() {
        // POST-pyynnön muuttujat sijaitsevat $_POST nimisessä assosiaatiolistassa
        $params = $_POST;
        // Alustetaan uusi Game-luokan olion käyttäjän syöttämillä arvoilla
        $aanestys = new Aanestys(array(
            'nimi' => $params['nimi'],
            'lisatieto' => $params['lisatieto'],
            'alkamisaika' => $params['alkamisaika'],
            'loppumisaika' => $params['loppumisaika'],
            'anonyymi' => $params['anonyymi']
        ));
        // Kutsutaan alustamamme olion save metodia, joka tallentaa olion tietokantaan
        $aanestys->save();

        // Ohjataan käyttäjä lisäyksen jälkeen pelin esittelysivulle
        Redirect::to('/vote_show/' . $aanestys->id, array('message' => 'Äänestys on aloitettu!'));
    }

}
