<?php
require __DIR__ . "/conectar.php";

$id_tipo_encuesta = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id_tipo_encuesta <= 0) {
    header("Location: index.php");
    exit;
}

$stmtTipo = $pdo->prepare("SELECT * FROM TIPO_ENCUESTA WHERE id_tipo_encuesta = :id");
$stmtTipo->execute(["id" => $id_tipo_encuesta]);
$tipoActual = $stmtTipo->fetch(PDO::FETCH_ASSOC);

if (!$tipoActual) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM ENCUESTA WHERE id_tipo_encuesta = :id AND estado = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(["id" => $id_tipo_encuesta]);
$encuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($tipoActual['nombre_tipo']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <header class="shadow-sm py-3 px-4">
    <h2 class="fw-bold m-0"><?= htmlspecialchars($tipoActual['nombre_tipo']) ?></h2>
    <p class="text-muted"><?= htmlspecialchars($tipoActual['descripcion']) ?></p>
  </header>

  <main class="container my-4">
    <?php if (empty($encuestas)): ?>
      <p class="text-muted">No hay encuestas de este tipo todavía.</p>
    <?php else: ?>
      <?php foreach ($encuestas as $enc): ?>
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-bold"><?= htmlspecialchars($enc['titulo']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($enc['descripcion']) ?></p>
            <a href="responder_encuesta.php?id=<?= (int) $enc['id_encuesta'] ?>" class="btn btn-primary btn-sm">
              Responder
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>