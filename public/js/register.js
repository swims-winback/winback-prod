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
