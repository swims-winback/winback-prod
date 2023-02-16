/*
$(document).ready(function () {
    $("registrationForm").submit(function (event) {
      var formData = {
        name: $("#username").val(),
        email: $("#email").val(),
      };
  
      $.ajax({
        type: "POST",
        url: `/register`,
        data: formData,
        dataType: "json",
        encode: true,
      }).done(function (data) {
        console.log(data);
      });
  
      event.preventDefault();
    });
  });
*/

function togglePassword(element, password) {
  if (element.classList.contains('fa-eye-slash')) {
      element.classList.remove('fa-eye-slash')
      element.classList.add('fa-eye')
      password.type ='text'
  } else {
      element.classList.remove('fa-eye')
      element.classList.add('fa-eye-slash')
      password.type ='password'
  }
}

let eyeIcon = document.getElementsByClassName("eyeIcon");

for (let i = 0; i < eyeIcon.length; i++) {
  eyeIcon.item(i).onclick = function () {
    let parent = eyeIcon.item(i).parentElement;
    let previous = parent.previousElementSibling;
    let passwordField = previous.firstChild;
    togglePassword(eyeIcon.item(i), passwordField)
  }
}

function checkPasswordStrength() {
	var number = /([0-9])/;
	var alphabets = /([a-zA-Z])/;
  var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
  
  //password_element = document.getElementById("password");
  //console.log(password_element);
  //let password = password_element.val().trim();
	var password = $('#registration_form_password_first').val().trim();
	if (password.length < 6) {
		$('#password-strength-status').removeClass();
    $('#password-strength-status').addClass('weak-password');
    $('#registration_form_password_first').addClass('weak-password');
    document.getElementById("progress-bar").style.width = "25%";
    $('#progress-bar').removeClass();
    $('#progress-bar').addClass('bg-red');
		$('#password-strength-status').html("at least 6 characters.");
	} else {
		if (password.match(number) && password.match(alphabets) && password.match(special_characters)) {
			$('#password-strength-status').removeClass();
      $('#password-strength-status').addClass('strong-password');
      $('#registration_form_password_first').addClass('strong-password');
      document.getElementById("progress-bar").style.width = "100%";
      $('#progress-bar').removeClass();
      $('#progress-bar').addClass('bg-green');
			$('#password-strength-status').html("Strong");
		}
		else {
			$('#password-strength-status').removeClass();
      $('#password-strength-status').addClass('medium-password');
      $('#registration_form_password_first').addClass('medium-password');
      document.getElementById("progress-bar").style.width = "50%";
      $('#progress-bar').removeClass();
      $('#progress-bar').addClass('bg-orange');
			$('#password-strength-status').html("should include alphabets, numbers and special characters.");
		}
	}
}