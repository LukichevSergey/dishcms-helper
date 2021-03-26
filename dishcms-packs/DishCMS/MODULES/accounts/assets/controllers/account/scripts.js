/**
 * Скрипт для \accounts\controllers\AccountController
 * 
 */
;$(document).ready(function() {
	var $pwd=$('.row__password'), $repwd=$('.row__repassword'), $lpwd=$('.row__lastpassword');
	function showPasswordBox() { $repwd.stop().slideDown(); $lpwd.stop().slideDown(); }	
	function hidePasswordBox() { $lpwd.stop().slideUp(); $repwd.stop().slideUp(); }
	var show=false; [$pwd, $repwd, $lpwd].forEach(function($row){if($row.find('input').val() || $row.find('.errorMessage').text()){show=true;}});
	if(show){showPasswordBox();}  
	$(document).on('keyup', '.js-profile-password', function(e) { 
		var $pwd=$(e.target).closest('.js-profile-password');
		if($pwd.val().trim().length > 0) showPasswordBox();
		else hidePasswordBox();
	});
});