/protected/modules/DCart/widgets/BaseCartWidget.php
	public function init()
	{
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array(
				'js/classes/DCart.js',
				'js/jquery/jquery-impromptu.min.js', <----------
				'js/phpjs/json_decode.js'
			),
			'css' => array(
				'css/style.css',
				'js/jquery/jquery-impromptu.min.css', <----------
			)
		));
	}	

protected/modules/DCart/widgets/assets/js/classes/DCart.js
			$.post(url, data, function(data) {
				if(typeof(data) != 'object') {
					return DEBUG_MODE ? DCart.exit('Error: (DCart.js) Invalid server responce.') : false;
				}
				if(data.success) {
---->				$.prompt("Товар добавлен в корзину!", {buttons: [], opacity: 0.8, top: "30%", timeout: 1500, persistent: false});
					if(typeof(DCartModalWidget) == 'object') DCartModalWidget.hAddAjaxSuccess(data);


стили
div.jqi .jqimessage {
    text-align: center;
    font-size: 17px;
}