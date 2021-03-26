
function productUpdateCounts() {
	$.post('/dCart/counts', function(response) {
		if(response.success) {
			response.data.items.forEach(function(item){
				$('.cart__count[data-id="'+item.id+'"]').text(item.count);
			});
		}
	}, 'json');
}

$(document).ready(function() {
	function productUpdateCount(e, cls, url) {
		let id=$(e.target).closest(cls).data('id');
		if (!isNaN(+id)) {
			$.post(url, {id: id}, function(response){
				if(response.success) {
					$(".dcart-total-count").text(response.data.total);
					$('.cart__count[data-id="'+response.data.id+'"]').text(response.data.count);
				}
			}, 'json');
		}
	}
	
	$(document).on('click', '.product-item .product__to-cart .cart__minus', function(e) {
		productUpdateCount(e, '.cart__minus', '/dCart/dec');
	});
	
	$(document).on('click', '.product-item .product__to-cart .cart__plus', function(e) {
		productUpdateCount(e, '.cart__plus', '/dCart/inc');
	});
	
	if($(".product-item").length) {
		setInterval(productUpdateCounts, 7000);
		productUpdateCounts();
	}
});
