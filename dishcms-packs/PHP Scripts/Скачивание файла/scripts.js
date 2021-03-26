$(document).ready(function(){
    $("a[href*='/files/']").each(function() { $(this).attr("href", "/download.php?f="+$(this).attr("href")); });
});