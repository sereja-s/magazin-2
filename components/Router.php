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
	 * Конструктор
	 */
	public function __construct()
	{
		// Путь к файлу с роутами
		$routesPath = ROOT . '/config/routes.php';

		//echo 'Путь к базовой директории-ROOT: ' . ROOT . '<br><br>';


		// Получаем роуты из файла
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
	 * Метод для обработки запроса
	 */
	public function run()
	{


		//echo 'Class: Router, method: run<br><br>';


		//echo 'Все прописанные маршруты из массива в переменной: $this->routes: <br><br>';
		//echo '<pre>';
		//print_r($this->routes);
		//echo '<pre>';

		//var_dump($this->routes);


		// Получаем строку запроса
		$uri = $this->getURI();

		//echo 'строка запроса (в $uri): ' . $uri;


		// Проверяем наличие такого запроса в массиве маршрутов (routes.php)
		foreach ($this->routes as $uriPattern => $path) {

			//echo "<br> шаблон запроса(uriPattern) => внутренний путь(path):  $uriPattern => $path";


			// Сравниваем $uriPattern и $uri
			if (preg_match("~^$uriPattern~", $uri)) {

				//echo '<br>Где ищем: uri (запрос который набрал пользователь): ' . $uri;
				//echo '<br>Что ищем: uriPattern (совпадене из правила(шаблона запроса)): ' . $uriPattern;
				//echo '<br>Кто обрабатывает: path (внутреннй путь к нужному экшену): ' . $path;


				// Получаем внутренний путь из внешнего согласно правилу.
				$internalRoute = preg_replace("~^$uriPattern~", $path, $uri);

				//echo '<br><br>Получаем внутренний путь в $internalRoute: ';
				//var_dump($internalRoute);
				//echo $internalRoute;

				// Определить контроллер, action, параметры
				// разделим строку по символу: /
				$segments = explode('/', $internalRoute);

				//echo '<pre>';
				//echo 'разделим на сегменты по символу: / и сохраним в $segments: <br>';
				//print_r($segments);
				//echo '<pre>';


				$controllerName = array_shift($segments) . 'Controller';

				//echo 'формируем имя контроллера: ' . $controllerName;


				// сделаем 1-ю букву в имени контроллера заглавной
				$controllerName = ucfirst($controllerName);

				//echo '<br>сделаем 1-ю букву в имени контроллера заглавной: ' . $controllerName;


				// получим название экшена (метода в контроллере) Это 2-ой элемент массива в $segments
				$actionName = 'action' . ucfirst(array_shift($segments));

				//echo '<br>формируем назване экшена: ' . $actionName;

				// Убираем в названии экшена символы: ?i=1 (если есть)
				$actionName = rtrim($actionName, '?i=1');

				//echo '<br>Убираем в названии экшена: ?i=1' . $actionName;


				// получим то что осталось от строки внутреннего пути (массив с параметрами)
				$parameters = $segments;

				//echo '<br><br>массив с параметрами: <br> ';
				//print_r($parameters);
				//echo '<pre>';

				// Подключить файл класса-контроллера
				// прописываем путь к файлу (конроллеру), который нужно подключить
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
					break;
				}
			}
		}
	}
}
