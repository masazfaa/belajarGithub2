<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('home/data', 'Home::data');
$routes->post('home/save', 'Home::save');
$routes->get('home/delete/(:num)', 'Home::delete/$1');
$routes->post('home/updateData', 'Home::updateData');
$routes->get('/logout', 'Home::logout');
$routes->get('home/get_fotos/(:num)', 'Home::get_fotos/$1');
$routes->get('home/delete_foto/(:num)', 'Home::delete_foto/$1');
