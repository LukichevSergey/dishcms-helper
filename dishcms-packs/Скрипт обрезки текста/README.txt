Использование 

Скрипт:

(function(selector) {
	var maxHeight=100,
		togglerClass="read-more",
		smallClass="small",
		labelMore="Читать далее",
		labelLess="Скрыть";
		
	$(selector).each(function() {
		var $this=$(this),
			$toggler=$($.parseHTML('<a href="#" class="'+togglerClass+'">'+labelMore+'</a>'));
		$this.after(["<div>",$toggler,"</div>"]);
		$toggler.on("click", $toggler, function(){
			$this.toggleClass(smallClass);
			$this.css('height', $this.hasClass(smallClass) ? maxHeight : $this.attr("data-height"));
			$toggler.text($this.hasClass(smallClass) ? labelMore : labelLess);
			return false;
		});
		$this.attr("data-height", $this.height());
		if($this.height() > maxHeight) {
			$this.addClass(smallClass);
			$this.css('height', maxHeight);
		}
		else {
			$toggler.hide();
		}
	});
})(".category-description");

Стили (LESS):
// <<< в стилях нового дишмана уже есть
.transition (@transition) {
    -webkit-transition: @transition;
    -moz-transition: @transition;
    -o-transition: @transition;
    -ms-transition: @transition;
    transition: @transition;
}
// >>>

.category-description {
    overflow: hidden;
    position: relative;
    max-height: auto;
    .transition(all 0.5s);

    &.small{

        &:after{
            content: '';
            position: absolute;
            width: 100%;
            height: 45px;
            bottom: 0;
            right: 0;
            background: url('../images/bg-t.png') left bottom repeat-x;
            z-index: 2;
        }
    }
}

// <<< стили для ссылки  
.read-more {
    margin-bottom: 20px;
    display: block;
    text-align: right;
}
// >>>

В HTML шаблоне (обычно это category-description):

<div class="category-description">		
	текст
</div>