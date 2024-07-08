<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reading_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id === 0) {
    echo "Invalid book ID.";
    exit();
}

// Insert progress entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['progress'])) {
    $progress = $_POST['progress'];
    $stmt = $conn->prepare("INSERT INTO progress (book_id, entry) VALUES (?, ?)");
    $stmt->bind_param("is", $book_id, $progress);
    $stmt->execute();
    $stmt->close();
}

// Delete progress entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_entry_id'])) {
    $entry_id = $_POST['delete_entry_id'];
    $stmt = $conn->prepare("DELETE FROM progress WHERE id = ?");
    $stmt->bind_param("i", $entry_id);
    $stmt->execute();
    $stmt->close();
}

// Retrieve book details
$book_result = $conn->query("SELECT * FROM books WHERE id = $book_id");
$book = $book_result->fetch_assoc();

if (!$book) {
    echo "Book not found.";
    exit();
}

// Retrieve progress entries for the book
$progress_result = $conn->query("SELECT * FROM progress WHERE book_id = $book_id ORDER BY date DESC");

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($book['name']); ?> - Reading Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 10px;
            width: 200px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            border: none;
            background-color: #5cb85c;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .delete-button {
            background-color: #d9534f;
            margin-left: 10px;
        }
        .delete-button:hover {
            background-color: #c9302c;
        }
        .back-button {
            padding: 10px 15px;
            border: none;
            background-color: #5bc0de;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #31b0d5;
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($book['name']); ?></h1>
    <form method="POST">
        <input type="text" name="progress" placeholder="Enter progress" required>
        <button type="submit">Add</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Progress</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $progress_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo date('d/m/y', strtotime($row['date'])); ?></td>
                    <td><?php echo htmlspecialchars($row['entry']); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="delete_entry_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="index.php" class="back-button">Back to Home</a>
</body>
</html>
