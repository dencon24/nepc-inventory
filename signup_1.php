<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-up</title>
    <link rel="stylesheet" href="./css/signup_1.css">
    <script>
        function validateForm() {
            let pass = document.getElementById("pass").value;
            let cpass = document.getElementById("con-password").value;
            
            if(pass !== cpass) {
                document.getElementById("notiff").style.display = "block";
                return false; // Prevent form submission
            }
            document.getElementById("notiff").style.display = "none";
            return true; // Allow form submission
        }

        function checkPasswords() {
            let pass = document.getElementById("pass").value;
            let cpass = document.getElementById("con-password").value;
            
            if(pass !== cpass) {
                document.getElementById("notiff").style.display = "block";
            } else {
                document.getElementById("notiff").style.display = "none";
            }
        }
    </script>
</head>
<header>
    <h1 onclick=window.location.href="dashboard.php">REGISTRATION</h1>
</header>
<body>
    <section>
        <div class="signup-container">
            <form action="register.php" method="POST" onsubmit="return validateForm()">
                <div class="fill">
                    <img src="img/Negros-Power-Logo-Black.png">
                </div>
                <div class="signup-box">
                    <span class="signup-icon">
                        <img src="img/human.png" id="human">
                    </span>
                    <input type="text" name="un" required>
                    <label>
                        Username
                    </label>
                </div>
                <div class="signup-box">
                    <input type="password" placeholder="" name="pw" id="pass" onkeyup="checkPasswords()" required>
                    <span class="signup-icon">
                        <img src="img/eyeclose.png" id="eyeicon">
                    </span> 
                    <label>
                        Password
                    </label>
                </div>
                <div class="signup-box">
                    <input type="password" placeholder="" id="con-password" name="cpass" onkeyup="checkPasswords()" required>
                    <span class="signup-icon">
                        <img src="img/eyeclose.png" id="eye-icon">
                    </span>
                    <label>
                        Confirm password
                    </label>
                </div>
                <div id="notiff" class="notif" style="color: red;text-align: center;font-weight: lighter;margin-bottom: 10px;display: none;">Password does not match.</div>

                <div class="role-box">
                    <select name="role" id="role-select" style="width: 100%; height: 40px;margin: 1%;border-radius: 40px;cursor: pointer;font-size: 1em;font-weight: 500;text-align: center;">
                        <option value="admin">Admin</option>
                        <option value="user">Guest User</option>
                        <option value="inventory manager">Inventory Manager</option>
                    </select>
                </div>
                <button type="submit">
                    Register
                </button>
                <button type="button" onclick="window.location.href='dashboard.php';">
                    Cancel
                </button>
            </form>
        </div>
    </section>

    <script>
        // Toggle password visibility
        let eyeicon = document.getElementById("eyeicon");
        let password = document.getElementById("pass");
        
        eyeicon.onclick = function(){
            if(password.type == "password"){
                password.type = "text";
                eyeicon.src = "img/eyeopen.png";
            } 
            else {
                password.type = "password";
                eyeicon.src = "img/eyeclose.png";
            }
        }

        let eye_icon = document.getElementById("eye-icon");
        let con_password = document.getElementById("con-password");
        
        eye_icon.onclick = function(){
            if(con_password.type == "password"){
                con_password.type = "text";
                eye_icon.src = "img/eyeopen.png";
            } else {
                con_password.type = "password";
                eye_icon.src = "img/eyeclose.png";
            }   
        }

        // Handle server-side error
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('error') === '1') {
                document.getElementById('notiff').style.display = 'block';
                document.getElementById('notiff').textContent = 'Passwords did not match. Please try again.';
            }
        });
    </script>
</body>
</html>