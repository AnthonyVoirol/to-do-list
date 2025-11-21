function OpenSettingAccount() {
  document.getElementById("avatar").addEventListener("click", () => {
    const menu = document.getElementById("profileMenu");
    menu.classList.toggle("show");
  });

  document.addEventListener("click", (e) => {
    const avatar = document.getElementById("avatar");
    const menu = document.getElementById("profileMenu");

    if (!avatar.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.remove("show");
    }
  });
}