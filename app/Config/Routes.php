<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Les routes nécessitant une connexion vont ici
    $routes->get('/', 'Home::index');
    $routes->get('/backoffice', 'BackofficeController::index');
    $routes->post('/backoffice', 'BackofficeController::index');
    
    $routes->get('/campaigns','CampaignsController::index',['as' => 'campaigns']);
    $routes->get('/campaigns/create','CampaignsController::create');
    $routes->post('/campaigns/store','CampaignsController::store');

    $routes->get('/logs','LogsController::index');
    $routes->get('/explorer','LogsController::explorer');

    $routes->get('/contacts','ContactsController::index');

    $routes->get('/audiences','AudiencesController::index');
    $routes->get('/audiences/create','AudiencesController::create');
    $routes->post('/audiences/store','AudiencesController::store');
    $routes->get('/audiences/delete/(:segment)','AudiencesController::delete/$1');
    $routes->get('/audiences/edit/(:segment)','AudiencesController::edit/$1');
    $routes->get('/audiences/details/(:segment)','AudiencesController::details/$1');
    $routes->post('/audiences/update','AudiencesController::update');

    $routes->get('/campaigns/delete/(:segment)','CampaignsController::delete/$1');
    $routes->get('/campaigns/edit/(:segment)','CampaignsController::edit/$1');
    $routes->get('/campaigns/send/(:segment)','CampaignsController::send/$1');
    $routes->get('/campaigns/sendWithGoogleCloudFunction/(:segment)','CampaignsController::sendWithGoogleCloudFunction/$1');
    $routes->post('/campaigns/update','CampaignsController::update');
    $routes->get('/campaigns/reloadAnalytics','CampaignsController::reloadAnalytics');
    $routes->get('/campaigns/sync','CampaignsController::showSync');
    $routes->post('/campaigns/sync-events','CampaignsController::syncEvents');

    // Domain routes
    $routes->get('/domains', 'DomainsController::index');
    $routes->get('/domains/import', 'DomainsController::import');
    $routes->get('/domains/create', 'DomainsController::create');
    $routes->post('/domains/create', 'DomainsController::create');
    $routes->get('/domains/edit/(:segment)', 'DomainsController::edit/$1');
    $routes->post('/domains/edit/(:segment)', 'DomainsController::edit/$1');
    $routes->get('/domains/delete/(:segment)', 'DomainsController::delete/$1');
    $routes->post('/domains/set-active/(:segment)', 'DomainsController::setActive/$1');
});

// Les routes publiques (accessibles sans connexion) vont ici
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::doLogin');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/unsubscribe/(:segment)','ContactsController::unsubscribe/$1');