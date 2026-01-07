let tasks = [];
let sortSelect;

const savedTheme = localStorage.getItem("theme");
if (savedTheme) {
  document.documentElement.setAttribute("data-theme", savedTheme);
}

async function init() {
  tasks = await recupTasks();
  if (tasks) {
    const savedSort = localStorage.getItem("taskSort") || "deadLine";
    
    sortSelect = document.getElementById("sortTasks");
    sortSelect.value = savedSort;
    
    sortTasks(savedSort);
    showTask(tasks);
  }

  sortSelect = document.getElementById("sortTasks");
  sortSelect.addEventListener("change", () => {
    const sortBy = sortSelect.value;
    localStorage.setItem("taskSort", sortBy);
    sortTasks(sortBy);
    showTask(tasks);
  });

  const btnAdd = document.getElementById("addTask");
  btnAdd.addEventListener("click", function () {
    addTask();
  });

  OpenSettingAccount();
}

init();

async function recupTasks() {
  try {
    const response = await fetch("../api/tasks.php", {
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

  const divDone = document.getElementById("done");
  divDone.classList.add("taskDone");
  divDone.innerHTML = "";

  tasks.forEach((task) => {
    const article = document.createElement("article");

    const taskContent = document.createElement("div");
    taskContent.classList.add("task-content");

    const taskHeader = document.createElement("div");
    taskHeader.classList.add("task-header");

    const taskName = document.createElement("div");
    taskName.classList.add("task-name");
    taskName.textContent = task.task;

    const taskBadge = document.createElement("span");
    taskBadge.classList.add("task-badge", task.importance);
    taskBadge.textContent = task.importance.replace("_", " ");

    taskHeader.appendChild(taskName);
    taskHeader.appendChild(taskBadge);

    const taskDesc = document.createElement("div");
    taskDesc.classList.add("task-desc");
    taskDesc.textContent = task.description;

    const taskDeadline = document.createElement("div");
    taskDeadline.classList.add("task-deadline");
    taskDeadline.textContent = new Date(task.deadLine).toLocaleDateString(
      "fr-FR",
      {
        day: "numeric",
        month: "long",
        year: "numeric",
      }
    );

    taskContent.appendChild(taskHeader);
    taskContent.appendChild(taskDesc);
    taskContent.appendChild(taskDeadline);

    article.appendChild(taskContent);

    const status = document.createElement("input");
    status.type = "checkbox";
    status.checked = task.isDone;

    if (task.isDone) {
      article.classList.add("isDone");
      divDone.appendChild(article);
    } else {
      main.appendChild(article);
    }

    status.addEventListener("change", async () => {
      task.isDone = status.checked;
      
      article.classList.toggle("isDone", status.checked);
      if (status.checked) {
        divDone.appendChild(article);
      } else {
        main.appendChild(article);
      }

      try {
        const response = await fetch("../api/tasks.php", {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: task.id, isDone: status.checked }),
          credentials: "include",
        });

        const data = await response.json();
        if (data.error) console.error("Erreur mise à jour status:", data.error);
      } catch (err) {
        console.error("Erreur fetch status:", err);
      }
    });

    const editBtn = document.createElement("button");
    editBtn.innerHTML =
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M15.7279 9.57627L14.3137 8.16206L5 17.4758V18.89H6.41421L15.7279 9.57627ZM17.1421 8.16206L18.5563 6.74785L17.1421 5.33363L15.7279 6.74785L17.1421 8.16206ZM7.24264 20.89H3V16.6473L16.435 3.21231C16.8256 2.82179 17.4587 2.82179 17.8492 3.21231L20.6777 6.04074C21.0682 6.43126 21.0682 7.06443 20.6777 7.45495L7.24264 20.89Z"></path></svg>';
    editBtn.addEventListener("click", () => showEditTask(task, article));

    const deleteBtn = document.createElement("button");
    deleteBtn.innerHTML =
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z"></path></svg>';
    deleteBtn.addEventListener("click", () => deleteTask(task, article));

    article.appendChild(status);
    article.appendChild(editBtn);
    article.appendChild(deleteBtn);
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

    <label for="importance">Importance</label>
    <select name="importance" id="importance">
      <option value="important">important</option>
      <option value="normal">normal</option>
      <option value="peu_important">peu important</option>
    </select>

    <label for="descriptionAdd">Description</label>
    <textarea id="descriptionAdd" required></textarea>

    <label for="deadLine">À faire jusqu'à</label>
    <input type="date" id="deadLine" required>

    <label>
      <input type="checkbox" id="isSchoolAdd">
      Tâche scolaire
    </label>
  </form>
`;

  let addBtn = document.createElement("article");
  addBtn.classList.add("addBtn");

  let addButton = document.createElement("button");
  addButton.innerText = "Ajouter la tâche";

  addButton.addEventListener("click", async function (event) {
    event.preventDefault();

    const name = document.getElementById("nameAdd").value;
    const importance = document.getElementById("importance").value;
    const description = document.getElementById("descriptionAdd").value;
    const deadLine = document.getElementById("deadLine").value;
    const isSchool = document.getElementById("isSchoolAdd").checked ? 1 : 0;

    const taskData = {
      task: name,
      importance,
      description,
      deadLine,
      isSchool,
    };

    const result = await sendTaskData(taskData);
    const sortSelect = document.getElementById("sortTasks");

    if (result.success) {
      tasks = await recupTasks();
      const sortBy = sortSelect.value;
      sortTasks(sortBy);
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
    const response = await fetch("../api/tasks.php", {
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
      return { success: true };
    }
  } catch (error) {
    console.error("Erreur lors de l'envoi des données:", error);
    return { success: false };
  }
}

function showEditTask(task) {
  const main = document.getElementById("main");

  const form = document.createElement("section");
  form.classList.add("formAdd");

  const editDisplay = document.createElement("article");
  editDisplay.classList.add("addDisplay");

  editDisplay.innerHTML = `
  <form class="add">
    <label for="nameEdit">Nom</label>
    <input type="text" id="nameEdit" value="${task.task}" required>

    <label for="importanceEdit">Importance</label>
    <select id="importanceEdit">
      <option value="important">important</option>
      <option value="normal">normal</option>
      <option value="peu_important">peu important</option>
    </select>

    <label for="descriptionEdit">Description</label>
    <textarea id="descriptionEdit" required>${task.description}</textarea>

    <label for="deadLineEdit">À faire jusqu'à</label>
    <input type="date" id="deadLineEdit" value="${
      task.deadLine.split(" ")[0]
    }" required>

    <label>
      <input type="checkbox" id="isSchoolEdit" ${
        task.isSchool ? "checked" : ""
      }>
      Tâche scolaire
    </label>
  </form>
`;

  setTimeout(() => {
    document.getElementById("importanceEdit").value = task.importance;
  });

  const btnContainer = document.createElement("article");
  btnContainer.classList.add("addBtn");

  const updateBtn = document.createElement("button");
  updateBtn.innerText = "Mettre à jour";

  updateBtn.addEventListener("click", async (event) => {
    event.preventDefault();

    const updatedTask = {
      id: task.id,
      task: document.getElementById("nameEdit").value,
      importance: document.getElementById("importanceEdit").value,
      description: document.getElementById("descriptionEdit").value,
      deadLine: document.getElementById("deadLineEdit").value,
      isSchool: document.getElementById("isSchoolEdit").checked ? 1 : 0,
    };

    const result = await updateTaskData(updatedTask);

    if (result.success) {
      tasks = await recupTasks();
      const sortBy = sortSelect.value;
      sortTasks(sortBy);
      showTask(tasks);
      form.remove();
      showNotification("Tâche mise à jour !");
    } else {
      showNotification("Erreur lors de la modification de la tâche.");
    }
  });

  const exitBtn = document.createElement("button");
  exitBtn.innerText = "Exit";

  exitBtn.addEventListener("click", () => {
    form.remove();
  });

  btnContainer.appendChild(updateBtn);
  btnContainer.appendChild(exitBtn);

  editDisplay.appendChild(btnContainer);
  form.appendChild(editDisplay);
  main.appendChild(form);
}

function renderTaskArticle(task, article) {
  article.innerHTML = "";

  const taskContent = document.createElement("div");
  taskContent.classList.add("task-content");

  const taskHeader = document.createElement("div");
  taskHeader.classList.add("task-header");

  const taskName = document.createElement("div");
  taskName.classList.add("task-name");
  taskName.textContent = task.task;

  const taskBadge = document.createElement("span");
  taskBadge.classList.add("task-badge", task.importance);
  taskBadge.textContent = task.importance.replace("_", " ");

  taskHeader.appendChild(taskName);
  taskHeader.appendChild(taskBadge);

  const taskDesc = document.createElement("div");
  taskDesc.classList.add("task-desc");
  taskDesc.textContent = task.description;

  const taskDeadline = document.createElement("div");
  taskDeadline.classList.add("task-deadline");
  taskDeadline.textContent = new Date(task.deadLine).toLocaleDateString(
    "fr-FR",
    {
      day: "numeric",
      month: "long",
      year: "numeric",
    }
  );

  taskContent.appendChild(taskHeader);
  taskContent.appendChild(taskDesc);
  taskContent.appendChild(taskDeadline);

  article.appendChild(taskContent);

  const status = document.createElement("input");
  status.type = "checkbox";
  status.checked = task.isDone;
  status.addEventListener("change", async () => {
    try {
      const response = await fetch("../api/tasks.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: task.id, isDone: status.checked }),
        credentials: "include",
      });
      const data = await response.json();
      if (data.error) console.error("Erreur mise à jour status:", data.error);
      article.classList.toggle("isDone", status.checked);
    } catch (err) {
      console.error("Erreur fetch status:", err);
    }
  });

  const editBtn = document.createElement("button");
  editBtn.innerHTML =
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M15.7279 9.57627L14.3137 8.16206L5 17.4758V18.89H6.41421L15.7279 9.57627ZM17.1421 8.16206L18.5563 6.74785L17.1421 5.33363L15.7279 6.74785L17.1421 8.16206ZM7.24264 20.89H3V16.6473L16.435 3.21231C16.8256 2.82179 17.4587 2.82179 17.8492 3.21231L20.6777 6.04074C21.0682 6.43126 21.0682 7.06443 20.6777 7.45495L7.24264 20.89Z"></path></svg>';
  editBtn.addEventListener("click", () => showEditTask(task, article));

  const deleteBtn = document.createElement("button");
  deleteBtn.innerHTML =
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z"></path></svg>';
  deleteBtn.addEventListener("click", () => deleteTask(task, article));

  article.appendChild(status);
  article.appendChild(editBtn);
  article.appendChild(deleteBtn);
}

async function updateTaskData(taskData) {
  try {
    const response = await fetch("../api/tasks.php", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(taskData),
      credentials: "include",
    });

    const text = await response.text();

    const data = JSON.parse(text);
    if (data.error) {
      console.error("Erreur côté serveur:", data.error);
      return { success: false };
    } else {
      return { success: true };
    }
  } catch (error) {
    console.error("Erreur lors de l'envoi des données:", error);
    return { success: false };
  }
}

async function deleteTask(task) {
  const taskData = { id: task.id };
  try {
    const response = await fetch("../api/tasks.php", {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(taskData),
      credentials: "include",
    });

    const data = await response.json();

    if (data.error) {
      console.error("Erreur côté serveur:", data.error);
      showNotification("Erreur lors de la suppression de la tâche.");
      return { success: false };
    } else {
      showNotification("Tâche supprimée !");

      tasks = await recupTasks();
      const sortBy = sortSelect.value;
      sortTasks(sortBy);
      showTask(tasks);
      return { success: true };
    }
  } catch (error) {
    console.error("Erreur lors de l'envoi des données:", error);
    showNotification("Erreur lors de la suppression de la tâche.");
    return { success: false };
  }
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

function sortTasks(sortBy) {
  const importanceOrder = { important: 1, normal: 2, peu_important: 3 };

  tasks.sort((a, b) => {
    switch (sortBy) {
      case "importance":
        const impDiff = importanceOrder[a.importance] - importanceOrder[b.importance];
        if (impDiff !== 0) return impDiff;
        return new Date(a.deadLine) - new Date(b.deadLine);
        
      case "isSchool":
        const schoolDiff = b.isSchool - a.isSchool;
        if (schoolDiff !== 0) return schoolDiff;
        return new Date(a.deadLine) - new Date(b.deadLine);
        
      default:
        return new Date(a.deadLine) - new Date(b.deadLine);
    }
  });
}