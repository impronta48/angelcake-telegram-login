<?php

//Attivo il telegram login
$routes->connect('/login', ['plugin' => 'TelegramLogin', 'controller' => 'Users', 'action' => 'tlogin']);