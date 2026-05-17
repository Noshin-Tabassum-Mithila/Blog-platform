<?php
include '../includes/auth.php';
include '../includes/db.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Article Queue</title>
<style>
body{
font-family: arial;
background-color: #f0f2f5;
padding: 20px;
}
h1{
color: #2c3e50;
}
table{
width: 100%;
border-collapse: collapse;
background-color: white;
}
th, td{
padding: 10px;
border: 1px solid #ddd;
text-align: left;
}
th{
background-color: #2c3e50;
color: white;
}
.btn-approve{
background-color: green;
color: white;
padding: 5px 10px;
text-decoration: none;
border-radius: 4px;
}
.btn-revision{
background-color: orange;
color: white;
padding: 5px 10px;
text-decoration: none;
border-radius: 4px;
}
a.back{
display: inline-block;
margin-bottom: 15px;
color: #2c3e50;
text-decoration: none;
}
</style>
</head>
<body>

<a class="back" href="index.php">← Back to Dashboard</a>
<h1>Article Queue</h1>

<?php
//submitted articles load korbo
$sql = "SELECT articles.*, users.name as author_name 
        FROM articles 
        JOIN users ON articles.author_id = users.id
        WHERE articles.status='submitted'
        ORDER BY articles.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<?php
//approve action
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $q = "UPDATE articles SET status='published' WHERE id='$id'";
    mysqli_query($conn, $q);

    //revision table a insert
    $eq = "INSERT INTO article_revisions (article_id, editor_id, status) 
           VALUES ('$id', '".$_SESSION['user_id']."', 'approved')";
    mysqli_query($conn, $eq);

    header('Location: queue.php');
}

//revision request action
if(isset($_GET['revision'])){
    $id = $_GET['revision'];
    $note = "Please revise your article";
    $q = "UPDATE articles SET status='draft' WHERE id='$id'";
    mysqli_query($conn, $q);

    $eq = "INSERT INTO article_revisions (article_id, editor_id, notes, status) 
           VALUES ('$id', '".$_SESSION['user_id']."', '$note', 'revision_requested')";
    mysqli_query($conn, $eq);

    header('Location: queue.php');
}
?>

<table>
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Date</th>
        <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['author_name']; ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td>
            <a class="btn-approve" href="queue.php?approve=<?php echo $row['id']; ?>">Approve</a>
            &nbsp;
            <a class="btn-revision" href="queue.php?revision=<?php echo $row['id']; ?>">Revision</a>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>