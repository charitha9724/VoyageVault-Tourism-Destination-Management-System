<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function validateContactNumber(input) {
            let contactField = document.getElementById("contact_no");
            let errorMsg = document.getElementById("contactError");

            if (!/^\d{10}$/.test(input.value)) {
                errorMsg.textContent = "Contact number must be exactly 10 digits.";
                contactField.classList.add("is-invalid");
            } else {
                errorMsg.textContent = "";
                contactField.classList.remove("is-invalid");
                contactField.classList.add("is-valid");
            }
        }
        function validateGmail(input) {
        let emailField = document.getElementById("email");
        let emailError = document.getElementById("emailError");
        let pattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

        if (!pattern.test(input.value)) {
            emailError.textContent = "Please enter a valid Gmail address.";
            emailField.classList.add("is-invalid");
            emailField.classList.remove("is-valid");
        } else {
            emailError.textContent = "";
            emailField.classList.remove("is-invalid");
            emailField.classList.add("is-valid");
        }
    }function checkEmailAvailability() {
    let email = document.getElementById("email").value;
    let emailField = document.getElementById("email");
    let emailError = document.getElementById("emailError");

    if (!email.endsWith("@gmail.com")) {
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "check_email.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.responseText.trim() === "taken") {
                emailError.textContent = "This email is already registered.";
                emailField.classList.add("is-invalid");
                emailField.classList.remove("is-valid");
            } else {
                emailField.classList.remove("is-invalid");
                emailField.classList.add("is-valid");
                emailError.textContent = "";
            }
        }
    };

    xhr.send("email=" + encodeURIComponent(email));
}

        
    </script>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-start mb-4">USER REGISTRATION</h2>

            <form action="signup_p.php" method="POST">
                <div class="mb-3">
                <label for="user_name">Username</label>
<input type="text" name="user_name" id="user_name" class="form-control" required>
<span id="username-status" class="text-sm"></span>

                </div>
                <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" required 
        oninput="validateGmail(this); checkEmailAvailability();" 
        onblur="validateGmail(this); checkEmailAvailability();">
    <small id="emailError" class="text-danger"></small>
</div>

                <div class="mb-3">
                    <label class="form-label">Contact Number</label>                    
                    <input type="text" class="form-control" id="contact_no" name="contact_no" pattern="\d{10}" maxlength="10" required oninput="validateContactNumber(this)">
                    <small id="contactError" class="text-danger"></small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="passwd" required>
                </div>

                <p class="mt-3">Already signed in? <a href="log.php">Login</a></p>

                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </form>
        </div>
    </div>
</div>
<script src="check_username.js"></script>

</body>
</html>
