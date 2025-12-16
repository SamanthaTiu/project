<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Grades - <?= esc($course['course_name']) ?></title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fb;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #343a40;
            color: #fff;
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .main-content h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #5865daff;
            font-weight: bold;
            font-size: 32px;
        }
        .grades-container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .grades-table th, .grades-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .grades-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .grades-table tr:hover {
            background-color: #f8f9fa;
        }
        .grade-input {
            width: 80px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .save-btn {
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        .save-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?= view('templates/sidebar') ?>
    
    <div class="main-content">
        <h2>Grades - <?= esc($course['course_name']) ?></h2>
        
        <div class="grades-container">
            <form action="<?= site_url('instructor/course/' . $course['course_id'] . '/grades/save') ?>" method="post">
                <table class="grades-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Assignment 1</th>
                            <th>Assignment 2</th>
                            <th>Midterm</th>
                            <th>Final</th>
                            <th>Total</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                        <tr>
                            <td><?= esc($enrollment['student_name']) ?></td>
                            <td><?= esc($enrollment['student_id'] ?? 'N/A') ?></td>
                            <td>
                                <input type="number" class="grade-input" name="grades[<?= $enrollment['enrollment_id'] ?>][assignment1]" 
                                       value="<?= $enrollment['assignment1'] ?? '' ?>" min="0" max="100">
                            </td>
                            <td>
                                <input type="number" class="grade-input" name="grades[<?= $enrollment['enrollment_id'] ?>][assignment2]" 
                                       value="<?= $enrollment['assignment2'] ?? '' ?>" min="0" max="100">
                            </td>
                            <td>
                                <input type="number" class="grade-input" name="grades[<?= $enrollment['enrollment_id'] ?>][midterm]" 
                                       value="<?= $enrollment['midterm'] ?? '' ?>" min="0" max="100">
                            </td>
                            <td>
                                <input type="number" class="grade-input" name="grades[<?= $enrollment['enrollment_id'] ?>][final]" 
                                       value="<?= $enrollment['final'] ?? '' ?>" min="0" max="100">
                            </td>
                            <td><?= $enrollment['total'] ?? '0' ?>%</td>
                            <td><?= $enrollment['letter_grade'] ?? 'N/A' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="save-btn">Save Grades</button>
            </form>
        </div>
    </div>
</body>
</html>
