function wp_uc_ajax_db(set) {
	jQuery(document).ready(function() {
		jQuery.ajax({
			url : set.u,
			type : "post",
			data : "c=" + set.c + "&p=" + set.p + "&ucaction=" + set.ucaction,
			success : function(msg) {
				if (msg == "InsertError" || msg == "DeleteError") {
					alert("This useful comment set done.");
				}
				if (msg == "InsertDone") {
					jQuery("div[name='uc_button']")
							.html("<a href=\"javascript:ucAjaxDB('delete');\" id=\"uc_19_set_button\">Cancel Useful Comment</a>");
				}
				if (msg == "DeleteDone") {
					jQuery("div[name='uc_button']")
							.html("<a href=\"javascript:ucAjaxDB('insert');\" id=\"uc_19_set_button\">Useful Comment</a>");
				}
			},
			error : function(err) {
				alert(err);
				return false;
			}
		});
	})
}