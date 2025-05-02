<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In Page</title>
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

        .container {
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

        .sign-in-button {
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

        .forgot-password-link {
            display: block;
            margin-top: 10px;
            text-align: right;
            font-size: 14px;
            color: #000;
            text-decoration: underline;
            cursor: pointer;
        }

        .sign-up-container {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }

        .sign-up-link {
            color: #000;
            text-decoration: underline;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <button class="back-button" onclick="window.location.href='Landing.php'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
    </button>

    <div class="container">
        <div class="title">SIGN IN</div>
        <div class="subtitle">Enter your email below to login to your account</div>

        <form id="signInForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" required>
            </div>

            <button type="submit" class="sign-in-button" onclick="window.location.href='Total_Expense.php'"
                style="cursor: pointer;">Sign in</button>
        </form>

        <!-- Forgot Password Link -->
        <a href="forgot-password.php" class="forgot-password-link">Forgot Password?</a>

        <div class="sign-up-container">
            Don't have an account yet? <a href="SignUp.php" class="sign-up-link">Sign up</a>
        </div>
    </div>

    <script>
        function goBack() {
            // In a real application, this would navigate back
            alert("Back button clicked");
        }

        function redirectToSignUp() {
            // In a real application, this would redirect to sign up page
            alert("Redirecting to sign up page");
        }

        document.getElementById("signInForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            // In a real application, this would send the data to a server
            console.log("Sign in attempt with:", { email, password });
            alert(`Sign in attempt with email: ${email}`);
        });
    </script>
</body>

</html>