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

          img.src = data.newPath + "?t=" + timestamp;

          if (window.opener && !window.opener.closed) {
            window.opener.location.reload();
          }

          alert("Avatar mis à jour avec succès !");

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

  const statusText = document.createElement("p");
  statusText.style.marginTop = "10px";
  statusText.style.fontWeight = "bold";
  statusText.innerText = "Loading...";

  const btnContainer = document.createElement("div");
  btnContainer.style.display = "flex";
  btnContainer.style.gap = "10px";
  btnContainer.style.marginTop = "20px";

  const btnActiveNotif = document.createElement("button");
  btnActiveNotif.innerText = "Activer notification";

  const btnDesactiveNotif = document.createElement("button");
  btnDesactiveNotif.innerText = "Désactiver notification";

  window.OneSignalDeferred = window.OneSignalDeferred || [];
  OneSignalDeferred.push(async function(OneSignal) {

    async function updateStatus() {
      try {
        const permission = await OneSignal.Notifications.permission;
        const isPushEnabled = await OneSignal.User.PushSubscription.optedIn;
        
        if (permission && isPushEnabled) {
          statusText.innerText = "Notifications activées";
          statusText.style.color = "green";
        } else if (permission === false) {
          statusText.innerText = "Notifications désactivées ou bloquées";
          statusText.style.color = "red";
        } else {
          statusText.innerText = "Notifications non configurées";
          statusText.style.color = "orange";
        }
      } catch (error) {
        statusText.innerText = "Verification error";
        statusText.style.color = "gray";
        console.error("Status check error:", error);
      }
    }

    btnActiveNotif.addEventListener("click", async () => {
      try {
        const permission = await OneSignal.Notifications.permission;
        
        if (permission === false) {
          await OneSignal.Slidedown.promptPush();
        } else {
          await OneSignal.User.PushSubscription.optIn();
          alert("Notifications activées !");
        }
        
        await updateStatus();
      } catch (error) {
        console.error("Activation error:", error);
        alert("Erreur lors de l'activation des notifications");
      }
    });

    btnDesactiveNotif.addEventListener("click", async () => {
      try {
        await OneSignal.User.PushSubscription.optOut();
        alert("Notifications désactivées !");
        await updateStatus();
      } catch (error) {
        console.error("Deactivation error:", error);
        alert("Erreur lors de la désactivation des notifications");
      }
    });

    await updateStatus();
  });

  btnContainer.appendChild(btnActiveNotif);
  btnContainer.appendChild(btnDesactiveNotif);

  display.appendChild(h1);
  display.appendChild(p);
  display.appendChild(statusText);
  display.appendChild(btnContainer);
}