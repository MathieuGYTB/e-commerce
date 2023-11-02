const eyeOn = document.querySelector('.eye-on');
const eyeOff = document.querySelector('.eye-off');
const inputPassword = document.querySelector('#inputPassword');

eyeOn.addEventListener('click', () => {
  eyeOn.style.display = "none";
  eyeOff.style.display = "block";
  inputPassword.type = "text";
});

eyeOff.addEventListener('click', () => {
  eyeOn.style.display = "block";
  eyeOff.style.display = "none";
  inputPassword.type = "password";
});