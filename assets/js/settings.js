const btnAccount = document.getElementById("btnAccount");

btnAccount.addEventListener("click", function () {
    AccountDisplay();
})

function AccountDisplay() {
    const display = document.getElementById("display");

    const h1 = document.createElement("h1");
    h1.innerText = "Mon compte";

    const avatar = document.createElement("div");
    const img = document.createElement("img");
    img.src = pathAvatar;

    avatar.appendChild(img);

    display.appendChild(h1);
    display.appendChild(avatar);
};

function appearanceDisplay() {

};

function notificationDisplay() {

};