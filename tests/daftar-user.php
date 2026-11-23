<?php
// Exercise the page's actual listing query against connection-local fixtures.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('localhost', 'root', '', 'ksu_st_karolus', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
$conn->query('CREATE TEMPORARY TABLE users (id_user INT PRIMARY KEY, id_role INT, id_active INT)');
$conn->query('CREATE TEMPORARY TABLE user_role (id_role INT PRIMARY KEY, role VARCHAR(50))');
$conn->query('CREATE TEMPORARY TABLE user_status (id_status INT PRIMARY KEY, status VARCHAR(50))');
$conn->query("INSERT INTO user_role VALUES (1,'Developer'),(2,'Administrator'),(3,'Ketua Koperasi'),(4,'Bendahara'),(5,'Anggota'),(6,'Auditor')");
$conn->query("INSERT INTO user_status VALUES (1,'Active'),(2,'No Active')");
$conn->query('INSERT INTO users VALUES (1,1,1),(2,2,1),(3,3,1),(4,4,1),(13,5,1),(14,5,2),(15,5,1),(16,6,1),(17,1,1)');
$source = file_get_contents(__DIR__ . '/../controller/user-management.php');
if (!preg_match('/\$select_users = "([^"]+)";/', $source, $match)) {
    throw new RuntimeException('Listing query not found');
}
$rows = $conn->query($match[1])->fetch_all(MYSQLI_ASSOC);
$ids = array_map('intval', array_column($rows, 'id_user'));
if ($ids !== [1,2,3,4,17]) {
    throw new RuntimeException('Only the four supported staff roles must appear once in ID order');
}
echo "Listing regression passed: all four staff roles visible, members and other roles excluded; real users unchanged.\n";
