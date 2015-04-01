var PageContactForm = function () {

    return {
        
        //Contact Form
        initPageContactForm: function () {
	        // Validation
	        $("#sky-form3").validate({
	            // Rules for form validation
	            rules:
	            {
	                nome:
	                {
	                    required: true
	                },
	                email:
	                {
	                    required: true,
	                    email: true
	                },
	                mensagem:
	                {
	                    required: true,
	                    minlength: 10
	                },
	                captcha:
	                {
	                    required: true,
	                    remote: 'assets/plugins/sky-forms/version-2.0.1/captcha/process.php'
	                }
	            },
	                                
	            // Messages for form validation
	            messages:
	            {
	                nome:
	                {
	                    required: 'Por favor, digite seu nome',
	                },
	                email:
	                {
	                    required: 'Por favor, digite seu email',
	                    email: 'Digite um email válido!'
	                },
	                mensagem:
	                {
	                    required: 'Por favor, escreva uma mensagem'
	                },
	                captcha:
	                {
	                    required: 'Digite os caracteres',
	                    remote: 'Preencha o captcha corretamente'
	                }
	            },
	                                
	            // Ajax form submition                  
	            submitHandler: function(form)
	            {
	                $(form).ajaxSubmit(
	                {
	                    beforeSend: function()
	                    {
	                        $('#sky-form3 button[type="submit"]').attr('disabled', true);
	                    },
	                    success: function()
	                    {
	                        $("#sky-form3").addClass('submited');
	                    }
	                });
	            },
	            
	            // Do not change code below
	            errorPlacement: function(error, element)
	            {
	                error.insertAfter(element.parent());
	            }
	        });
        }

    };
    
}();