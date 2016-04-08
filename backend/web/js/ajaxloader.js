var ajaxloader = {
    type:'GET',
	dataType:"html",
	async: true,
	jsonres:'',
	inprocess:[],
	loadURL: function (url, vars, callback){
		if(ajaxloader.inprocess[url]) return false;
		ajaxloader.inprocess[url] = true;
		loader.init();
		$.post(url,
          vars,
		  function (response){
			   loader.close();
			   try {
				   ajaxloader.jsonres = $.parseJSON(response);    
				   if(ajaxloader.jsonres['system_error'] == 1)
					 window.location.href= base_url+'/error.php';
			      } catch (e) {
			    	  ajaxloader.jsonres = eval(response);
			      }
			   callback(ajaxloader.jsonres);
			   ajaxloader.inprocess[url] = false;
		 });
	},
   load: function (url, callback, callbackparams, vars){ 
       if(typeof vars == 'undefined') vars=null;
            if(ajaxloader.inprocess[url]) return false;
		    ajaxloader.inprocess[url] = true; 
			loader.init();
			$.ajax({
				  url: url,
				  data : vars,
				  type: ajaxloader.type,
				  async: ajaxloader.async,
				  success: function (response){
					   loader.close();
					   ajaxloader.inprocess[url] = false;
					   callback = eval(callback);
					   callback(response, callbackparams);
				  },
				  dataType: ajaxloader.dataType,
				  error: ajaxloader.errorHandler
		   });
   },
   errorHandler: function (jqXHR, textStatus,  errorThrown){
		// alert(errorThrown);
   }
}