<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenzo</title>
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Lora:wght@400;500&family=Meie+Script&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAF9F6;
            color: #1C1C1C;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            border-bottom: 1.5px solid #9C9696;
            background-color: #f8f7f3;
            width: 100%;
            position: relative;
            z-index: 5;
        }

        .logo {
            font-family: 'Meie Script', cursive;
            font-size: 36px;
            font-weight: 400;
        }

        .header-buttons {
            display: flex;
            align-items: center;
        }

        .sign-in {
            font-size: 14px;
            margin-right: 20px;
            cursor: pointer;
            text-transform: uppercase;
            text-decoration: none;
            color: #1C1C1C;
        }

        .sign-in:hover {
            text-decoration: underline;
        }

        .get-started-btn {
            background: #1C1C1C;
            color: #F5F5F5;
            padding: 8px 15px;
            border-radius: 1px;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
            text-decoration: none;
            color: #F5F5F5;
            display: inline-block;
        }

        .main-layout {
            display: grid;
            grid-template-columns: 1fr 3fr 1fr;
            flex: 1;
            position: relative;
            background-color: #f8f7f3;
            width: 100%;
        }

        .left-column,
        .right-column {
            padding: 30px;
            position: relative;
            z-index: 5;
            display: flex;
            flex-direction: column;
        }

        .left-column {
            border-right: 1.5px solid #9C9696;
        }

        .right-column {
            border-left: 1.5px solid #9C9696;
        }

        .central-content {
            padding: 20px 40px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .hero-text {
            font-family: 'Lora', serif;
            font-size: 80px;
            text-align: center;
            line-height: 1.1;
            margin-top: 10px;
            position: relative;
            z-index: 5;
        }

        .email-signup {
            margin: 15px 0 10px;
            text-align: center;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 5;
        }

        .email-input {
            width: 100%;
            display: flex;
            margin-bottom: 8px;
        }

        .email-input input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .email-input button {
            background: #1C1C1C;
            color: #F5F5F5;
            border: none;
            padding: 0 15px;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 12px;
        }

        .features {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: 20px;
            position: relative;
            z-index: 5;
        }

        .feature {
            display: flex;
            align-items: center;
            margin: 0 15px;
            font-size: 14px;
        }

        .feature-icon {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #1C1C1C;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .left-info {
            margin-bottom: 30px;
        }

        .left-info p {
            font-size: 15px;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .read-more,
        .platform-link {
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        /* Bottom platform section */
        .bottom-platform-section {
            background-color: #f8f7f3;
            padding: 20px 0;
            border-top: 1.5px solid #9C9696;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 8px;
            position: relative;
            z-index: 10;
            width: 100%;
        }

        .bottom-info {
            text-transform: uppercase;
            font-weight: 700;
            font-size: 15px;
            text-align: center;
        }

        .bottom-info p {
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .circular-badge {
            border: 1px solid #070707;
            border-radius: 50%;
            width: 270px;
            height: 270px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 30px auto;
            margin-top: -20px;
            /* pulls it upwards */
        }

        .badge-logo {
            font-family: 'Meie Script', cursive;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .badge-text {
            font-size: 11px;
            text-align: center;
            font-weight: 700;
            max-width: 80%;
            line-height: 1.3;
        }

        .badge-text span:first-child {
            font-weight: 400;
        }

        .savings-info {
            text-align: center;
            margin-top: auto;
            padding-bottom: 20px;
            position: relative;
            z-index: 5;
        }

        .savings-title {
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .savings-percentage {
            font-size: 44px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .savings-period {
            font-size: 16px;
            font-weight: 500;
        }

        /* Updated hands positioning with higher z-index */
        .hands-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 20;
        }

        .card-hand {
            position: absolute;
            bottom: 180px;
            left: -30px;
            width: 50%;
            max-width: 500px;
            z-index: 20;
            /* Increased z-index to appear on top */
            transform: translateY(20px);
        }

        .left-hand {
            position: absolute;
            bottom: 130px;
            right: -30px;
            width: 50%;
            max-width: 500px;
            z-index: 20;
            /* Increased z-index to appear on top */
            transform: translateY(20px);
        }

        .tagline {
            text-align: center;
            font-size: 15px;
            margin: 12px 0;
            position: relative;
            z-index: 5;
        }

        .responsibility-text {
            text-align: center;
            font-size: 12px;
            margin: 8px 0;
            color: #555;
            position: relative;
            z-index: 5;
        }

        footer {
            background-color: #1C1C1C;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        /* Add diagonal line with lower z-index */
        .diagonal-line {
            position: absolute;
            height: 1px;
            width: 140%;
            background-color: #ddd;
            transform: rotate(-45deg);
            transform-origin: top left;
            left: 0;
            top: 170px;
            z-index: 0;
            /* Lower z-index to appear behind */
        }

        /* Media query to handle very large screens */
        @media screen and (min-width: 1600px) {
            .container {
                max-width: 100%;
            }

            .main-layout {
                grid-template-columns: 1fr 3fr 1fr;
            }

            .hero-text {
                font-size: 80px;
            }
        }

        /* Media query to handle smaller screens */
        @media screen and (max-width: 1200px) {
            .main-layout {
                grid-template-columns: 1fr 3fr 1fr;
            }

            .hero-text {
                font-size: 70px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">Expenzo</div>
            <div class="header-buttons">
                <a href="Login.php" class="sign-in">SIGN IN</a>
                <a href="SignUp.php" class="get-started-btn">GET STARTED</a>
            </div>
        </header>
        <div class="main-layout">
            <!-- Diagonal line -->
            <div class="diagonal-line"></div>
            <!-- Left Column -->
            <div class="left-column">
                <div class="left-info">
                    <p>Everything you need to control spend and optimize finance operations, all on a single platform
                    </p>
                    <div class="read-more">Read more →</div>
                </div>
            </div>
            <!-- Central Content -->
            <div class="central-content">
                <div class="hero-text">
                    <div>MAKE EVERY</div>
                    <div>RUPEE</div>
                    <div>COUNT</div>
                </div>
                <div class="tagline">
                    Spend money wisely track every rupee and become responsibe
                </div>
                <div class="email-signup">
                    <div class="email-input">
                        <input type="email" id="emailInput" placeholder="What's your email?">
                        <button onclick="window.location.href='SignUp.php'" style="cursor: pointer;">
                            GET STARTED
                        </button>
                    </div>
                    <div class="responsibility-text">
                        Man up and take responsibility of you money
                    </div>
                </div>
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">✓</div>
                        <div>Expense Management</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">✓</div>
                        <div>Budget Tracking</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">✓</div>
                        <div>Visual Report</div>
                    </div>
                </div>
            </div>
            <!-- Right Column -->
            <div class="right-column">
                <div class="circular-badge">
                    <div class="badge-logo">Expenzo</div>
                    <div class="badge-text">
                        <span>#1</span> BEST EXPENSE TRACKER WEBSITE IN HERALD
                    </div>
                </div>
                <div class="savings-info">
                    <div class="savings-title">DESIGNED TO<br>HELP YOU SAVE</div>
                    <div class="savings-percentage">15-20%</div>
                    <div class="savings-period">every month</div>
                </div>
            </div>
            <!-- Hand images positioned on sides with higher z-index -->
            <div class="hands-container">
                <img src="leftcardhand.png" alt="Hand holding a credit card" class="card-hand">
                <img src="lefthand.png" alt="Hand gesture" class="left-hand">
            </div>
        </div>
        <!-- Bottom Platform Section -->
        <div class="bottom-platform-section">
            <div class="bottom-info">
                <p>CONTROL YOUR SPEND ON A SINGLE PLATFORM</p>
            </div>
            <!-- <div class="platform-link">Platform →</div> -->
        </div>
        <!-- <footer>
            Copyright© 2025 Expense Tracker App
        </footer> -->
    </div>

    <script>
        // Function to validate email and redirect to signup form
        function redirectToSignup() {
            const emailInput = document.getElementById('emailInput').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailPattern.test(emailInput)) {
                // Redirect to signup form with email as query parameter
                window.location.href = `SignUp.php?email=${encodeURIComponent(emailInput)}`;
            } else {
                alert('Please enter a valid email address.');
            }
        }

        // Disable button initially
        document.getElementById('getStartedButton').disabled = true;

        // Enable button only when email is valid
        document.getElementById('emailInput').addEventListener('input', function () {
            const emailInput = this.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            document.getElementById('getStartedButton').disabled = !emailPattern.test(emailInput);
        });
    </script>
</body>

</html>