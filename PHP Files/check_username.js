document.getElementById("user_name").addEventListener("input", function () {
    const username = this.value;
    const status = document.getElementById("username-status");

    if (username.length === 0) {
        status.textContent = "";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "check_username.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (this.responseText === "taken") {
            status.textContent = "Username already taken.";
            status.style.color = "red";
        } else {
            status.textContent = "Username available!";
            status.style.color = "green";
        }
    };

    xhr.send("user_name=" + encodeURIComponent(username));
});
