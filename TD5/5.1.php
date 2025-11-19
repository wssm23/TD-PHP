<?php

$limit = 10;
$search = "";
$data = [];
$totalPages = 0;
$totalRows = 0;
$sort = "nom";
$order = "DESC";
$errors = [];
 

$allowed_sorts = ["nom", "pays", "course", "temps"];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts)) {
    $sort = $_GET['sort'];
}
 
if (isset($_GET['order'])) {
    $tmp = strtoupper($_GET['order']);
    if (in_array($tmp, ["ASC", "DESC"])) {
        $order = $tmp;
    }
}
 

$whereClause = "";
$params = [];
if (!empty($_GET['q'])) {
    $search = trim($_GET['q']);
    $whereClause = "WHERE nom LIKE :search OR pays LIKE :search OR course LIKE :search";
    $params[':search'] = "%{$search}%";
}
 

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
 

try {
    $mysqlClient = new PDO(
        'mysql:host=localhost;dbname=dz;charset=utf8',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
 

$coursesList = $mysqlClient->query("SELECT DISTINCT course FROM `100` ORDER BY course ASC")->fetchAll(PDO::FETCH_COLUMN);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_result'])) {
    $nom = trim($_POST['nom']);
    $pays = strtoupper(trim($_POST['pays']));
    $temps = trim($_POST['temps']);
    $course = trim($_POST['course']);
 
    if (strlen($pays) !== 3) {
        $errors[] = "Le code pays doit contenir exactement 3 lettres.";
    }
    if (!is_numeric($temps)) {
        $errors[] = "Le temps doit être un nombre.";
    }
    if (!in_array($course, $coursesList)) {
        $errors[] = "La course sélectionnée est invalide.";
    }
 
    if (empty($errors)) {
        $stmtInsert = $mysqlClient->prepare("INSERT INTO `100` (nom, pays, temps, course) VALUES (:nom, :pays, :temps, :course)");
        $stmtInsert->execute([
            ':nom' => $nom,
            ':pays' => $pays,
            ':temps' => $temps,
            ':course' => $course
        ]);
       
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}
 

$countSql = "SELECT COUNT(*) FROM `100` $whereClause";
$stmtCount = $mysqlClient->prepare($countSql);
foreach ($params as $k => $v) $stmtCount->bindValue($k, $v);
$stmtCount->execute();
$totalRows = (int)$stmtCount->fetchColumn();
$totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;
 

$selectSql = "SELECT * FROM `100` $whereClause ORDER BY `$sort` $order LIMIT :limit OFFSET :offset";
$stmt = $mysqlClient->prepare($selectSql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
 

$ranks = [];
if (!empty($data)) {
    $coursesOnPage = array_unique(array_map(function($r){ return $r['course']; }, $data));
    $inPlaceholders = [];
    $inParams = [];
    foreach ($coursesOnPage as $i => $c) {
        $ph = ":c{$i}";
        $inPlaceholders[] = $ph;
        $inParams[$ph] = $c;
    }
    $inList = implode(", ", $inPlaceholders);
    $allForCoursesSql = "SELECT id, course, temps FROM `100` WHERE course IN ($inList) ORDER BY course ASC, temps + 0 ASC";
    $stmt2 = $mysqlClient->prepare($allForCoursesSql);
    foreach ($inParams as $k => $v) $stmt2->bindValue($k, $v);
    $stmt2->execute();
    $all = $stmt2->fetchAll(PDO::FETCH_ASSOC);
 
    $currentCourse = null;
    $rank = 0;
    foreach ($all as $row) {
        if ($row['course'] !== $currentCourse) {
            $currentCourse = $row['course'];
            $rank = 1;
        } else {
            $rank++;
        }
        $ranks[$row['id']] = $rank;
    }
}
 

$mysqlClient = null;
 

function urlFor($overrides = []) {
    $params = [
        'q' => $_GET['q'] ?? '',
        'page' => $_GET['page'] ?? 1,
        'sort' => $_GET['sort'] ?? 'nom',
        'order' => $_GET['order'] ?? 'DESC'
    ];
    foreach ($overrides as $k => $v) $params[$k] = $v;
    $pairs = [];
    foreach ($params as $k => $v) {
        if ($v === '' || $v === null) continue;
        $pairs[] = urlencode($k)."=".urlencode($v);
    }
    return $_SERVER['PHP_SELF'] . "?" . implode("&", $pairs);
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Résultats</title>
<style>
    table { border-collapse: collapse; width:100%; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align:left; }
    th a { text-decoration: none; }
    .pagination a { margin-right: 6px; }
</style>
</head>
<body>
 
<h2>Ajouter un résultat</h2>
<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <ul>
        <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="post" action="">
    <input type="text" name="nom" placeholder="Nom du coureur" required>
    <input type="text" name="pays" placeholder="Pays (3 lettres)" maxlength="3" required>
    <input type="text" name="temps" placeholder="Temps" required>
    <select name="course" required>
        <option value="">-- Sélectionner une course --</option>
        <?php foreach ($coursesList as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="add_result">Ajouter</button>
</form>
<hr>
 

<form method="get" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <input type="text" name="q" placeholder="Recherche..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Rechercher</button>
</form>
 

<?php if (empty($data)): ?>
    <p>Aucun résultat (page <?= $page ?> / <?= $totalPages ?>).</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>
                <?php $newOrder = ($sort == 'nom' && $order == 'DESC') ? 'ASC' : 'DESC'; ?>
                <a href="<?= urlFor(['sort'=>'nom','order'=>$newOrder,'page'=>1]) ?>">Nom</a>
                <?= $sort == 'nom' ? ($order == 'DESC' ? ' ▼' : ' ▲') : '' ?>
            </th>
            <th>
                <?php $newOrder = ($sort == 'pays' && $order == 'DESC') ? 'ASC' : 'DESC'; ?>
                <a href="<?= urlFor(['sort'=>'pays','order'=>$newOrder,'page'=>1]) ?>">Pays</a>
                <?= $sort == 'pays' ? ($order == 'DESC' ? ' ▼' : ' ▲') : '' ?>
            </th>
            <th>
                <?php $newOrder = ($sort == 'course' && $order == 'DESC') ? 'ASC' : 'DESC'; ?>
                <a href="<?= urlFor(['sort'=>'course','order'=>$newOrder,'page'=>1]) ?>">Course</a>
                <?= $sort == 'course' ? ($order == 'DESC' ? ' ▼' : ' ▲') : '' ?>
            </th>
            <th>
                <?php $newOrder = ($sort == 'temps' && $order == 'DESC') ? 'ASC' : 'DESC'; ?>
                <a href="<?= urlFor(['sort'=>'temps','order'=>$newOrder,'page'=>1]) ?>">Temps</a>
                <?= $sort == 'temps' ? ($order == 'DESC' ? ' ▼' : ' ▲') : '' ?>
            </th>
            <th>Classement</th>
            <th>Modifier</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['nom']); ?></td>
                <td><?= htmlspecialchars($row['pays']); ?></td>
                <td><?= htmlspecialchars($row['course']); ?></td>
                <td><?= htmlspecialchars($row['temps']); ?></td>
                <td><?= isset($ranks[$row['id']]) ? (int)$ranks[$row['id']] : '-' ?></td>
                <td><a href="edit.php?id=<?= urlencode($row['id']); ?>">Modifier</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
 

<div class="pagination" style="margin-top:10px;">
    <?php
    $start = max(1, $page - 3);
    $end = min($totalPages, $page + 3);
    if ($page > 1) {
        echo '<a href="'.urlFor(['page'=>$page-1]).'">&laquo; Précédent</a>';
    }
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            echo "<strong>$i</strong> ";
        } else {
            echo '<a href="'.urlFor(['page'=>$i]).'">'.$i.'</a> ';
        }
    }
    if ($page < $totalPages) {
        echo '<a href="'.urlFor(['page'=>$page+1]).'">Suivant &raquo;</a>';
    }
    ?>
</div>
 
</body>
</html>
