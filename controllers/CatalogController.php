<?php

/**
 * Контроллер CatalogController
 * Каталог товаров
 */
class CatalogController
{

	/**
	 * Action для страницы "Каталог товаров"
	 */
	public function actionIndex()
	{
		// Список категорий для левого меню
		$categories = Category::getCategoriesList();

		// Список последних товаров
		$latestProducts = Product::getLatestProducts(3);

		// Подключаем вид
		require_once(ROOT . '/views/catalog/index.php');

		return true;
	}

	/**
	 * Action для страницы "Категория товаров"
	 */
	public function actionCategory($categoryId, $page = 1)
	{
		// Список категорий для левого меню
		$categories = Category::getCategoriesList();

		// Список товаров в категории
		$categoryProducts = Product::getProductsListByCategory($categoryId, $page);

		// Общее количетсво товаров конкретной категории (необходимо для постраничной навигации)
		$total = Product::getTotalProductsInCategory($categoryId);

		// Создаем объект Pagination - постраничная навигация (здесь- 'page-' это ключ который будет указан в адресе страницы)
		$pagination = new Pagination($total, $page, Product::SHOW_BY_DEFAULT, 'page-');

		// Подключаем вид
		require_once(ROOT . '/views/catalog/category.php');

		return true;
	}
}
