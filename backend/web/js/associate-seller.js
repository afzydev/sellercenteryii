$(document).on('click', '[data-seller-assignment-model]', function (e) {
    e.preventDefault();
    var sellerId = $(this).data('seller-assignment-model');
    var id = $(this).data('seller-assignment-admin');
    ajaxloader.type = 'POST';
    ajaxloader.load(base_url+"index.php?r=associate-seller/assignment", function(data, cparams){
	  if(data != ''){
          var json = $.parseJSON(data);
                if (json.result == true) {
                  $.pjax.reload({container: '#user-grid-pjax'});
              } else {
                  var msg = '<strong>There are some errors </strong><br/>';
                  for (desc in json.description) {
                      msg += json.description[desc] + '<br/>';
                  }

                  errorAlert(msg);
              }
	  }
   }, 'cparams', {'id': id, 'sellerId': sellerId});
});

function errorAlert(msg) {
    $('.modal-footer').show(0);
    $('#alertMessage-modal').modal('show')
            .find('#alert-title').html('<strong>Error!</strong>');
    $('#alertMessage-modal').modal('show')
            .find('#alertMessage').html(msg);

}