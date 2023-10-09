<?php

// FRONT CONTROLLER

// Общие настройки (здесь- отображение ошибок)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Подключение файлов системы:
// (используем полный путь к файлам на диске)
define('ROOT', dirname(__FILE__));

// подключение всех контроллеров, компонентов и моделей (отпадает потребность их включения, на тех страницах, где они были нужны т.е. requare_once)
require_once(ROOT . '/components/Autoload.php');


// Вызов Router
// создаём экземпляр класса:
$router = new Router();
$router->run();
