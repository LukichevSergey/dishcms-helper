layouts/index.php
<script>
$(document).on('click','a[href="/"]',function(event){event.preventDefault();$('html, body').animate({scrollTop:$('body').offset().top},500);});
$(document).on('click','a[href^="/#"]',function (event){event.preventDefault();$('html, body').animate({scrollTop: $($.attr(this, 'href').replace(/^\/#/, '#')).offset().top},500);});
</script>

