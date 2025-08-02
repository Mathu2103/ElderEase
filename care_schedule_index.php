<?php
include 'care_schedule_db.php';

// Insert New Appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_appointment'])) {
    $resident_id = $_POST['resident_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = $_POST['description'];

    // Get caregiver_id for selected resident
    $caregiver_query = $conn->prepare("SELECT caregiver_id FROM residents WHERE id = ?");
    $caregiver_query->bind_param("i", $resident_id);
    $caregiver_query->execute();
    $caregiver_result = $caregiver_query->get_result();

    if ($caregiver_result->num_rows > 0) {
        $caregiver_row = $caregiver_result->fetch_assoc();
        $caregiver_id = $caregiver_row['caregiver_id'];

        if ($caregiver_id !== null) {
            $stmt = $conn->prepare("INSERT INTO care_schedule (resident_id, caregiver_id, task_description, date, time) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $resident_id, $caregiver_id, $description, $date, $time);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error: This resident has no caregiver assigned.");
        }
    } else {
        die("Error: Resident not found.");
    }

    header("Location: care_schedule_index.php");
    exit;
}

// Delete Appointment
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM care_schedule WHERE id = $id");
    header("Location: care_schedule_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="care_schedule_style.css">
    <meta charset="UTF-8">
    <title>ElderEase - Care Schedule</title>
</head>
<body>
    <header>
        <div class="logo">👨‍⚕️ ElderEase</div>
        <nav>
            <a href="#">Home</a>
            <a href="#">Residents</a>
            <a href="#">Care Schedule</a>
        </nav>
    </header>

    <main>
        <h1>Care Schedule</h1>
        <button class="add-btn" id="openFormBtn">Add Appointment</button>

        <!-- FORM (HIDDEN BY DEFAULT) -->
        <div id="form-modal" class="modal" style="display: none;">
            <form method="POST" class="modal-content">
                <h2>Add Appointment</h2>

                <label for="resident_id">Resident</label>
                <select name="resident_id" required>
                    <option value="">Select</option>
                    <?php
                    $res = $conn->query("SELECT * FROM residents");
                    while ($r = $res->fetch_assoc()):
                        echo "<option value='{$r['id']}'>{$r['full_name']}</option>";
                    endwhile;
                    ?>
                </select>

                <label for="date">Date</label>
                <input type="date" name="date" required>

                <label for="time">Time</label>
                <input type="time" name="time" required>

                <label for="description">Description</label>
                <input type="text" name="description" required>

                <div class="modal-actions">
                    <button type="submit" name="add_appointment" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>

        <script>
            function closeModal() {
                document.getElementById('form-modal').style.display = 'none';
            }

            document.getElementById('openFormBtn').addEventListener('click', function () {
                document.getElementById('form-modal').style.display = 'block';
            });
        </script>

        <!-- TABLE -->
        <table>
            <thead>
                <tr>
                    <th>Resident</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT cs.id, r.full_name AS resident_name, cs.date, cs.time, cs.task_description
                        FROM care_schedule cs
                        INNER JOIN residents r ON cs.resident_id = r.id
                        ORDER BY cs.date, cs.time";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['resident_name']) ?></td>
                    <td><?= date("m/d/Y", strtotime($row['date'])) ?></td>
                    <td><?= date("h:i A", strtotime($row['time'])) ?></td>
                    <td><?= htmlspecialchars($row['task_description']) ?></td>
                    <td>
                        <button class="edit-btn" disabled>Edit</button>
                        <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this appointment?')">Det</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="5">No appointments found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>ElderEase</p>
    </footer>
</body>
</html>
