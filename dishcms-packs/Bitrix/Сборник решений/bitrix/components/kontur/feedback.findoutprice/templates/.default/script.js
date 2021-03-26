$(function() {
	$(document).on("click", ".findoutprice__form :submit", function(e) {
		e.stopImmediatePropagation();
		var $form=$(e.target).parents("form:first");
		$.post("/findoutprice/", {id: $form.find("[name='id']").val(), data: $form.serializeArray(), validate: true}, function(response) {
			$form.find(".findoutprice__form-item").removeClass("error");
			if(!response.success) {
				for(var name in response.errors) {
					$form.find("[name='"+name+"']").parents(".findoutprice__form-item:first").addClass("error");
				}
			}
			else {
				$.post("/findoutprice/", {id: $form.find("[name='id']").val(), send: true, data: $form.serializeArray()}, function(response) {
					if(response.success) {
						$(".feedback__findoutprice").html($.parseHTML(
							'<div class="success">Ваша заявка успешно отправлена</div>'
							+ '<center><a href="javascript:;" onclick="$.fancybox.close()" class="btn1">Закрыть</a></center>'
						));
					}
				}, "json");
			}
		}, "json");
		return false;
	});
});
