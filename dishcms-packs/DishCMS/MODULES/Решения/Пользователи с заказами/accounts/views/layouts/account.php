<?php $this->beginContent('//layouts/main'); ?>
    <div class="inner-page-head account-page-head">
    	<div class="container">
    		<div class="inner-page-head-inner">
    			<div class="inner-page-head-left">
    				<h1>Рersonal area</h1>
    			</div>
    			<div class="inner-page-head-right">
    				<div class="inner-page-head-img">
    					<img src="/images/pp.png">
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    
    <div class="account-hamburger-wrapper d-lg-none">
    	<div class="container">
    		<div class="account-hamburger">
    			<button class="hamburger hamburger--spin" type="button">
    				<span class="hamburger-box">
    					<span class="hamburger-inner"></span>
    				</span>
    			</button>
    			<div class="account-menu-label">
    				Menu
    			</div>
    		</div>
    	</div>
    </div>
    
    <div class="container">
        <div class="account">
    		<div class="row">
    			<div class="col-lg-8">
					<?= $content ?>
				</div>

    			<div class="col-lg-4 d-lg-block d-none">
    				<aside class="account-side">
    					<?php $this->widget('\accounts\widgets\AccountMenu'); ?>
    				</aside>
    			</div>
    		</div>
    	</div>
    </div>

<?php $this->endContent(); ?>