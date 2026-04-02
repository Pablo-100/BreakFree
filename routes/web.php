<?php
/**
 * BreakFree - Définition des routes
 */

/** @var Router $router */

// ─── Pages publiques ───
$router->get('/',          'AuthController', 'loginForm');

// ─── Auth ───
$router->get('/login',     'AuthController', 'loginForm');
$router->post('/login',    'AuthController', 'login');
$router->get('/register',  'AuthController', 'registerForm');
$router->post('/register', 'AuthController', 'register');
$router->get('/logout',    'AuthController', 'logout');
$router->get('/forgot-password',  'AuthController', 'forgotPasswordForm');
$router->post('/forgot-password', 'AuthController', 'forgotPassword');
$router->get('/reset-password',   'AuthController', 'resetPasswordForm');
$router->post('/reset-password',  'AuthController', 'resetPassword');

// ─── Dashboard ───
$router->get('/dashboard',  'DashboardController', 'index');

// ─── Profil ───
$router->get('/profile',    'ProfileController', 'index');
$router->post('/profile',   'ProfileController', 'update');

// ─── Suivi quotidien ───
$router->get('/log',        'DailyLogController', 'index');
$router->post('/log',       'DailyLogController', 'store');
$router->get('/log/history','DailyLogController', 'history');
$router->get('/log/edit/{id}',  'DailyLogController', 'editForm');
$router->post('/log/edit/{id}', 'DailyLogController', 'update');
$router->post('/log/delete/{id}', 'DailyLogController', 'delete');

// ─── API interne (JSON) ───
$router->get('/api/chart/consumption', 'DashboardController', 'apiConsumption');
$router->get('/api/chart/cravings',    'DashboardController', 'apiCravings');
$router->get('/api/stats',             'DashboardController', 'apiStats');

// ─── Admin ───
$router->get('/admin',               'AdminController', 'index');
$router->get('/admin/users',         'AdminController', 'users');
$router->post('/admin/users/delete/{id}', 'AdminController', 'deleteUser');
$router->get('/api/admin/stats',     'AdminController', 'apiStats');
