<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f9f9f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .back-button svg {
            width: 24px;
            height: 24px;
        }

        .form-container {
            background-color: white;
            width: 400px;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        .subtitle {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 2px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #000;
            color: white;
            border: none;
            border-radius: 2px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-link {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }

        .login-link a {
            color: #000;
            text-decoration: underline;
            font-weight: bold;
            cursor: pointer;
        }

        .password-match-message,
        .password-validation-message {
            font-size: 12px;
            margin-top: 2px;
            margin-bottom: 5px;
            display: none;
        }

        .match {
            color: green;
        }

        .no-match {
            color: red;
        }
    </style>
</head>

<body>
    <button class="back-button" onclick="goBack()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
    </button>

    <div class="form-container">
        <!-- Header -->
        <div class="title">CREATE AN ACCOUNT</div>
        <div class="subtitle">Sign Up & Start Tracking Your Expenses</div>

        <!-- Form -->
        <form id="signup-form">
            <!-- Full Name Input -->
            <div class="form-group">
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" name="fullName" required />
            </div>

            <!-- Email Input -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />
            </div>

            <!-- Password Input -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />
                <div id="password-validation-message" class="password-validation-message no-match">
                    Password must include at least one capital letter and one symbol.
                </div>
            </div>

            <!-- Confirm Password Input -->
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirmPassword" required />
                <div id="password-match-message" class="password-match-message">Passwords don't match</div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submit-btn" disabled>Sign Up</button>
        </form>

        <!-- Already have an account? Link -->
        <div class="login-link">
            Already have an account? <a href="Login_Page.html">Sign in</a>
        </div>
    </div>

    <script>
        function goBack() {
            window.location.href = 'index.html';
        }

        // Extract email from query parameter and pre-fill the email field
        const urlParams = new URLSearchParams(window.location.search);
        const emailFromQuery = urlParams.get('email');

        if (emailFromQuery) {
            document.getElementById('email').value = emailFromQuery;
        }

        // JavaScript to handle form submission and password validation
        const signupForm = document.getElementById("signup-form");
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById("confirm-password");
        const passwordMatchMessage = document.getElementById("password-match-message");
        const passwordValidationMessage = document.getElementById("password-validation-message");
        const submitBtn = document.getElementById("submit-btn");

        // Function to validate password
        function validatePassword() {
            const passwordValue = password.value;
            const hasCapitalLetter = /[A-Z]/.test(passwordValue); // Check for at least one capital letter
            const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/.test(passwordValue); // Check for at least one symbol

            if (hasCapitalLetter && hasSymbol) {
                passwordValidationMessage.style.display = "none";
                return true;
            } else {
                passwordValidationMessage.style.display = "block";
                return false;
            }
        }

        // Function to check if passwords match
        function checkPasswordMatch() {
            if (confirmPassword.value === "") {
                passwordMatchMessage.style.display = "none";
                return;
            }

            if (password.value === confirmPassword.value) {
                passwordMatchMessage.textContent = "Passwords match";
                passwordMatchMessage.className = "password-match-message match";
                passwordMatchMessage.style.display = "block";
            } else {
                passwordMatchMessage.textContent = "Passwords don't match";
                passwordMatchMessage.className = "password-match-message no-match";
                passwordMatchMessage.style.display = "block";
            }
        }

        // Add event listeners for password fields
        password.addEventListener("input", function () {
            validatePassword();
            checkPasswordMatch();
            updateSubmitButtonState();
        });

        confirmPassword.addEventListener("input", function () {
            checkPasswordMatch();
            updateSubmitButtonState();
        });

        // Enable or disable the submit button based on form validity
        function updateSubmitButtonState() {
            const isPasswordValid = validatePassword();
            const passwordsMatch = password.value === confirmPassword.value;

            if (isPasswordValid && passwordsMatch) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        signupForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent default form submission

            // Check if passwords match before submitting
            if (password.value !== confirmPassword.value) {
                passwordMatchMessage.textContent = "Passwords don't match";
                passwordMatchMessage.className = "password-match-message no-match";
                passwordMatchMessage.style.display = "block";
                return;
            }

            // Collect form data
            const formData = {
                fullName: document.getElementById("full-name").value,
                email: document.getElementById("email").value,
                password: document.getElementById("password").value,
            };

            // Log form data to the console
            console.log("Form Data:", formData);

            // Add your logic here to handle form submission (e.g., send data to a backend API)
            alert("Sign Up Successful! Check the console for form data.");
        });
    </script>
</body>

</html>