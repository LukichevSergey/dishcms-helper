/**
 * Feedback admin  
 */

var FeedbackAdmin = {
	/**
	 * Initialize
	 * @param string feedbackId feedback id.
	 * @returns
	 */
	init: function(feedbackId) {
		$box = $("#feedback-" + feedbackId);
		$box.on('click', ".mark", function(){
			t = $(this);
			$.ajax({
				type: "POST",
			    url: "/cp/feedback/" + feedbackId + "/changeCompleted",
			    data: {id: $(this).data('item')},
			    dataType: "json",
			    success: function(data) {
			    	if(data.success) {
			    		if(!data.status) {
			    			$(t).removeClass('unmarked');
			    		} else {
			    			$(t).addClass('unmarked');
			    		}
			    		$(".feedback-" + feedbackId + "-count-in-title").text(data.count);
				    }
				    else {
				    	alert(data.message);
				    }
			    }
			});
		});
		$box.on("click", ".feedback-btn-remove", function() {
			if(confirm("Подтвердите удаление.")) {
				$.post(
					"/cp/feedback/" + feedbackId + "/delete", 
					{id: $(this).data('item')}, 
					function(json) {
						$(".feedback-" + feedbackId + "-count-in-title").text(json.uncompletedCount);
						$("#feedback-row-" + json.id).remove();   
					}, 
					"json"
				); 
			}
		});
	}
}