
jQuery(document).ready(function () {
    jQuery("#ms_cu_form").on("submit", function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'ms_cu_submit_form_data'); // Important for WP AJAX
        
        jQuery.ajax({
            url: ms_cu_object.ajax_url,
            data: formData,
            dataType: "json",
            method: "POST",
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    jQuery("#success-message").html(response.message).css({
                        color: "green",
                    });
					//reset form data
					jQuery("#ms_cu_form")[0].reset();
                   
                } else {
                    jQuery("#success-message").html(response.message).css({
                        color: "red",
                    });
                }
            }
        });
    });
});
