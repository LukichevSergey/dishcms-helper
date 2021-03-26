        private $_assetsBaseUrl;

		$this->_assetsBaseUrl = \AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array('js/classes/DCartMiniWidget.js', 'js/dcart_mini_cart_widget.js'),
			'css' => 'css/mini_cart.css'
		));

		public function getAssetsBaseUrl()
		{
			return $this->_asstsBaseUrl;
		}
