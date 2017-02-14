<?php

function check_logged_in() {
    BaseController::check_logged_in();
}

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/candidate/edit', function() {
    HelloWorldController::candidateEdit();
});

$routes->get('/candidate/show', function() {
    HelloWorldController::candidateShow();
});

$routes->get('/user/edit/:id', 'check_logged_in', function($id) {
    KayttajaController::edit($id);
});

$routes->post('/user/edit/:id', function($id) {
    KayttajaController::update($id);
});

$routes->get('/user/show', function() {
    HelloWorldController::userShow();
});

$routes->get('/user/show/:id', function($id) {
    KayttajaController::show($id);
});

$routes->get('/user/login', function() {
    KayttajaController::login();
});

$routes->post('/user/logout', function() {
    KayttajaController::logout();
});

$routes->post('/user/login', function() {
    KayttajaController::handle_login();
});

$routes->get('/user/register', function() {
    KayttajaController::register();
});

$routes->post('/user/register', function() {
    KayttajaController::handle_register();
});

$routes->get('/vote/show', function() {
    HelloWorldController::voteShow();
});

$routes->post('/vote/show', function() {
    AanestysController::store();
});

$routes->get('/vote/list', function() {
    AanestysController::index();
});

$routes->get('/vote/new', 'check_logged_in', function() {
    AanestysController::newVote();
});

$routes->get('/vote/show/:id', function($id) {
    AanestysController::show($id);
});

$routes->get('/vote/edit', function() { //HUOM HELLOWORLD, EI OIKEAA KÄYTTÖÄ
    HelloWorldController::voteEdit();
});

$routes->get('/vote/edit/:id', 'check_logged_in', function($id) {
    AanestysController::edit($id);
});

$routes->post('/vote/edit/:id', function($id) {
    AanestysController::update($id);
});

$routes->get('/vote/delete/:id', function($id) {
    AanestysController::delete($id);
});

$routes->post('/vote/delete/:id', function($id) {
    AanestysController::destroy($id);
});
