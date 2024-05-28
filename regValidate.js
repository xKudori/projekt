document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");

    form.addEventListener("submit", function(event) {
        let valid = true;
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        if (username.length < 6) {
            alert("Username must be at least 6 characters long.");
            valid = false;
        }

        if (!validatePassword(password)) {
            alert("Password must be at least 8 characters long, contain at least one uppercase letter, and one special character.");
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
        }
    });

    function validatePassword(password) {
        const passwordRegex = /^(?=.*[A-Z])(?=.*\W).{8,}$/;
        return passwordRegex.test(password);
    }
});
