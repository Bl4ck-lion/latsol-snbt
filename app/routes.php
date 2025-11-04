<?php
$router->add('/', 'HomeController', 'index');
$router->add('/register', 'AuthController', 'showRegistrationForm', 'GET');
$router->add('/register', 'AuthController', 'register', 'POST');
$router->add('/login', 'AuthController', 'showLoginForm', 'GET');
$router->add('/login', 'AuthController', 'login', 'POST');
$router->add('/logout', 'AuthController', 'logout', 'POST');

$router->add('/dashboard', 'DashboardController', 'index', 'GET');

$router->add('/attempt', 'AttemptController', 'show', 'GET');
$router->add('/attempt/start', 'AttemptController', 'start', 'POST');
$router->add('/attempt/answer', 'AttemptController', 'answer', 'POST');
$router->add('/attempt/submit', 'AttemptController', 'submit', 'POST');
$router->add('/attempt/status', 'AttemptController', 'status', 'GET');

$router->add('/scoreboard', 'ScoreboardController', 'index', 'GET');
$router->add('/api/scoreboard', 'ApiController', 'scoreboard', 'GET');

$router->add('/battle/join', 'BattleController', 'join', 'POST');
$router->add('/battle/status', 'BattleController', 'status', 'GET');
$router->add('/battle/answer', 'BattleController', 'answer', 'POST');
$router->add('/battle/finish', 'BattleController', 'finish', 'POST');

$router->add('/admin/generate_daily', 'AdminController', 'generateDaily', 'POST');

$router->add('/admin', 'AdminController', 'index', 'GET');
$router->add('/admin/generate_daily', 'AdminController', 'generateDaily', 'POST');
$router->add('/admin/rebuild_scoreboard', 'AdminController', 'rebuildScoreboard', 'POST');