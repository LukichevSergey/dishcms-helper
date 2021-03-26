/**
 * Скрипт обработки ajax форм
 */
$(window).on('onExtendFormsFormAjaxSuccess', function(e,response) {
	let afid=response.data.afid;

	let $form=afid ? $('#' + afid) : $('body');
	
	function error(attribute, error) {
		let $field=$form.find('[name*="[' + attribute + ']"]');
		if($field.length) {
			$field.parent().addClass('error');
			let $error=$('#' + $field.attr('id') + '_em_');
			if($error.length) {
				$error.html(error);
                $error.show();
			}
		}
	}
	
	function errors(errors) {
		$form.find('.error').removeClass('error');
        for(var attribute in errors) {
        	error(attribute, errors[attribute]);
        }
    };

	if(response.success){
		$(window).trigger('onExtendFormsFormAjaxSuccesed',[response, $form]);
	}
	else {
		errors(response.errors);
	}
});