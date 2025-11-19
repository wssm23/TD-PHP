<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
try {
    $mysqlClient = new PDO(
        'mysql:host=localhost;port=3306;dbname=dz;charset=utf8',
        'root',
        ''
    );
} catch (PDOException $e) {
    die($e->getMessage());
}
 
$sortColumn = 'nom';
$sortOrder = 'ASC';
$errorMessage = '';
 
if (isset($_GET['sort'])) {
    $sortColumn = $_GET['sort'];
}
 
if (isset($_GET['order'])) {
    $sortOrder = $_GET['order'];
}
 
$validColumns = ['nom', 'pays', 'course', 'temps'];
$validOrders = ['ASC', 'DESC'];
 
if (!in_array($sortColumn, $validColumns, true)) {
    $errorMessage = 'Paramètre de tri inconnu — tri par défaut appliqué.';
    $sortColumn = 'nom';
}
 
$query = $mysqlClient->prepare('SELECT * FROM `dz`.`100` ORDER BY ' . $sortColumn . ' ' . $sortOrder);
$query->execute();
$data = $query->fetchAll(PDO::FETCH_ASSOC);
 
$mysqlClient = null;
$dbh = null;
?>

<style>

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #121212;
    color: #e0e0e0;
    margin: 40px;
}


table {
    border-collapse: collapse;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    background: #1e1e2f;
    box-shadow: 0 8px 20px rgba(0,0,0,0.5);
    border-radius: 12px;
    overflow: hidden;
}


thead {
    background: #29294d;
    color: #ffffff;
}

thead th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s, color 0.3s;
}

thead th:hover {
    background: #3f3fa5;
    color: #fffacd;
}


tbody tr {
    transition: background 0.3s;
}

tbody tr:nth-child(even) {
    background: #1b1b2b;
}

tbody tr:hover {
    background: #3f3fa5;
    color: #ffffff;
}


td {
    padding: 12px 15px;
    border-bottom: 1px solid #2c2c3c;
}


a {
    color: inherit;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    color: #ffdd57;
}


th, td {
    letter-spacing: 0.5px;
}

</style>


<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th><a href="?sort=nom&order=<?php echo ($sortColumn === 'nom' && $sortOrder === 'ASC') ? 'DESC' : 'ASC'; ?>">Nom <?php echo ($sortColumn === 'nom') ? (($sortOrder === 'ASC') ? '↑' : '↓') : ''; ?></a></th>
            <th><a href="?sort=pays&order=<?php echo ($sortColumn === 'pays' && $sortOrder === 'ASC') ? 'DESC' : 'ASC'; ?>">Pays <?php echo ($sortColumn === 'pays') ? (($sortOrder === 'ASC') ? '↑' : '↓') : ''; ?></a></th>
            <th><a href="?sort=course&order=<?php echo ($sortColumn === 'course' && $sortOrder === 'ASC') ? 'DESC' : 'ASC'; ?>">Course <?php echo ($sortColumn === 'course') ? (($sortOrder === 'ASC') ? '↑' : '↓') : ''; ?></a></th>
            <th><a href="?sort=temps&order=<?php echo ($sortColumn === 'temps' && $sortOrder === 'ASC') ? 'DESC' : 'ASC'; ?>">Temps <?php echo ($sortColumn === 'temps') ? (($sortOrder === 'ASC') ? '↑' : '↓') : ''; ?></a></th>
        </tr>
    </thead>
<?php foreach ($data as $value)
{ ?>
    <tr>
        <td><?php echo $value ["nom"]; ?></td>
        <td><?php echo $value ["pays"]; ?></td>
        <td><?php echo $value ["course"]; ?></td>
        <td><?php echo $value ["temps"]; ?>s</td>
    </tr>
<?php } ?>
</table>
<?php*