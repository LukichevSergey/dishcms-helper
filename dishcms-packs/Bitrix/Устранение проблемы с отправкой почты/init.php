if(!function_exists('custom_mail')) {
    function custom_mail($to, $subject, $message, $addh = "", $addp = "") {
	$from='no-reply@' . $_SERVER['SERVER_NAME'];
	$addh=preg_replace(['/^From: .+$/mUs', '/^Reply-To: .+$/mUs'], ["From: {$from}", "Reply-To: {$from}"], $addh);
	return mail($to, $subject, $message, $addh, $addp);
    }
}
