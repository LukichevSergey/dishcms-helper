function KFF_InitForm(formId, hash) {
    $(document).on("click", "#" + formId + " :submit", function(e) {
        var $form=$("#" + formId);
        var $submit=$(e.target).closest(":submit");
        e.stopImmediatePropagation();
        $submit.attr("disabled", "disabled");        
        $.post($form.attr("action"), {hash: hash, data: $form.serializeArray()}, function(response) {
            $form.find(".error").removeClass("error");
            if(!response.success) {
                for(var name in response.errors) {
                    $form.find("[name='"+name+"']").addClass("error");
                    $form.find("[name='"+name+"']").parent().addClass("error");
                }
                $submit.removeAttr("disabled");
            }
            else {
                $form.html($.parseHTML(
                    '<div class="fb-success">Ваша заявка успешно отправлена</div>'
                    // + '<center><a href="javascript:;" onclick="$.fancybox.close()" class="btn1">Закрыть</a></center>'
                ));
            }
        }, "json");
        return false;
    });
}
