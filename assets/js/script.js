let tasks = [];

async function init() {
  tasks = await recupTasks();
  if (tasks) {
    showTask(tasks);
  }

  const btnAdd = document.getElementById("addTask");
  btnAdd.addEventListener("click", function () {
    addTask();
  });
}

init();

async function recupTasks() {
  try {
    const response = await fetch("assets/php/API.php", {
      method: "GET",
      credentials: "include",
    });

    if (!response.ok) {
      throw new Error("Erreur HTTP: " + response.status);
    }

    const data = await response.json();

    if (data.error) {
      console.error("Erreur côté serveur:", data.error);
      return [];
    } else {
      console.log("Tâches récupérées:", data.tasks);
      return data.tasks;
    }
  } catch (error) {
    console.error("Erreur lors de la récupération:", error);
    return [];
  }
}

function showTask(tasks) {
  const main = document.getElementById("main");
  main.innerHTML = "";

  tasks.forEach((task) => {
    const article = document.createElement("article");
    article.innerText = `${task.task} - ${task.description} - à faire jusqu'au ${task.deadLine} - ${task.importance}`;

    const status = document.createElement("input");
    status.type = "checkbox";
    status.checked = task.isDone;

    const editBtn = document.createElement("button");
    editBtn.innerText = "⁝";
    editBtn.addEventListener("click", function () {
      showEditTask(task, article);
    });

    article.appendChild(status);
    article.appendChild(editBtn);
    main.appendChild(article);
  });

}

async function addTask() {
  const main = document.getElementById("main");
  const form = document.createElement("section");
  form.classList.add("formAdd");

  const addDisplay = document.createElement("article");
  addDisplay.classList.add("addDisplay");

  addDisplay.innerHTML = `
    <form class="add">
      <label for="nameAdd">Nom</label>
      <input type="text" id="nameAdd" required>

      <select name="importance" id="importance">
        <option value="important">important</option>
        <option value="normal">normal</option>
        <option value="peu_important">peu important</option>
      </select>

      <label for="descriptionAdd">Description</label>
      <textarea id="descriptionAdd" required></textarea>

      <label for="deadLine">À faire jusqu'à</label>
      <input type="date" id="deadLine" required>
    </form>
  `;

  let addBtn = document.createElement("article");
  addBtn.classList.add("addBtn");

  let addButton = document.createElement("button");
  addButton.innerText = "Ajouter la tâche";

  addButton.addEventListener("click", async function (event) {
    event.preventDefault();

    const name = document.getElementById('nameAdd').value;
    const importance = document.getElementById('importance').value;
    const description = document.getElementById('descriptionAdd').value;
    const deadLine = document.getElementById('deadLine').value;

    const taskData = {
      task: name,
      importance,
      description,
      deadLine
    };

    const result = await sendTaskData(taskData);

    if (result.success) {
      tasks = await recupTasks();
      showTask(tasks);
      form.remove();
    } else {
      showNotification("Erreur lors de l'ajout de la tâche.");
    }
  });

  let exitButton = document.createElement("button");
  exitButton.innerText = "Exit";
  exitButton.addEventListener("click", function () {
    form.remove();
  });

  addBtn.appendChild(addButton);
  addBtn.appendChild(exitButton);
  addDisplay.appendChild(addBtn);
  form.appendChild(addDisplay);
  main.appendChild(form);
}

async function sendTaskData(taskData) {
  try {
    const response = await fetch("assets/php/API.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(taskData),
      credentials: "include",
    });

    if (!response.ok) {
      throw new Error("Erreur HTTP: " + response.status);
    }

    const data = await response.json();

    if (data.error) {
      console.error("Erreur côté serveur:", data.error);
      return { success: false };
    } else {
      console.log("Tâche ajoutée avec succès:", data);
      return { success: true };
    }
  } catch (error) {
    console.error("Erreur lors de l'envoi des données:", error);
    return { success: false };
  }
}

function showEditTask(task, article) {
  article.innerHTML = "";

  const taskInput = document.createElement("input");
  taskInput.value = task.task;

  const descInput = document.createElement("textarea");
  descInput.value = task.description;

  const deadLineInput = document.createElement("input");
  deadLineInput.type = "date";
  deadLineInput.value = task.deadLine;

  const importanceSelect = document.createElement("select");

  article.appendChild(taskInput);
  article.appendChild(descInput);
  article.appendChild(deadLineInput);
  article.appendChild(importanceSelect);
}








function showNotification(message) {
  const existing = document.getElementById("notification");
  if (existing) existing.remove();

  const notification = document.createElement("div");
  notification.id = "notification";
  notification.className = "notification";
  notification.innerHTML = `<span>${message}</span>`;

  const progress = document.createElement("div");
  progress.className = "notification-progress";
  notification.appendChild(progress);

  document.body.appendChild(notification);

  setTimeout(() => notification.classList.add("show"), 100);
  setTimeout(() => hideNotification(), 3000);

  notification.addEventListener("click", hideNotification);
}

function hideNotification() {
  const notification = document.getElementById("notification");
  if (notification) {
    notification.classList.add("hide");
    setTimeout(() => notification.remove(), 400);
  }
}
