const btnAccount = document.getElementById("btnAccount");
const btnAppearance = document.getElementById("btnAppearance");
const btnNotification = document.getElementById("btnNotification");

let currentPathAvatar = pathAvatar;

const savedTheme = localStorage.getItem("theme");
if (savedTheme) {
  document.documentElement.setAttribute("data-theme", savedTheme);
}

showView(AccountDisplay);

btnAccount.addEventListener("click", () => showView(AccountDisplay));
btnAppearance.addEventListener("click", () => showView(AppearanceDisplay));
btnNotification.addEventListener("click", () => showView(NotificationDisplay));

function showView(viewFunction) {
  const display = document.getElementById("display");
  display.innerHTML = "";
  viewFunction(display);
}

function AccountDisplay(display) {
  pathOverlay = "../img/overlayAvatar.png";

  const h1 = document.createElement("h1");
  h1.innerText = "Mon compte";

  const avatar = document.createElement("div");
  avatar.classList.add("avatar-container");

  const img = document.createElement("img");
  img.classList.add("imgAvatar");
  // Forcer un nouveau chargement à chaque affichage
  const cacheBuster = new Date().getTime();
  img.src = currentPathAvatar + "?t=" + cacheBuster;

  const bg = document.createElement("div");
  bg.classList.add("bgAvatar");

  const overlayAvatar = document.createElement("img");
  overlayAvatar.classList.add("overlayAvatar");
  overlayAvatar.src = pathOverlay;

  const fileInput = document.createElement("input");
  fileInput.type = "file";
  fileInput.accept = "image/*";
  fileInput.style.display = "none";

  img.addEventListener("click", () => {
    fileInput.click();
  });

  fileInput.addEventListener("change", () => {
    const file = fileInput.files[0];
    if (!file) return;

    if (!file.type.startsWith("image/")) {
      alert("Veuillez sélectionner une image valide");
      return;
    }

    const formData = new FormData();
    formData.append("avatar", file);

    fetch("avatar.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.newPath) {
          const timestamp = data.timestamp || Date.now();

          // Mettre à jour l'image actuelle dans les settings
          img.src = data.newPath + "?t=" + timestamp;

          // Forcer le rechargement de l'image sur la page d'accueil
          if (window.opener && !window.opener.closed) {
            window.opener.location.reload();
          }
          
          alert("Avatar mis à jour avec succès !");
          
          // Rediriger après 1 seconde
          setTimeout(() => {
            window.location.href = "../../";
          }, 1000);
        } else {
          alert(data.error || "Erreur lors de la mise à jour");
        }
      })
      .catch((err) => {
        console.error(err);
        alert("Erreur lors de l'envoi du fichier");
      });
  });

  avatar.appendChild(img);
  avatar.appendChild(bg);
  avatar.appendChild(overlayAvatar);
  avatar.appendChild(fileInput);

  const usernameInput = document.createElement("input");
  usernameInput.value = username;
  usernameInput.readOnly = true;

  display.appendChild(h1);
  display.appendChild(avatar);
  display.appendChild(usernameInput);
}

function AppearanceDisplay(display) {
  const h1 = document.createElement("h1");
  h1.innerText = "Apparence";

  const themeContainer = document.createElement("div");

  const themeWhiteBtn = document.createElement("button");
  themeWhiteBtn.innerText = "Thème clair";

  const themeBlackBtn = document.createElement("button");
  themeBlackBtn.innerText = "Thème sombre";
  //few other themes soon

  themeWhiteBtn.addEventListener("click", function () {
    document.documentElement.setAttribute("data-theme", "light");
    localStorage.setItem("theme", "light");
  });

  themeBlackBtn.addEventListener("click", function () {
    document.documentElement.setAttribute("data-theme", "dark");
    localStorage.setItem("theme", "dark");
  });

  themeContainer.appendChild(themeWhiteBtn);
  themeContainer.appendChild(themeBlackBtn);

  display.appendChild(h1);
  display.appendChild(themeContainer);
}

function NotificationDisplay(display) {
  const h1 = document.createElement("h1");
  h1.innerText = "Notification";

  const p = document.createElement("p");
  p.innerText = "Bientôt disponible avec d'autres paramètres";
  //settings notif

  display.appendChild(h1);
  display.appendChild(p);
}