<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=tododb;charset=utf8', 'root', '');

// Traitement des actions reçues en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;
    $title = $_POST['title'] ?? null;

    if ($action === 'insert' && !empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO todo (title) VALUES (?)");
        $stmt->execute([$title]);
    } elseif ($action === 'delete' && $id) {
        $stmt = $pdo->prepare("DELETE FROM todo WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'toggle' && $id) {
        $stmt = $pdo->prepare("UPDATE todo SET done = NOT done WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Récupération des tâches triées du plus récent au plus ancien
$stmt = $pdo->query("SELECT * FROM todo ORDER BY created_at DESC");
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma Todolist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">TodoList</a>
    </div>
</nav>

<div class="container">
    <!-- Formulaire d'ajout de tâche -->
    <form method="post" class="mb-4">
        <div class="input-group">
            <input type="text" name="title" class="form-control" placeholder="Ajouter une nouvelle tâche..." required>
            <button type="submit" name="action" value="insert" class="btn btn-primary">Ajouter</button>
        </div>
    </form>

    <!-- Liste des tâches -->
    <ul class="list-group">
        <?php foreach ($taches as $tache): ?>
            <li class="list-group-item <?= $tache['done'] ? 'list-group-item-success' : 'list-group-item-warning' ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($tache['title']) ?></span>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="id" value="<?= $tache['id'] ?>">
                        <button type="submit" name="action" value="toggle" class="btn btn-sm btn-outline-secondary">Toggle</button>
                        <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>