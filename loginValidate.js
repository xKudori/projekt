document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const usernameInput = document.querySelector("input[name='Uname']");
    const passwordInput = document.querySelector("input[name='password']");

    form.addEventListener("submit", function(event) {
        let valid = true;
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        if (username === "") {
            alert("Username is required.");
            valid = false;
        }

        if (password === "") {
            alert("Password is required.");
            valid = false;
        } else if (!validatePassword(password)) {
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
