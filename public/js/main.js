

/* Launch modal when page is ready */
$(document).ready(function(){
  $("#exampleModal").modal('show');
});
$("#cookie_consent_save").click(function () {
  $("#exampleModal").modal('hide');
});