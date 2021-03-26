<div id="<?=$this->id?>">
	<?foreach($this->data as $slide):?>
		<span class="cycle-slide"><?=$slide->{$this->attributeLink} 
			? CHtml::link(CHtml::image($slide->{$this->attributeSrc}), $slide->{$this->attributeLink}) 
			: CHtml::image($slide->{$this->attributeSrc});
		?></span>
	<?endforeach?>
</div>


