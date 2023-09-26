define(['jquery'], function($){
   "use strict";
       return function myscript()
       {
           $.ajax({
		url : 'https://m6.ausandbox.com/index.php/dev_restapi/index/api',
                type : 'POST',
                dataType:'html',
		data: {token:''},
                success : function(data) {              
                 $('.page-title').text(data);
                },
                error : function(request,error)
                {
                    alert("Error");
                }
            });
	}
      
});