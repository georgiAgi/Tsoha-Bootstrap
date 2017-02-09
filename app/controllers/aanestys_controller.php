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
        $jarjestaja = self::get_user_logged_in();
        // Alustetaan uusi Game-luokan olion käyttäjän syöttämillä arvoilla
        $aanestys = new Aanestys(array(
            'nimi' => $params['nimi'],
            'lisatieto' => $params['lisatieto'],
            'alkamisaika' => $params['alkamisaika'],
            'loppumisaika' => $params['loppumisaika'],
            'anonyymi' => $params['anonyymi'],
            'jarjestaja_id' => $jarjestaja->id
        ));
        // Kutsutaan alustamamme olion save metodia, joka tallentaa olion tietokantaan
        $aanestys->save();

        // Ohjataan käyttäjä lisäyksen jälkeen pelin esittelysivulle
        Redirect::to('/vote_show/' . $aanestys->id, array('message' => 'Äänestys on aloitettu!'));
    }

    public static function edit($id) {
        $aanestys = Aanestys::find($id);
        View::make('vote_edit.html', array('aanestys' => $aanestys));
    }

    public static function update($id) {
        $params = $_POST;

        $attributes = array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'lisatieto' => $params['lisatieto'],
            'alkamisaika' => $params['alkamisaika'],
            'loppumisaika' => $params['loppumisaika'],
            'anonyymi' => $params['anonyymi']
        );
        
        $aanestys = new Aanestys($attributes);
        //$errors = $aanestys->errors();'errors' => $errors,

//        if (count($errors) > 0) {
//            View::make('vote_edit.html', array('attributes' => $attributes));
//        } else {
            // Kutsutaan alustetun olion update-metodia, joka päivittää pelin tiedot tietokannassa
            $aanestys->update();

            Redirect::to('/vote_show/' . $aanestys->id, array('message' => 'Äänestystä on muokattu onnistuneesti!'));
//        }
    }

    public static function destroy($id) {
        $aanestys = new Aanestys(array('id' => $id));
        // Kutsutaan Game-malliluokan metodia destroy, joka poistaa pelin sen id:llä
        $aanestys->destroy();

        // Ohjataan käyttäjä pelien listaussivulle ilmoituksen kera
        Redirect::to('/vote_list', array('message' => 'Äänestys on poistettu onnistuneesti!'));
    }

    public function delete($id) {
        $aanestys = Aanestys::find($id);
        View::make('vote_delete.html', array('aanestys' => $aanestys));
    }

}
