<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<div class="popup-form-title">Failed to send email</div> 
<br/>
<center>
	<p>Failed to send <b> <?= $account->email; ?> </b> letter with a link to restore your access to your personal account.</p>
	<p>Please contact the technical support service of our site for further instructions</p>
</center>
<br/>
<center><a href="/" class="btn">Go to Home</a></center>