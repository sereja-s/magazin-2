<?php

/*
 * Класс Pagination для генерации постраничной навигации
 */

class Pagination
{

	/** 
	 * Ссылок навигации на страницу
	 */
	private $max = 10;

	/** 
	 * Ключ для GET, в который пишется номер страницы
	 */
	private $index = 'page';

	/** 
	 * Текущая страница
	 */
	private $current_page;

	/** 
	 * Общее количество записей
	 */
	private $total;

	/** 
	 * Записей на страницу
	 */
	private $limit;

	/** 
	 * количество страниц
	 */
	private $amount;

	/**
	 * Метод-конструктор инициализирует объект
	 * (Запуск необходимых данных для навигации)
	 */
	public function __construct($total, $currentPage, $limit, $index)
	{
		# Устанавливаем общее количество записей
		$this->total = $total;

		# Устанавливаем количество записей на страницу
		$this->limit = $limit;

		# Устанавливаем ключ в url
		$this->index = $index;

		# Устанавливаем количество страниц
		$this->amount = $this->amount();

		# Устанавливаем номер текущей страницы
		$this->setCurrentPage($currentPage);
	}

	/**
	 * Метод для вывода ссылок
	 * (вернёт HTML-код со ссылками навигации)
	 */
	public function get()
	{
		# Для записи ссылок
		$links = null;

		# Получаем ограничения для цикла
		$limits = $this->limits();

		// в переменную сохраним список в который (в цикле) будем добавлять нужные страницы
		$html = '<ul class="pagination">';

		# Генерируем ссылки
		for ($page = $limits[0]; $page <= $limits[1]; $page++) {

			# Если текущая это текущая страница, ссылки нет и добавляется класс active
			if ($page == $this->current_page) {

				$links .= '<li class="active"><a href="#">' . $page . '</a></li>';
			} else {

				# Иначе генерируем ссылку
				$links .= $this->generateHtml($page);
			}
		}

		# Если ссылки создались
		if (!is_null($links)) {

			# Если текущая страница не первая
			if ($this->current_page > 1)

				# Создаём ссылку "На первую"
				$links = $this->generateHtml(1, '&lt;') . $links;

			# Если текущая страница не последняя
			if ($this->current_page < $this->amount)

				# Создаём ссылку "На последнюю"
				$links .= $this->generateHtml($this->amount, '&gt;');
		}

		$html .= $links . '</ul>';

		# Возвращаем html
		return $html;
	}

	/**
	 * Для генерации HTML-кода ссылки
	 * на вход: 1- $page- номер страницы 2- $text- текст ссылки 
	 */
	private function generateHtml($page, $text = null)
	{
		# Если текст ссылки не указан
		if (!$text)

			# Указываем, что текст - цифра страницы
			$text = $page;

		$currentURI = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
		$currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);

		# Формируем HTML код ссылки и возвращаем
		return
			'<li><a href="' . $currentURI . $this->index . $page . '">' . $text . '</a></li>';
	}

	/**
	 * Метод вернёт массив с началом и концом отсчёта
	 * (Для получения, откуда стартовать)	
	 */
	private function limits()
	{
		# Вычисляем ссылки слева (чтобы активная ссылка была посередине)
		$left = $this->current_page - round($this->max / 2);

		# Вычисляем начало отсчёта
		$start = $left > 0 ? $left : 1;

		# Если впереди есть как минимум $this->max страниц
		if ($start + $this->max <= $this->amount) {

			# Назначаем конец цикла вперёд на $this->max страниц или просто на минимум
			$end = $start > 1 ? $start + $this->max : $this->max;
		} else {

			# Конец - общее количество страниц
			$end = $this->amount;

			# Начало - минус $this->max от конца
			$start = $this->amount - $this->max > 0 ? $this->amount - $this->max : 1;
		}

		# Возвращаем
		return
			array($start, $end);
	}

	/**
	 * Для установки текущей страницы 	 
	 */
	private function setCurrentPage($currentPage)
	{
		# Получаем текущий номер страницы
		$this->current_page = $currentPage;

		# Если текущая страница больше нуля
		if ($this->current_page > 0) {

			# Если текущая страница больше общего количества страниц
			if ($this->current_page > $this->amount)

				# Устанавливаем страницу на последнюю
				$this->current_page = $this->amount;
		} else
			# Устанавливаем страницу на первую
			$this->current_page = 1;
	}

	/** 
	 * Для получения общего числа страниц
	 */
	private function amount()
	{
		# Делим и возвращаем
		return ceil($this->total / $this->limit);
	}
}
