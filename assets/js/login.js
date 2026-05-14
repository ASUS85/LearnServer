
let loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", async function(e){
    e.preventDefault();

    const formData = new FormData(this);

    const response = await fetch("api/login.php", {
        method: "POST",
        body: formData
    });

    const data = await response.json();

    const message = document.getElementById("message");

    if(data.status === "success"){
        message.style.color = "green";
        message.textContent = data.message;

        setTimeout(() => {
            window.location.href = data.redirect;
        }, 1000);

    }else{
        message.style.color = "red";
        message.textContent = data.message;
    }
});