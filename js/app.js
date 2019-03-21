jQuery(function($){

  $('#S-Time').datetimepicker();
  
  $('#S-Time-Show').click(function(){
    console.log('x')
    $('#S-Time').focus();
  });

});