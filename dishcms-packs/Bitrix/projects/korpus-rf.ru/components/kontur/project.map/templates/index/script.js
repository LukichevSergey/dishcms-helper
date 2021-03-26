(function(){
	$(document).ready(function(){
		let failMessage='<p class="g-filter-empty">На сегодняшний день в данном регионе работы не проводились. Мы готовы к сотрудничеству!</p>'
			+ '<div style="background-color:#8eb4e3;max-width:250px;margin:0 auto">'
			+ '<a data-href="/ajax/callback.php" data-toggle="modal" style="color:#fff;width:100%" data-target="#modal_callback" href="javascript:void(0)" class="btn js_ajax_modal"><span>Перезвоните мне</span></a></div>';
		$(document).on('click', '.js-geography svg path[data-id]', function(e) {
			let el=$(e.target).closest('path');$('.js-geography svg path').removeAttr('data-selected');el.attr('data-selected', 1);
			$.post('/local/components/kontur/project.map/templates/index/ajax.php', {id: el.data('id')}, function(response) {
				if(response.success) {
					let html='<div class="g-filter-title">' + response.data.NAME + '</div>';
					if($.isArray(response.data.PROPERTY_REGIONS_DESCRIPTION) && (response.data.PROPERTY_REGIONS_DESCRIPTION.length > 0)) {
						response.data.PROPERTY_REGIONS_DESCRIPTION.forEach(function(name, idx) {
							html+='<div class="g-filter-item"><div class="g-filter-item-name">' + name + '</div>';
							if(typeof response.data.PROPERTY_REGIONS_VALUE[idx] != 'undefined') {
								html+='<div class="g-filter-item-desc">' + response.data.PROPERTY_REGIONS_VALUE[idx] + '</div>';
							}
							html+='</div>';
						});
						
						if(response.data.DETAIL_PAGE_URL) {
							html+='<div class="g-show-all"><a href="' + response.data.DETAIL_PAGE_URL + '">Посмотреть все проекты</a></div>';
						}
					}
					else {
						html+=failMessage;
					}
					$('.js-geography .geography-filter').html($.parseHTML(html));
				}
				else {
					$('.js-geography .geography-filter').html($.parseHTML(failMessage));
				}
			}, 'json');
		});
	});
})();