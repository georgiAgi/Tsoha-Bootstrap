<?php

class AanestysController extends BaseController {

    public static function index() {
        $aanestykset = Aanestys::all();
        View::make('vote/list.html', array('aanestykset' => $aanestykset));
    }

    public function show($id) {
        $aanestys = Aanestys::find($id);
        View::make('vote/show.html', array('aanestys' => $aanestys));
    }

    public function results($id) {
        $aanestys = Aanestys::find($id);
        View::make('vote/results.html', array('aanestys' => $aanestys));
    }

    public function newVote() {
        self::check_logged_in();
        View::make('vote/new.html');
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
        $errors = $aanestys->errors();

        if (count($errors) == 0) {
            $aanestys->save();

            Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Äänestys on luotu!'));
        } else {
            View::make('vote/new.html', array('errors' => $errors, 'aanestys' => $aanestys));
        }
    }

    public static function edit($id) {
        $aanestys = Aanestys::find($id);
        $user = self::get_user_logged_in();
        if ($user->id == $aanestys->jarjestaja_id) {
            View::make('vote/edit.html', array('aanestys' => $aanestys));
        }
        Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Vain järjestäjä voi muokata äänestystä!'));
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

        $errors = $aanestys->errors();
        if (count($errors) > 0) {
            View::make('vote/edit.html', array('aanestys' => $attributes, 'errors' => $errors));
        } else {
            $aanestys->update();

            Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Äänestystä on muokattu onnistuneesti!'));
        }
    }

    public static function destroy($id) {
        $aanestys = new Aanestys(array('id' => $id));
        // Kutsutaan Game-malliluokan metodia destroy, joka poistaa pelin sen id:llä
        $aanestys->destroy();

        // Ohjataan käyttäjä pelien listaussivulle ilmoituksen kera
        Redirect::to('/vote/list', array('message' => 'Äänestys on poistettu onnistuneesti!'));
    }

    public function delete($id) {
        $aanestys = Aanestys::find($id);
        $user = self::get_user_logged_in();
        if ($user->id == $aanestys->jarjestaja_id) {
            View::make('vote/delete.html', array('aanestys' => $aanestys));
        }
        Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Vain järjestäjä voi poistaa äänestyksen!'));
    }

}
