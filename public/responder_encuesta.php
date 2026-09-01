<?php
session_start();
require __DIR__ . "/conectar.php";

$mensaje_respuesta = $_SESSION["mensaje_respuesta"] ?? "";
unset($_SESSION["mensaje_respuesta"]);

$id_encuesta = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id_encuesta <= 0) {
    header("Location: index.php");
    exit;
}

$stmtEnc = $pdo->prepare("SELECT * FROM ENCUESTA WHERE id_encuesta = :id");
$stmtEnc->execute(["id" => $id_encuesta]);
$encuestaActual = $stmtEnc->fetch(PDO::FETCH_ASSOC);

if (!$encuestaActual) {
    header("Location: index.php");
    exit;
}

// Registrar el acceso (dispositivo/IP), igual que en documentos
$stmtAcceso = $pdo->prepare(
    "INSERT INTO ACCESO_ENCUESTA (id_encuesta, id_paciente, dispositivo, ip_address, fecha_acceso)
     VALUES (:id_encuesta, :id_paciente, :dispositivo, :ip_address, NOW())"
);
$stmtAcceso->execute([
    "id_encuesta"  => $id_encuesta,
    "id_paciente"  => null,
    "dispositivo"  => $_SERVER["HTTP_USER_AGENT"] ?? null,
    "ip_address"   => $_SERVER["REMOTE_ADDR"] ?? null
]);

$sql = "SELECT id_pregunta, texto_pregunta, tipo_pregunta, obligatoria, orden
        FROM PREGUNTA
        WHERE id_encuesta = :id_encuesta
        ORDER BY orden ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["id_encuesta" => $id_encuesta]);
$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$opcionesPorPregunta = [];
$idsConOpciones = array_column(
    array_filter($preguntas, fn($p) => in_array($p["tipo_pregunta"], ["opcion_multiple", "escala"])),
    "id_pregunta"
);

if (!empty($idsConOpciones)) {
    $placeholders = implode(",", array_fill(0, count($idsConOpciones), "?"));
    $stmtOp = $pdo->prepare("SELECT * FROM OPCION_RESPUESTA WHERE id_pregunta IN ($placeholders) ORDER BY id_opcion ASC");
    $stmtOp->execute($idsConOpciones);
    foreach ($stmtOp->fetchAll(PDO::FETCH_ASSOC) as $op) {
        $opcionesPorPregunta[$op["id_pregunta"]][] = $op;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($encuestaActual['titulo']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <header class="shadow-sm py-3 px-4">
    <h2 class="fw-bold m-0"><?= htmlspecialchars($encuestaActual['titulo']) ?></h2>
    <p class="text-muted"><?= htmlspecialchars($encuestaActual['descripcion']) ?></p>
  </header>

  <main class="container my-4" style="max-width: 700px;">
    <?php if (!empty($mensaje_respuesta)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($mensaje_respuesta) ?></div>
    <?php endif; ?>

    <?php if (empty($preguntas)): ?>
      <p class="text-muted">No hay preguntas en este formulario todavía.</p>
    <?php else: ?>
      <form action="guardar_respuesta.php" method="POST">
        <input type="hidden" name="id_encuesta" value="<?= (int) $id_encuesta ?>">

        <?php foreach ($preguntas as $preg): ?>
          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title fw-bold">
                <?= htmlspecialchars($preg['texto_pregunta']) ?>
                <?php if ($preg['obligatoria']): ?><span class="text-danger">*</span><?php endif; ?>
              </h5>

              <?php if ($preg['tipo_pregunta'] === 'texto'): ?>
                <textarea class="form-control" name="pregunta_<?= (int) $preg['id_pregunta'] ?>" rows="3"></textarea>

              <?php elseif (in_array($preg['tipo_pregunta'], ['opcion_multiple', 'escala'])): ?>
                <?php foreach (($opcionesPorPregunta[$preg['id_pregunta']] ?? []) as $op): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="opcion_<?= (int) $preg['id_pregunta'] ?>"
                           value="<?= (int) $op['id_opcion'] ?>"
                           id="op_<?= (int) $op['id_opcion'] ?>">
                    <label class="form-check-label" for="op_<?= (int) $op['id_opcion'] ?>">
                      <?= htmlspecialchars($op['texto_opcion']) ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <button class="btn btn-primary">Enviar respuestas</button>
      </form>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>