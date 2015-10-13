(function ($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note that this assume you're going to use jQuery, so it prepares
     * the $ function reference to be used within the scope of this
     * function.
     *
     * From here, you're able to define handlers for when the DOM is
     * ready:
     *
     * $(function() {
     *
     * });
     *
     * Or when the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and so on.
     *
     * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
     * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
     * be doing this, we should try to minimize doing that in our own work.
     */
    
    $(document).ready(function () {
        
        $(".jobpost_form").on("submit", function () {
            
            var applicant_resume = $("#applicant_resume");
            var jobpost_submit_button = $('#jobpost_submit_button');
            var jobpost_form_status = $('#jobpost_form_status');
            var datastring = new FormData(document.getElementById("cs-assignments-form"));

            if (0 === document.getElementById("applicant_resume").files.length) {
                document.getElementById('file_error_message').innerHTML = 'Please Attach Resume';
                return false;
            }

            /**
             *  Uploded File Extensions Checks
             *  Get Uploded File Ext
             */
            var file_ext = applicant_resume.val().split('.').pop().toLowerCase();

            // All Allowed File Extensions
            var allowed_file_exts = application_form.allowed_extensions;

            // Settings File Extensions && Getting value From Script Localization
            var settings_file_exts = application_form.setting_extensions;
            var selected_file_exts = (('yes' === application_form.all_extensions_check) || null == settings_file_exts) ? allowed_file_exts : settings_file_exts;

            if ($.inArray(file_ext, selected_file_exts) > -1) {
                jobpost_submit_button.attr('disabled', false);
            }
            else {
                alert('This is not an allowed file type.');
                applicant_resume.val('');
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

                    jobpost_form_status.html('Submitting.....');
                    jobpost_submit_button.attr('disabled', 'diabled');
                },
                success: function (response) {
                    if (response['success'] == true) {
                        $('.jobpost_form').slideUp();
                        jobpost_form_status.html('Your application has been received. We will get back to you soon.');
                    }
                    if (response['success'] == false) {
                        jobpost_form_status.html(response['error'] + ' Your application could not be processed.</div>');
                        jobpost_submit_button.removeAttr('disabled');
                        applicant_resume.val('');
                    }

                }
            });
            return false;
        });
        
        /* Date-time picker */
        $('.datepicker').datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
        
        if ( 'logo-detail' === application_form.job_listing_view ) {
            $(".company-logo").show();
            $(".description").show();
        }
        
        if ( 'without-logo' === application_form.job_listing_view ) {
            $(".description").show();
            $(".company-logo").hide();
        }   
        
        if ( 'without-logo-detail' === application_form.job_listing_view ) {
            $(".company-logo").hide();
            $(".description").hide();
        }
    });

})(jQuery);
