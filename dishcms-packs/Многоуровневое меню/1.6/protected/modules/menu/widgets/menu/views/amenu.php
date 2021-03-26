<?php 
/* @var AMenuWidget $this */ 
?>
<div class="amenu-widget-wrapper">
	<?php echo $menu; ?>
</div>

<script type="text/javascript">
$(function(){ 
	$("#<?php echo $this->id; ?>").amenu(<?php echo \CJSON::encode($this->options); ?>); 
});
</script>