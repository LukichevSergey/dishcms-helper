

<?php if(!$result['status']): ?>
<a class="default-button" href="<?php echo Yii::app()->createUrl('admin/subscribe/clear'); ?>">Назад</a>

<div class="error">
	Произошла ошибка, возможно вы не выбрали пользователей для отправления?
</div>

<?php $this->clearSession(); ?>
<?php else: ?>

<a class="default-button" href="<?php echo Yii::app()->createUrl('admin/subscribe/clear'); ?>">Назад</a>



<div id="results" class="blocks buttons">Колличество получателей: <?php echo $result['email_count']+1; ?></div>
<div id="emails" class="blocks buttons">
	<?php foreach ($result['email'] as $key => $email) { 
		
		echo $email."<br>";
		#echo $email['adress'].": ".$send_status."<br>"; 

		} ?>
</div>


<div class="message_block">
	<div id="show_body" class="blocks buttons c_pointer" ><?php echo $send_info['theme']; ?></div>
	<div id="body" class="blocks buttons">
		<?php echo $send_info['message']; ?>
	</div>
</div>

<?php endif; ?>

<script>

$( document ).ready(function() {

	$( "#show_body" ).click(function() {
		  if ( $( "#body" ).is( ":hidden" ) ) {
		    $( "#body" ).slideDown( "fast" );
		  } else {
		    $( "#body" ).slideUp( "fast" );
		  }
		//$( "#body" ).slideDown( "fast" );
	});

	$( "#results" ).click(function() {
		  if ( $( "#emails" ).is( ":hidden" ) ) {
		    $( "#emails" ).slideDown( "fast" );
		  } else {
		    $( "#emails" ).slideUp( "fast" );
		  }
		//$( "#body" ).slideDown( "fast" );
	});
});

</script>

<style>
	
.error {
padding: 20px;
background: rgba(229, 135, 135, 0.61);
margin: 20px;
}

</style>