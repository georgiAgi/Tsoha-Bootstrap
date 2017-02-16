<?php

class EhdokasController extends BaseController {

    public static function newCandidate($id) {
        $aanestys = Aanestys::find($id);
        View::make('candidate/new.html', array('aanestys' => $aanestys));
    }

    public static function show($id) {
        $ehdokas = Ehdokas::find($id);
        $aanestys = Aanestys::find($ehdokas->aanestys_id);
        View::make('candidate/show.html', array('ehdokas' => $ehdokas, 'aanestys' => $aanestys));
    }

    public static function edit($id) {
        $kayttaja = self::get_user_logged_in();
        $ehdokas = Ehdokas::find($id);
        $aanestys = Aanestys::find($ehdokas->aanestys_id);
        if ($kayttaja->id == $aanestys->jarjestaja_id) {
            View::make('candidate/edit.html', array('ehdokas' => $ehdokas));
        }
        Redirect::to('/candidate/show/' . $id, array('message' => 'Vain äänestyksen järjestäjä voi muokata ehdokkaiden tietoja!'));
    }

    public static function store($aanestysID) {
        $params = $_POST;
        $ehdokas = new Ehdokas(array(
            'nimi' => $params['nimi'],
            'lisatieto' => $params['lisatieto'],
            'aanestys_id' => $aanestysID
        ));

        $errors = $ehdokas->errors();

        if (count($errors) == 0) {
            $ehdokas->save();

            Redirect::to('/candidate/show/' . $ehdokas->id, array('message' => 'Ehdokas on luotu onnistuneesti!'));
        } else {
            $aanestys = Aanestys::find($ehdokas->aanestys_id);
            View::make('candidate/new.html', array('errors' => $errors, 'aanestys' => $aanestys, 'ehdokas' => $ehdokas));
        }
    }

    public static function update($id) {
        $params = $_POST;

        $attributes = array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'lisatieto' => $params['lisatieto']
        );

        $ehdokas = new Ehdokas($attributes);

        $errors = $ehdokas->errors();
        if (count($errors) > 0) {
            $aanestys = Aanestys::find($ehdokas->aanestys_id);
            View::make('candidate/edit.html', array('aanestys' => $aanestys, 'errors' => $errors, 'ehdokas' => $ehdokas));
        } else {
            $ehdokas->update();

            Redirect::to('/candidate/show/' . $ehdokas->id, array('message' => 'Ehdokasta on muokattu onnistuneesti!'));
        }
    }

    public function vote($id) {
        $ehdokas = Ehdokas::find($id);
        $aanestys = Aanestys::find($ehdokas->aanestys_id);
        
        if (strtotime($aanestys->alkamisaika) > strtotime(date("Y-m-d"))) {
            Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Äänestys ei ole vielä alkanut!'));
        } else if (strtotime($aanestys->loppumisaika) < strtotime(date("Y-m-d"))) {
            Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Äänestys on jo päättynyt!'));
        }
               
        if ($aanestys->anonyymi == FALSE) { //tähän vielä tarkistus, onko käyttäjä jo äänestänyt
            self::check_logged_in();
            $kayttaja = self::get_user_logged_in();
            
            if ($kayttaja::findOnkoAanestanyt($kayttaja->id, $aanestys->id)) {
                Redirect::to('/vote/show/' . $aanestys->id, array('message' => 'Olet jo äänestänyt!'));
            }
            
            $kayttaja::vote($kayttaja->id, $aanestys->id);
        }
        
        Aani::save($id);
        View::make('/vote/show.html', array('aanestys' => $aanestys, 'message' => 'Äänestit onnistuneesti.'));
    }

    public static function destroy($id) {
        $ehdokas = Ehdokas::find($id);
        $aanestys = Aanestys::find($ehdokas->aanestys_id);
        $ehdokas->destroy();
        //Kun redirectaa, ei jostain syystä onnistu käyttämään äänestyksen metodeja, mutta saa kuitenkin oikean äänestyksen.
        View::make('/vote/show.html', array('aanestys' => $aanestys, 'message' => 'Ehdokas on poistettu onnistuneesti!'));
    }

}
