document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordField = document.getElementById("floatingPassword");
    const icon = this;
  
    // Toggle the password field type
    if (passwordField.type === "password") {
      passwordField.type = "text";
      icon.classList.remove("uil-eye-slash");
      icon.classList.add("uil-eye"); // Change to "eye" icon
    } else {
      passwordField.type = "password";
      icon.classList.remove("uil-eye");
      icon.classList.add("uil-eye-slash"); // Change back to "eye-slash" icon
    }
  });
  
