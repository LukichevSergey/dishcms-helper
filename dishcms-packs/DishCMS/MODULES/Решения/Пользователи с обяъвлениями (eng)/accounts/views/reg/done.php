<?php
/** @var \accounts\controllers\RegController $this */
use accounts\components\helpers\HAccount;

?>
<div class="popup-form-title">Registration successful!</div>
<br/>
<?= HAccount::settings()->reg_done_text; ?>
<br/>
<center><a href="/signin" class="btn">Sign In</a></center>
