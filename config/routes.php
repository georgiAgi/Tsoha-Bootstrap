<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
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

  $routes->get('/vote_show', function() {
    HelloWorldController::voteShow();
});

  $routes->get('/vote_list', function() {
    HelloWorldController::voteList();
});
