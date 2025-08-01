<?php include 'care_schedule_db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">

    <meta charset="UTF-8">
    <title>ElderEase - Care Schedule</title>
    <link rel="stylesheet" href="style.css">
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
        <button class="add-btn">Add Appointment</button>

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
                        <button class="delete-btn" disabled>Det</button>
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
