        jQuery(document).ready(function($){
            $( ".jobpost_form" ).submit(function() {
		
		//var formData = new FormData($(this)[0]);
		var datastring = new FormData(document.getElementById("cs-assignments-form"));
		 $.ajax({
			url: MyAjax.ajaxurl,
			type: 'POST',
			dataType: 'json',
			//data: jQuery('#cs-assignments-form').serialize()+'&attachment='fd,
			data: datastring,
			async: false,
			cache: false,
			contentType: false,
			processData: false,
                        beforeSend: function(){
                            $('#jobpost_form_status').html('Submitting.....');
                            $("#jobpost_submit_button").attr('disabled','diabled');                            
                        },
			success:function(response){
                            if(response['success']==true){
                                $('.jobpost_form').slideUp();
                                $('#jobpost_form_status').html('Your application has been received. We will get back to you soon.');
                            }
                            if(response['success']==false){
                                $('#jobpost_form_status').html(response['error']+' Your application could not be processed.');
                                $("#jobpost_submit_button").removeAttr('disabled');
                            }
				
			}
		});
		return false;
                
                

                // Stop form from submitting normally
               // event.preventDefault();
                
                // Get some values from elements on the page:
                //var data = $(this).serialize();
               // var data = new FormData(document.getElementById("cs-assignments-form"));
               // var data = jQuery('#cs-assignments-form').serialize();
                // Send the data using post
//                var posting=$.post(MyAjax.ajaxurl, data, function(response) {
//                    $('#jobpost_form_status').html('Submitting.....');
//                    $("#jobpost_submit_button").attr('disabled','diabled');
//			if(response['success']==true){
//                            $('.jobpost_form').slideUp();
//                            $('#jobpost_form_status').html('Your application has been received. We will get back to you soon.');
//                        }
//                        if(response['success']==false){
//                            $('#jobpost_form_status').html('Your application could not be processed. Make sure you have filled all the form fields');
//                        }
//		});
              });
        })