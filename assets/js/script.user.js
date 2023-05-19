$('button.btnDelete').on('click', function (e) {
    e.preventDefault();
    var id = $(this).closest('tr').data('id');
    $('#myModal').data('id', id).modal('show');
});
$(function() {
  $('#status').hide();
});
$('#btnDelteYes').click(function () {
    var id = $('#myModal').data('id');
$.post('conf/delete.php',{acao:'delete',id:id},function(response,status){
     if(status == "success"){   
	     var obj = jQuery.parseJSON(response);
        $('[data-id=' + id + ']').remove();
        $('#status').removeClass('alert-danger')
                    .addClass('alert-success')
                    .text(obj.msg).fadeIn('slow');
	$('#myModal').modal('hide');
       } else {
        $('#myModal').modal('hide');
        $('#status').removeClass('alert-success')
                    .addClass('alert-danger')
                    .text(obj.msg).fadeIn('slow');
       }
          hideMessage();
    });
});
function hideMessage() {
    setTimeout(function() {
        $('#status').hide();
    }, 4000);
}