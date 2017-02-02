<?php

class HelloWorldController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        View::make('home.html');
        ;
    }

    public static function sandbox() {
        $armas = Kayttaja::find(1);
        $kayttajat = Kayttaja::all();
        $misstekoaly = Aanestys::find(1);
        $aanestykset = Aanestys::all();
        // Kint-luokan dump-metodi tulostaa muuttujan arvon
        Kint::dump($kayttajat);
        Kint::dump($armas);
        Kint::dump($aanestykset);
        Kint::dump($misstekoaly);
    }

    public static function hiekka() {
        // Testaa koodiasi täällä
        View::make('hiekkalaatikko.html');
    }

    public static function candidateEdit() {
        // Testaa koodiasi täällä
        View::make('candidate_edit.html');
    }

    public static function candidateShow() {
        // Testaa koodiasi täällä
        View::make('candidate_show.html');
    }

    public static function userEdit() {
        // Testaa koodiasi täällä
        View::make('user_edit.html');
    }

    public static function userShow() {
        // Testaa koodiasi täällä
        View::make('user_show.html');
    }

    public static function voteEdit() {
        // Testaa koodiasi täällä
        View::make('vote_edit.html');
    }

    public static function voteShow() {
        // Testaa koodiasi täällä
        View::make('vote_show.html');
    }

    public static function voteList() {
        // Testaa koodiasi täällä
        View::make('vote_list.html');
    }

}
