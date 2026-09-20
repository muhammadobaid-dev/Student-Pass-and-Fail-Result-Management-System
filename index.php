<?php
session_start();

if (!isset($_SESSION['students'])) {
    $_SESSION['students'] = [];
}

$maxStudents = 20;
$message = '';
$error = '';

if (isset($_POST['reset'])) {
    $_SESSION['students'] = [];
    $message = 'All student records have been cleared.';
}

if (isset($_POST['submit_marks'])) {
    if (count($_SESSION['students']) >= $maxStudents) {
        $error = 'Maximum of 20 students already entered.';
    } else {
        $marks = isset($_POST['marks']) ? trim($_POST['marks']) : '';

        if ($marks === '' || !is_numeric($marks)) {
            $error = 'Please enter valid numeric marks.';
        } else {
            $marks = (float) $marks;

            if ($marks < 0 || $marks > 100) {
                $error = 'Marks must be between 0 and 100.';
            } else {
                $status = ($marks >= 50) ? 'Pass' : 'Fail';
                $_SESSION['students'][] = [
                    'marks' => $marks,
                    'status' => $status,
                ];
                $message = 'Student record added successfully. Status: ' . $status . '.';
            }
        }
    }
}

$students = $_SESSION['students'];
$totalEntered = count($students);
$remaining = $maxStudents - $totalEntered;
$isComplete = ($totalEntered >= $maxStudents);

$passed = [];
$failed = [];

foreach ($students as $student) {
    if ($student['status'] === 'Pass') {
        $passed[] = $student;
    } else {
        $failed[] = $student;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Pass and Fail Result Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Pass and Fail Result Management System</h1>
            <p class="subtitle">Enter marks for up to 20 students. Pass mark: 50 or above.</p>
        </header>

        <section class="info-bar">
            <p>Students entered: <strong><?php echo $totalEntered; ?></strong> / <?php echo $maxStudents; ?></p>
            <p>Remaining slots: <strong><?php echo $remaining; ?></strong></p>
        </section>

        <?php if ($message !== ''): ?>
            <div class="alert success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!$isComplete): ?>
            <section class="form-section">
                <h2>Enter Student Marks</h2>
                <form method="POST" action="" class="marks-form">
                    <label for="marks">Student Marks (0 – 100)</label>
                    <input
                        type="number"
                        id="marks"
                        name="marks"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                        placeholder="e.g. 75"
                    >
                    <button type="submit" name="submit_marks" class="btn btn-primary">Submit Marks</button>
                </form>
            </section>
        <?php else: ?>
            <section class="complete-notice">
                <h2>All 20 Students Submitted</h2>
                <p>Results are shown below. Use Reset All to start over.</p>
            </section>
        <?php endif; ?>

        <?php if ($totalEntered > 0): ?>
            <section class="results">
                <div class="table-block">
                    <h2>Passed Students (Marks ≥ 50)</h2>
                    <?php if (count($passed) > 0): ?>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($passed as $index => $student): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars((string) $student['marks']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="empty">No students have passed yet.</p>
                    <?php endif; ?>
                </div>

                <div class="table-block">
                    <h2>Failed Students (Marks &lt; 50)</h2>
                    <?php if (count($failed) > 0): ?>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($failed as $index => $student): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars((string) $student['marks']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="empty">No students have failed yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="reset-section">
            <form method="POST" action="">
                <button type="submit" name="reset" class="btn btn-reset" onclick="return confirm('Clear all student records?');">
                    Reset All
                </button>
            </form>
        </section>
    </div>
</body>
</html>
