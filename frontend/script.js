const API_URL = "http://127.0.0.1:8000/api";

async function loadUsers() {
  try {
    const response = await fetch(`${API_URL}/users`);
    const data = await response.json();

    console.log(data);

    if (data.success) {
      const userTableBody = document.getElementById("userTableBody");
      userTableBody.innerHTML = "";

      data.data.items.forEach((user) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.age || "-"}</td>
                    <td>${user.code}</td>
                    <td>
                        <button class="action-btn edit-btn" onclick="editUser(${
                          user.id
                        })">Edit</button>
                        <button class="action-btn delete-btn" onclick="deleteUser(${
                          user.id
                        })">Delete</button>
                    </td>
                `;
        userTableBody.appendChild(row);
      });
    }
  } catch (error) {
    alert("Failed to load user data");
    console.error("Error:", error);
  }
}

async function createUser(userData) {
  try {
    const response = await fetch(`${API_URL}/users`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(userData),
    });

    const data = await response.json();
    if (data.success) {
      alert("User successfully added");
      loadUsers();
      resetForm();
    } else {
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat().join("\n");
        alert(errorMessages);
      } else {
        alert(data.message || "Failed to add user");
      }
    }
  } catch (error) {
    alert("Failed to add user");
    console.error("Error:", error);
  }
}

async function updateUser(id, userData) {
  try {
    const response = await fetch(`${API_URL}/users/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(userData),
    });

    const data = await response.json();
    if (data.success) {
      alert("User successfully updated");
      loadUsers();
      resetForm();
    } else {
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat().join("\n");
        alert(errorMessages);
      } else {
        alert(data.message || "Failed to update user");
      }
    }
  } catch (error) {
    alert("Failed to update user");
    console.error("Error:", error);
  }
}

async function editUser(id) {
  try {
    const response = await fetch(`${API_URL}/users/${id}`);
    const data = await response.json();

    if (data.success) {
      const user = data.data;
      document.getElementById("userId").value = user.id;
      document.getElementById("name").value = user.name;
      document.getElementById("email").value = user.email;
      document.getElementById("age").value = user.age || "";
      document.getElementById("password").value = "";

      document.getElementById("formTitle").textContent = "Edit User";
      document.getElementById("cancelBtn").style.display = "inline-block";
      document.getElementById("password").required = false;
    }
  } catch (error) {
    alert("Failed to load user data");
    console.error("Error:", error);
  }
}

async function updateUser(id, userData) {
  try {
    const response = await fetch(`${API_URL}/users/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(userData),
    });

    const data = await response.json();
    if (data.success) {
      alert("User successfully updated");
      loadUsers();
      resetForm();
    } else {
      alert(data.message || "Failed to update user");
    }
  } catch (error) {
    alert("Failed to update user");
    console.error("Error:", error);
  }
}

async function deleteUser(id) {
  if (confirm("Are you sure you want to delete this user?")) {
    try {
      const response = await fetch(`${API_URL}/users/${id}`, {
        method: "DELETE",
      });

      const data = await response.json();
      if (data.success) {
        alert("User successfully deleted");
        loadUsers();
      } else {
        alert(data.message || "Failed to delete user");
      }
    } catch (error) {
      alert("Failed to delete user");
      console.error("Error:", error);
    }
  }
}

function resetForm() {
  document.getElementById("userForm").reset();
  document.getElementById("userId").value = "";
  document.getElementById("formTitle").textContent = "Add New User";
  document.getElementById("cancelBtn").style.display = "none";
  document.getElementById("password").required = true;
}

document.getElementById("userForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const userId = document.getElementById("userId").value;
  const userData = {
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    age: parseInt(document.getElementById("age").value),
  };

  const password = document.getElementById("password").value;
  if (password) {
    userData.password = password;
  }

  if (userId) {
    await updateUser(userId, userData);
  } else {
    if (!password) {
      alert("Password is required for new user");
      return;
    }
    await createUser(userData);
  }
});

document.getElementById("cancelBtn").addEventListener("click", resetForm);

document.addEventListener("DOMContentLoaded", loadUsers);
