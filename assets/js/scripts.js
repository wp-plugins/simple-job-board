/*
 Function Calls for jQuery
 by Sehrish Iftikhar for PressTigers, http://presstigers.com
 
 Version 1.0.0
 Copyright (c) 2015 PressTigers, http://presstigers.com
 */

(function ($) {
    "use_strict";

    $(document).ready(function () {
        $(".jobpost_form").on("submit", function () {
            var datastring = new FormData(document.getElementById("cs-assignments-form"));
            if (0 === document.getElementById("applicant_resume").files.length) {
                document.getElementById('file_error_message').innerHTML = 'Please Attach Resume';
                return false;
            }
            $.ajax({
                url: application_form.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: datastring,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {

                    $('#jobpost_form_status').html('Submitting.....');
                    $("#jobpost_submit_button").attr('disabled', 'diabled');
                },
                success: function (response) {
                    if (response['success'] == true) {
                        $('.jobpost_form').slideUp();
                        $('#jobpost_form_status').html('Your application has been received. We will get back to you soon.');
                    }
                    if (response['success'] == false) {
                        $('#jobpost_form_status').html(response['error'] + ' Your application could not be processed.');
                        $("#jobpost_submit_button").removeAttr('disabled');
                    }

                }
            });
            return false;
        });

        $('.datepicker').datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
})(jQuery);