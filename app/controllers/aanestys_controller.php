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
        $user = self::get_user_logged_in();
        $aanestys = Aanestys::find($id);
        if ($aanestys->jarjestaja_id == $user->id) {
            View::make('vote/results.html', array('aanestys' => $aanestys));
        }
        self::public_results($id);
    }

    public function new_vote() {
        self::check_logged_in();
        View::make('vote/new.html');
    }

    public static function store() {
        $params = $_POST;
        $jarjestaja = self::get_user_logged_in();
        $aanestys = new Aanestys(array(
            'nimi' => $params['nimi'],
            'lisatieto' => $params['lisatieto'],
            'alkamisaika' => $params['alkamisaika'],
            'loppumisaika' => $params['loppumisaika'],
            'anonyymi' => $params['anonyymi'],
            'julkisettulokset' => $params['julkisettulokset'],
            'jarjestaja_id' => $jarjestaja->id
        ));
        
        $errors = $aanestys->errors();

        if ($aanestys->find_by_name($params['nimi']) != null) {
            $errors[] = 'Samanniminen äänestys on jo luotu!';
        }

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
        Redirect::to('/vote/show/' . $aanestys->id, array('error' => 'Vain järjestäjä voi muokata äänestystä!'));
    }

    public static function update($id) {
        $params = $_POST;

        $attributes = array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'lisatieto' => $params['lisatieto'],
            'alkamisaika' => $params['alkamisaika'],
            'loppumisaika' => $params['loppumisaika'],
            'anonyymi' => $params['anonyymi'],
            'julkisettulokset' => $params['julkisettulokset']
        );

        $aanestys = new Aanestys($attributes);

        $errors = $aanestys->errors();

        $samanniminenAanestys = $aanestys->find_by_name($params['nimi']);
        if ($samanniminenAanestys != null) {
            if ($samanniminenAanestys->id != $id) {
                $errors[] = 'Samanniminen äänestys on jo luotu!';
            }
        }
        if (count($errors) > 0) {
            View::make('vote/edit.html', array('aanestys' => $attributes, 'errors' => $errors));
        } else {
            $aanestys->update();

            Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Äänestystä on muokattu onnistuneesti!'));
        }
    }

    public static function destroy($id) {
        $aanestys = new Aanestys(array('id' => $id));        
        $aanestys->destroy();

        Redirect::to('/vote/list', array('message' => 'Äänestys on poistettu onnistuneesti!'));
    }

    public function delete($id) {
        $aanestys = Aanestys::find($id);
        $user = self::get_user_logged_in();
        if ($user->id == $aanestys->jarjestaja_id) {
            View::make('vote/delete.html', array('aanestys' => $aanestys));
        }
        Redirect::to('/vote/show/' . $aanestys->id, array('error' => 'Vain järjestäjä voi poistaa äänestyksen!'));
    }

    public function public_results($id) {
        $aanestys = Aanestys::find($id);
        if ($aanestys->julkisettulokset == 1) {
            View::make('vote/topcandidates.html', array('aanestys' => $aanestys));
        } elseif ($aanestys->julkisettulokset == 2) {
            View::make('vote/candidatescores.html', array('aanestys' => $aanestys));
        }
        Redirect::to('/vote/show/' . $aanestys->id, array('error' => 'Tuloksia ei saatavilla.'));
    }

}
