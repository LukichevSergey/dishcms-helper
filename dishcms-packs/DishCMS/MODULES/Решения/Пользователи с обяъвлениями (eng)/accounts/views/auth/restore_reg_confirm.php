<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
/** @var string $code */

?>
<div class="popup-form-title">Your access has not yet been activated.</div> 
<br/>
<center>
	<p>You can <?=\CHtml::link('request another letter', ['/accounts/reg/confirm', 'c'=>$code])?> to confirm your account.</p>
</center>
<br/>
<center><a href="/" class="btn">Go to Home</a></center>