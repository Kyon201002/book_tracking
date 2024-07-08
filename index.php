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

// Insert book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_name'])) {
    $book_name = $_POST['book_name'];
    $stmt = $conn->prepare("INSERT INTO books (name) VALUES (?)");
    $stmt->bind_param("s", $book_name);
    $stmt->execute();
    $stmt->close();
}

// Delete book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_book_id'])) {
    $book_id = $_POST['delete_book_id'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    // Also delete progress entries for this book
    $stmt = $conn->prepare("DELETE FROM progress WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    $stmt->close();
}

// Retrieve all books
$result = $conn->query("SELECT * FROM books");

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reading Tracker</title>
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
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: white;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        a {
            text-decoration: none;
            color: #333;
        }
        .delete-button {
            background-color: #d9534f;
            margin-left: 10px;
        }
        .delete-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <h1>Reading Tracker</h1>
    <form method="POST">
        <input type="text" name="book_name" placeholder="Enter book name" required>
        <button type="submit">Add</button>
    </form>
    <ul>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <li>
                <a href="book.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="delete_book_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="delete-button">Delete</button>
                </form>
            </li>
        <?php } ?>
    </ul>
</body>
</html>
