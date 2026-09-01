<?php
require __DIR__ . "/conectar.php";

$id_categoria = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id_categoria <= 0) {
    header("Location: index.php");
    exit;
}

$stmtCat = $pdo->prepare("SELECT * FROM CATEGORIA_DOCUMENTO WHERE id_categoria = :id");
$stmtCat->execute(["id" => $id_categoria]);
$categoriaActual = $stmtCat->fetch(PDO::FETCH_ASSOC);

if (!$categoriaActual) {
    header("Location: index.php");
    exit;
}

// Documentos directo a esta categoría, O bajo cualquiera de sus subcategorías
$sql = "SELECT d.*, sd.nombre_subcategoria
        FROM DOCUMENTO d
        LEFT JOIN SUBCATEGORIA_DOCUMENTO sd ON d.id_subcategoria = sd.id_subcategoria
        WHERE d.id_categoria = :id
           OR sd.id_categoria = :id2";
$stmt = $pdo->prepare($sql);
$stmt->execute(["id" => $id_categoria, "id2" => $id_categoria]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($categoriaActual['nombre_categoria']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <header class="shadow-sm py-3 px-4">
    <h2 class="fw-bold m-0"><?= htmlspecialchars($categoriaActual['nombre_categoria']) ?></h2>
    <p class="text-muted"><?= htmlspecialchars($categoriaActual['descripcion']) ?></p>
  </header>

  <main class="container my-4">
    <?php if (empty($documentos)): ?>
      <p class="text-muted">No hay documentos en esta categoría todavía.</p>
    <?php else: ?>
      <?php foreach ($documentos as $doc): ?>
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-bold"><?= htmlspecialchars($doc['titulo']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($doc['descripcion']) ?></p>
            <?php if ($doc['nombre_subcategoria']): ?>
              <span class="badge bg-secondary mb-2"><?= htmlspecialchars($doc['nombre_subcategoria']) ?></span>
            <?php endif; ?>

            <?php
              $ext = strtolower(pathinfo($doc['archivo_url'], PATHINFO_EXTENSION));
            ?>
            <?php if ($ext === "pdf"): ?>
              <iframe src="<?= htmlspecialchars($doc['archivo_url']) ?>" type="application/pdf" width="100%" height="500px"> </iframe>
            <?php elseif (in_array($ext, ["jpg", "jpeg", "png", "gif", "webp"])): ?>
              <img src="<?= htmlspecialchars($doc['archivo_url']) ?>" class="img-fluid" alt="<?= htmlspecialchars($doc['titulo']) ?>">
            <?php else: ?>
              <a href="<?= htmlspecialchars($doc['archivo_url']) ?>" target="_blank">Abrir documento</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>