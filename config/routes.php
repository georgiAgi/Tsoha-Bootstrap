<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::hiekka();
});

$routes->get('/candidate_edit', function() {
    HelloWorldController::candidateEdit();
});

$routes->get('/candidate_show', function() {
    HelloWorldController::candidateShow();
});

$routes->get('/user_edit', function() {
    HelloWorldController::userEdit();
});

$routes->get('/user_show', function() {
    HelloWorldController::userShow();
});

$routes->get('/vote_edit', function() {
    HelloWorldController::voteEdit();
});

$routes->post('/vote_show', function() {
    AanestysController::store();
});

$routes->get('/vote_list', function() {
    AanestysController::index();
});

$routes->get('/vote_new', function() {
    AanestysController::newVote();
});

$routes->get('/vote_show/:id', function($id) {
    AanestysController::show($id);
});
