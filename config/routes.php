<?php

function check_logged_in() {
    BaseController::check_logged_in();
}

$routes->get('/', function() {
    BaseController::index();
});

$routes->get('/candidate/edit/:id', 'check_logged_in', function($id) {
    EhdokasController::edit($id);
});

$routes->post('/candidate/edit/:id', function($id) {
    EhdokasController::update($id);
});

$routes->get('/candidate/destroy/:id', 'check_logged_in', function($id) {
    EhdokasController::destroy($id);
});

$routes->get('/candidate/vote/:id', function($id) {
    EhdokasController::vote($id);
});


$routes->get('/candidate/show/:id', function($id) {
    EhdokasController::show($id);
});

$routes->post('/candidate/new/:id', function($id) {
    EhdokasController::store($id);
});

$routes->get('/candidate/new/:id', 'check_logged_in', function($id) { //äänestyksen id
    EhdokasController::new_candidate($id);
});

$routes->get('/user/edit/:id', 'check_logged_in', function($id) {
    KayttajaController::edit($id);
});

$routes->post('/user/edit/:id', function($id) {
    KayttajaController::update($id);
});

$routes->get('/user/delete/:id', 'check_logged_in', function($id) {
    KayttajaController::delete($id);
});

$routes->post('/user/delete/:id', function($id) {
    KayttajaController::destroy($id);
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

$routes->post('/vote/show', function() {
    AanestysController::store();
});

$routes->get('/vote/list', function() {
    AanestysController::index();
});

$routes->get('/vote/new', 'check_logged_in', function() {
    AanestysController::new_vote();
});

$routes->get('/vote/show/:id', function($id) {
    AanestysController::show($id);
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

$routes->get('/vote/results/:id', 'check_logged_in', function($id) {
    AanestysController::results($id);
});

$routes->get('/vote/publicresults/:id', function($id) {
    AanestysController::public_results($id);
});
