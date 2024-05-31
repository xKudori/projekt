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
        } 

        if (!valid) {
            event.preventDefault();
        }
    });

});
