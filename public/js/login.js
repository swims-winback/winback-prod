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

eyeIcon = document.getElementById("eyeIcon");
passwordField = document.getElementById("inputPassword");
eyeIcon.onclick = function () {
    togglePassword(eyeIcon, passwordField)
}