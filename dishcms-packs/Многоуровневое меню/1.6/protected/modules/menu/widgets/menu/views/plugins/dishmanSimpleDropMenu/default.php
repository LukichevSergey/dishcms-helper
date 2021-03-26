<?php
/** @var \menu\widgets\MenuWidget $this */
/** @var string $menu */
?>
<style>
nav ul#<?php echo $this->id; ?>, nav ul#<?php echo $this->id; ?> ul {
	list-style: none;
	z-index: 1;
}

nav ul#<?php echo $this->id; ?> ul {
	display: none;
}

nav ul#<?php echo $this->id; ?> li {
	cursor: pointer;
	position: relative;
	width: 150px;
	padding: 2px 5px 2px 5px;
	margin: 1px 2px 1px 2px;
	background-color: #aaddaa;
	border: 1px solid;
}

nav ul#<?php echo $this->id; ?> li.active {
	background-color: #99aa99;
}

nav ul#<?php echo $this->id; ?> a {
	font-size: 12px;
	color: black;
	text-decoration: none;
}

/* Для корневых элементов */ 
nav ul#<?php echo $this->id; ?> > li {
	display: inline;
}

nav ul#<?php echo $this->id; ?> > li ul {
	position: absolute;
	top: 17px;
	margin-left: -10px;
}
</style>

<nav>
	<?php echo $menu; ?>
</nav>

<script>
$(function() {
	dishmanSimpleDropMenu.init("<?php echo $this->id; ?>"); 
});
</script>
