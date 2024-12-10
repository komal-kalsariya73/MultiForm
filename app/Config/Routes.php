<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/multistep', 'MultistepController::index');
$routes->get('/multistep/(:num)', 'MultistepController::index/$1'); 

$routes->post('/insert', 'MultistepController::insertData');
// $routes->post('/update', 'MultistepController::insertData');
$routes->get('/view', 'MultistepController::viewData');
 $routes->get('/fetch-data', 'MultistepController::fetchData'); 
 $routes->get('/getFormData/(:num)', 'MultistepController::getFormData/$1'); 
 
 $routes->post('update/(:num)', 'MultistepController::updateData/$1');
 $routes->delete('/delete/(:num)', 'MultistepController::delete/$1');

 

