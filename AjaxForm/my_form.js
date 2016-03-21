jQuery(function($) {
		$(document).ready(function() {
			 $("#my_form_t").validate({

       rules:{

            user_name:{
                required: true,
                minlength: 2,

            },

            user_email:{
                required: true,
                email: true,
            },
			 user_text:{
                required: true,
              
               
            },
       },

       messages:{

            user_name:{
                required: "Это поле обязательно для заполнения",
                minlength: "Логин должен быть минимум 2 символа",
        
            },

            user_email:{
                required: "Это поле обязательно для заполнения",
                email:"Email должен быть корректным",
            },
			user_text1:{
                required: "Это поле обязательно для заполнения",
            
              
            } ,
			},
			 invalidHandler: function( event, validator )
        {
           $("#my_error").slideDown(400);
        },
			submitHandler: function( form )
        {
            sendData( $( form ).serialize() );
			 $("#my_error").slideUp(400);
	//	yaCounter33289718.reachGoal( 'formsendx' );
            
        },
        errorPlacement: function( error,element )
        {
            //return true;
        }

      

    });
	 function sendData( form_data )
    { 
	form_data+="&action=add_testm";
	console.log(form_data);
      
		jQuery.ajax({
         type : "post",
      
        url : myAjax.ajaxurl,
        data :  form_data,
        success: function(response) {
			$("#my_form_t").slideUp(400);
         $("#my_send_ok").slideDown(400);
            }
            }) 
		window.IsFormPosted = false;
     
    }

			
			
			   });
    });