<?php if(!$model->isNewRecord):?>
	<div id="video-tabl-block">
		<?php foreach($model->video as $video):?>
			<div class="item">
				<a href="#" class="remove-video" data-remove="<?php echo $video->id;?>">Удалить видео</a>
				<?php
					/*$this->widget('ext.jwplayer.Jwplayer',array(
					    'width'=>'auto',
					    'height'=>360,
					    'file'=>'/upload/'.$video->name, // the file of the player, if null we use demo file of jwplayer
					    'image'=>$video->preview, // the thumbnail image of the player, if null we use demo image of jwplayer
					    'options'=>array(
					        //'controlbar'=>'bottom'
					    )
					));*/
					$this->widget('ext.Yiippod.Yiippod', array(
					    'video'=>'/upload/'.$video->name, //if you don't use playlist
					    'id' => 'yiippodplayer-'.$video->id,
					    'autoplay'=>false,
					    'width'=>594,
					    'view'=>6, 
					    'height'=>300,
					    'bgcolor'=>'#000'
				    ));
				?>
			</div>
		<?php endforeach;?>
	</div>
<?php endif;?>

<div id="video-items">
	<div class="row">
	    <label>Видео</label>
	    <input name="Video[]" type="file" value="">
	</div>
</div>
<a href="#" id="add-video">Добавить видео</a>
<script type="text/javascript">
	$(function(){
		$('#video-tabl-block .remove-video').click(function(){
			if(!confirm('Вы действительно хотите удалить это видео?')) return false;

			var _self = $(this);

			$.get('/admin/shop/removeVideo/' + _self.data('remove'));

			_self.closest('.item').remove();

			return false;
		});

		$('#add-video').click(function(){
			$('#video-items').append(
				'<div class="row">' +
				    '<label>Видео</label>' +
				    '<input name="Video[]" type="file" value="">' +
				'</div>'
				);

			return false;
		});
	});
</script>