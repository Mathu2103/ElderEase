document.addEventListener("DOMContentLoaded", () => {
  fetch("fetchCaregiverResident.php")
    .then(response => response.json())
    .then(data => {
      const tbody = document.querySelector("#residentTable tbody");

      if (Array.isArray(data) && data.length > 0) {
        data.forEach(resident => {
          const row = document.createElement("tr");

          row.innerHTML = `
            <td>${resident.resident_name}</td>
            <td>${resident.age}</td>
            <td>${resident.gender}</td>
            <td>${resident.medical_condition || ''}</td>
            <td>${resident.emergency_contact || ''}</td>
            <td>${resident.caregiver_name || 'N/A'}</td>
          `;

          tbody.appendChild(row);
        });
      } else {
        const row = document.createElement("tr");
        const cell = document.createElement("td");
        cell.colSpan = 6;
        cell.textContent = "No residents found.";
        cell.style.textAlign = "center";
        row.appendChild(cell);
        tbody.appendChild(row);
      }
    })
    .catch(error => {
      console.error("Error fetching residents:", error);
    });
});
