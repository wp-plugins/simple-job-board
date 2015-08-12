/*
 JavaScript For Creating Dynamic Fields on Setting Page 
 by Sehrish Iftikhar for PressTigers, http://presstigers.com
 
 Version 1.0.0
 Copyright (c) 2015 PressTigers, http://presstigers.com
 */

(function ($) {
    "use_strict";

    $(document).ready(function () {
        var feature_form = $("#job_feature_form");
        var jobapp_form = $("#job_app_form");

        /*Start of Script for Setting Page Job Feature Settings*/
        $('#settings_addFeature').on("click", function () {
            var field_name_raw = $('#settings_jobfeature_name').val(); // Get Raw value.
            var job_feature_value = $('#settings_jobfeature_value').val(); //Job Feature value
            var fieldName;

            field_name_raw = field_name_raw.trim();    // Remove White Spaces from both ends.
            fieldName = field_name_raw.split(' ').join('_').toLowerCase(); //Replace white space with _.            
            if (fieldName != '') {

                var jobfeature_value_textbox;
                if ('' == job_feature_value)
                {
                    jobfeature_value_textbox = '<input type="hidden" value="empty" name="jobfeature_value[]"/>';
                }
                else {
                    jobfeature_value_textbox = '<input type="text" value="' + job_feature_value + '" name="jobfeature_value[]" >';
                }
                $('#settings_job_features').append('<li class="' + fieldName + '"><label for="' + fieldName + '">Field Name: ' + field_name_raw + '</label> <input type="hidden"  name="jobfeature[]" value="jobfeature_' + fieldName + '"  >' + jobfeature_value_textbox + ' &nbsp; <div class="button removeField" >Delete</div></li>');
                $('#settings_jobfeature_name').val(""); //Reset Field value.
                $('#settings_jobfeature_value').val(""); //Reset Field value.
            }
            else {
                alert("Please fill out feature name");
                $('#settings_jobfeature_name').focus(); //Keep focus on this inpu
            }
        });

        /*Remove Job app or job Feature Fields*/
        $('.jobpost_fields').on('click', 'li .removeField', function () {


            $(this).parent('li').remove();     // remove HTML
        });

        /*
         * On Click Save button 
         */
        $('#jobfeature_form').on('click', function () {

            feature_form.submit();

        });

        /**
         * End of Script for Setting Page Job Feature MetaBox
         * Start of Setting Page Job Application MetaBox Script
         * Add Application Field (Group Fields)
         */
        $('#app_add_field').on("click", function () {
            var app_field_raw = $('#setting_jobapp_name').val(); // Get Raw value.
            var app_field_raw = app_field_raw.trim(); // Remove White Spaces from both ends.
            var app_field_name = app_field_raw.split(' ').join('_').toLowerCase(); //Replace white space with _.          
            var app_field_type = $('#setting_jobapp_field_type').val();
            var selected_field_name = $("#setting_jobapp_field_type option:selected").text();
            var fieldOptions = $('#settings_jobapp_field_options').val();

            if (app_field_name != '') {

                // Show Options for [Checkbox],[Radio] and [Dropdown]  
                var application_field_option;
                if ('text' === app_field_type || 'date' === app_field_type || 'text_area' === app_field_type) {
                    application_field_option = '<input type="hidden" name="field_option[]" value="empty_options" >';
                }
                else {
                    application_field_option = '<input type="text" name="field_option[]" value="' + fieldOptions + '" >';
                }

                $('#settings_app_form_fields').append('<li class="jobapp_' + app_field_name + '">\n\
                    <label for="' + app_field_name + '">' + app_field_raw + '</label>\n\
                    <input type="hidden" name="field_name[]" value="jobapp_' + app_field_name + '" >\n\
                    <input type="hidden" name="field_type[]" value="' + app_field_type + '" >' + application_field_option + '\n\
                    <select class="setting_jobapp_field_type" name="jobapp_' + app_field_name + '[type]"  >\n\
                    <option value="' + app_field_type + '" >' + selected_field_name + '</option>\n\
                    </select>\n\
                    &nbsp; <div class="button removeField">Delete</div></li>');
                $('.' + app_field_name + ' .' + app_field_type).attr('selected', 'selected');
                $('#setting_jobapp_name').val('');
                $('#setting_jobapp_field_type').val('text');

            }
            else {

                alert("Please fill out application form field name");
                $('#setting_jobapp_name').focus(); //Keep focus on this input
            }
        });

        /*Job Application Field Type change*/
        $('#setting_jobapp_field_type').on("change", function () {
            var option_value = $('#settings_jobapp_field_options');
            var fieldType = $(this).val();

            if (!('text' == fieldType || 'date' == fieldType || 'text_area' == fieldType)) {
                option_value.show();
            }
            else {
                option_value.hide();
                option_value.val('');
            }
        });

        // job Application Form Submission 
        $('#jobapp_btn').on('click', function () {

            jobapp_form.submit();
        });

        $('.nav-tab-wrapper a').on("click", function () {
            $('.settings_panel').hide();
            $('.nav-tab-active').removeClass('nav-tab-active');
            $($(this).attr('href')).show();
            $(this).addClass('nav-tab-active');
            return false;
        });
        $('.nav-tab-wrapper a:first').click();
    });
})(jQuery);