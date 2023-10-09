    <div class="page-buffer"></div>
    </div>

    <footer id="footer" class="page-footer"><!--Footer-->
    	<div class="footer-bottom">
    		<div class="container">
    			<div class="row">
    				<p class="pull-left">Copyright © <?php echo date("Y"); ?></p>
    				<a href="https://saitpostroen.ru/">
    					<p class="pull-right">СайтПостроен</p>
    				</a>
    			</div>
    		</div>
    	</div>
    </footer>



    <script src="/template/js/jquery.js"></script>
    <script src="/template/js/jquery.cycle2.min.js"></script>
    <script src="/template/js/jquery.cycle2.carousel.min.js"></script>
    <script src="/template/js/bootstrap.min.js"></script>
    <script src="/template/js/jquery.scrollUp.min.js"></script>
    <script src="/template/js/price-range.js"></script>
    <script src="/template/js/jquery.prettyPhoto.js"></script>
    <script src="/template/js/main.js"></script>

    <script>
    	/* отправка асинхронного запроса ддя подсчёта и вывода количества товара в корзине пользователя */

    	// код внутри блока должен быть выполнен только после загрузки документа(страницы)
    	$(document).ready(function() {

    		// при клике на кнопки с классом: add-to-cart (здесь- В корзину)
    		$(".add-to-cart").click(function() {

    			// получим идентификатор товара, который нужно добавить в корзину (data-атрибут: data-id = "id" товара, указан для каждой кнопки)
    			var id = $(this).attr("data-id");

    			// Сформируем асинхронный запрос (применется метод: POST):

    			// на вход: 1- адрес на который отправляется запрос, 2- параметры (здесь пустые т.к. идентификатор мы будем 
    			// отправлять в ссылке), 3- функция, которая будет обрабатывать результат сформированный контроллером: 
    			// CartController, в методе; actionAddAjax($id)
    			// результат(кол-во товаров в корзине) будет попадать в переменную: data
    			$.post("/cart/addAjax/" + id, {}, function(data) {

    				// результат поместим в счётчик (он имеет id="cart-count")
    				$("#cart-count").html(data);
    			});

    			return false;
    		});
    	});
    </script>

    </body>

    </html>