<?php

class KayttajaController extends BaseController {

    public static function login() {
        View::make('user/login.html');
    }

    public static function handle_login() {
        $params = $_POST;

        $kayttaja = Kayttaja::authenticate($params['nimi'], $params['salasana']);

        if (!$kayttaja) {
            View::make('user/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'nimi' => $params['nimi']));
        } else {
            $_SESSION['user'] = $kayttaja->id;

            Redirect::to('/vote/list', array('message' => 'Tervetuloa takaisin ' . $kayttaja->nimi . '!'));
        }
    }

    public function show($id) {
        $kayttaja = Kayttaja::find($id);
        View::make('user/show.html', array('kayttaja' => $kayttaja));
    }

    public static function logout() {
        $_SESSION['user'] = null;
        Redirect::to('/', array('message' => 'Olet kirjautunut ulos!'));
    }

    public static function register() {
        View::make('user/register.html');
    }

    public static function handle_register() {
        $params = $_POST;

        if ($params['salasana'] !== $params['salasananVarmennus']) {
            View::make('user/register.html', array('error' => 'Salasanojen täytyy olla samanlaiset!', 'nimi' => $params['nimi']));
        }

        $kayttaja = new Kayttaja(array(
            'nimi' => $params['nimi'],
            'salasana' => $params['salasana']
        ));

        if ($kayttaja->findByName($params['nimi']) != null) {
            View::make('user/register.html', array('error' => 'Käyttäjänimi on jo käytössä!'));
        }

        $kayttaja->save();

        $_SESSION['user'] = $kayttaja->id;

        Redirect::to('/vote/list', array('message' => 'Tervetuloa ' . $kayttaja->nimi . '!'));
    }

    public static function edit($id) {
        $kayttaja = self::get_user_logged_in();
        if ($kayttaja->id == $id) {
            View::make('user/edit.html', array('kayttaja' => $kayttaja));
        }
        Redirect::to('/user/show/' . $id, array('message' => 'Vain käyttäjä voi muokata omia tietojaan!'));
    }

    public static function update($id) {
        $params = $_POST;

        $attributes = array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'tiedot' => $params['tiedot']
        );

        $kayttaja = new Kayttaja($attributes);

        $errors = $kayttaja->errors();
        if (count($errors) > 0) {
            View::make('user/edit.html', array('kayttaja' => $attributes, 'errors' => $errors));
        } else {
            $kayttaja->update();

            Redirect::to('/user/show/' . $kayttaja->id, array('message' => 'Käyttäjää on muokattu onnistuneesti!'));
        }
    }
}
