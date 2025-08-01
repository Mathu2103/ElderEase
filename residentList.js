document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.querySelector("#residentTable tbody");
  const popupOverlay = document.getElementById("popupOverlay");
  const addBtn = document.querySelector(".add-btn");
  const cancelBtn = document.getElementById("cancelBtn");
  const form = document.getElementById("residentForm");
  const residentIdInput = document.querySelector("#residentId");
  const popupTitle = document.querySelector("#popupTitle");

  // Animate brand name
  setTimeout(() => {
    document.getElementById("brandName").classList.add("blink");
  }, 1000);

  // Function to fetch and display residents
  async function loadResidents() {
    try {
      const response = await fetch("fetchResident.php");
      const residents = await response.json();

      // Clear existing rows
      tableBody.innerHTML = "";

      // Populate table rows dynamically
      residents.forEach(resident => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
          <td>${resident.full_name}</td>
          <td>${resident.age}</td>
          <td>${resident.gender}</td>
          <td>${resident.medical_condition}</td>
          <td>${resident.emergency_contact}</td>
          <td>${resident.caregiver_id}</td>
          <td>
            <button class="action-btn edit" data-id="${resident.id}">Edit</button>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      // Add event listeners to edit buttons
      document.querySelectorAll(".action-btn.edit").forEach(button => {
        button.addEventListener("click", async (e) => {
          const id = e.target.getAttribute("data-id");

          try {
            const res = await fetch(`fetchSingleResident.php?id=${id}`);
            const resident = await res.json();

            // Fill the form fields with resident data
            form.elements["full_name"].value = resident.full_name;
            form.elements["age"].value = resident.age;
            form.elements["gender"].value = resident.gender;
            form.elements["medical_condition"].value = resident.medical_condition;
            form.elements["emergency_contact"].value = resident.emergency_contact;
            form.elements["caregiver_id"].value = resident.caregiver_id;
            residentIdInput.value = resident.id; // set hidden id field

            // Change popup title
            popupTitle.textContent = "Edit Resident";

            // Show popup
            popupOverlay.style.display = "flex";

          } catch (error) {
            alert("Failed to load resident data for editing.");
            console.error(error);
          }
        });
      });

    } catch (error) {
      console.error("Error loading residents:", error);
      alert("Failed to load residents data.");
    }
  }

  // Initial load of residents
  loadResidents();

  // Open form for adding new resident
  addBtn.addEventListener("click", () => {
    form.reset();
    residentIdInput.value = ""; // clear hidden id field for new entry
    popupTitle.textContent = "Add Resident"; // reset popup title
    popupOverlay.style.display = "flex";
  });

  // Close form
  cancelBtn.addEventListener("click", () => {
    popupOverlay.style.display = "none";
    form.reset();
    residentIdInput.value = "";
  });

  // Handle form submission (add or update)
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const isEditing = residentIdInput.value.trim() !== "";

    try {
      // Decide URL and method based on add or edit
      const url = isEditing ? "updateResident.php" : "insertResident.php";

      const res = await fetch(url, {
        method: "POST",
        body: formData,
      });

      const text = await res.text();

      if (text.trim() === "success") {
        alert(isEditing ? "Resident updated successfully!" : "Resident added successfully!");
        popupOverlay.style.display = "none";
        form.reset();
        residentIdInput.value = "";
        popupTitle.textContent = "Add Resident";
        loadResidents();  // Reload the resident list dynamically
      } else {
        alert("Error: " + text);
      }
    } catch (error) {
      alert("Network Error: " + error);
    }
  });
});
