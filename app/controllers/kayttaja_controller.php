<?php
class KayttajaController extends BaseController{
  public static function login(){
      View::make('user_login.html');
  }
  public static function handle_login(){
    $params = $_POST;

    $kayttaja = Kayttaja::authenticate($params['nimi'], $params['salasana']);

    if(!$kayttaja){
      View::make('user_login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'nimi' => $params['nimi']));
    }else{
      $_SESSION['user'] = $kayttaja->id;

      Redirect::to('/vote_list', array('message' => 'Tervetuloa takaisin ' . $kayttaja->nimi . '!'));
    }
  }
}