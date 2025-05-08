//hide show static message and number of posts
jQuery(document).ready(function ($) {
	$(".mcw_display_type_options").on("change", function () {
		let mcw_display_option = $(this).val();

		if (mcw_display_option == "recent-posts") {
			$("p#mcw-recent-posts").removeClass("mcw-hide-item");
			$("p#mcw-static-message").addClass("mcw-hide-item");
		} else if (mcw_display_option == "static-message") {
			$("p#mcw-recent-posts").addClass("mcw-hide-item");
			$("p#mcw-static-message").removeClass("mcw-hide-item");
		}
	});
});
