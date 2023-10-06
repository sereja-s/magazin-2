<?php

/**
 * Класс Router
 * Компонент для работы с маршрутами
 */
class Router
{

	/**
	 * Свойство для хранения массива роутов
	 * @var array 
	 */
	private $routes;

	/**
	 * Конструктор (читает и запоминает роуты(маршруты))
	 */
	public function __construct()
	{
		// Путь к файлу с роутами (путь к базовой директории . 'путь к файлу с маршрутами')
		$routesPath = ROOT . '/config/routes.php';

		// Получаем(подключаем) роуты из файла с роутами и сохраняем в свойстве класса
		$this->routes = include($routesPath);
	}

	/**
	 * Возвращает строку запроса
	 */
	private function getURI()
	{
		if (!empty($_SERVER['REQUEST_URI'])) {

			return trim($_SERVER['REQUEST_URI'], '/');
		}
	}

	/**
	 * Метод для обработки запроса (анализирует запрос и передаёт управление)
	 */
	public function run()
	{
		// Получаем строку запроса
		$uri = $this->getURI();

		// Проверяем наличие такого запроса в массиве маршрутов: routes.php
		foreach ($this->routes as $uriPattern => $path) {

			// Сравниваем $uriPattern и $uri 
			// Условие выполнится если шаблон (1-ый параметр метода) есть в строке запроса (2-ой параметр метода)
			// здесь ~ это знак разделитель вместо знака: / (т.к. знаки / могут содержаться в шаблоне)
			if (preg_match("~^$uriPattern~", $uri)) {

				//echo '<br>Где ищем: uri (запрос который набрал пользователь): ' . $uri;
				//echo '<br>Что ищем: uriPattern (шаблона запроса): ' . $uriPattern;
				//echo '<br>Кто обрабатывает: path (внутреннй путь к нужному экшену): ' . $path;


				// ОПРЕДЕЛИМ КАКОЙ КОНТРОЛЛЕР И ЭКШЕН ОБРАБАТЫВАЕТ ЗАПРОС:

				// Получаем внутренний путь из внешнего согласно правилу.
				$internalRoute = preg_replace("~^$uriPattern~", $path, $uri);

				// разделим строку по символу: / 
				// (В результате получим два элемента: 1- относится к контроллеру, 2- к экщену)
				$segments = explode('/', $internalRoute);;

				// получим имя контроллера Метод забирает первый элемент массива
				$controllerName = array_shift($segments) . 'Controller';

				// сделаем 1-ю букву в имени контроллера заглавной
				$controllerName = ucfirst($controllerName);


				// Получим название экшена (метода в контроллере)
				// В оставшемся элементе массива (в $segments) делаем первую букву заглавной и добавляем к стандартному слову: action
				$actionName = 'action' . ucfirst(array_shift($segments));

				// Убираем в названии экшена символы: ?i=1 (если есть)
				$actionName = rtrim($actionName, '?i=1');


				// получим то что осталось от строки внутреннего пути (массив с параметрами)
				$parameters = $segments;


				// Подключить файл класса-контроллера
				// прописываем путь к файлу конроллера, который нужно подключить
				$controllerFile = ROOT . '/controllers/' .
					$controllerName . '.php';

				// проверяем существует ли такой файл на диске и если существует , то подключаем его
				if (file_exists($controllerFile)) {

					include_once($controllerFile);
				}

				// Создать объект класса-контроллера, вызвать метод (т.е. action)
				$controllerObject = new $controllerName;

				// Вызываем необходимый метод ($actionName) у объекта определенного класса ($controllerObject) с заданными параметрами ($parameters)
				$result = call_user_func_array(array($controllerObject, $actionName), $parameters);

				// Если метод контроллера успешно вызван, завершаем работу роутера
				if ($result != null) {

					// обрывваем цикл, который ищет совпадение в файле маршрутов
					break;
				}
			}
		}
	}
}
