<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<div class="popup-form-title">Restore Password</div> 
<br/>
<center>
	<p>To your email address <b> <?= $account->email; ?> </b> a letter was sent with a link to restore access to your personal account.</p>
</center>
<br/>
<center><a href="/" class="btn">Go to Home</a></center>
