<?php
session_start();
require __DIR__ . "/conectar.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id_encuesta = (int) ($_POST["id_encuesta"] ?? 0);

if ($id_encuesta <= 0) {
    $_SESSION["mensaje_respuesta"] = "Encuesta inválida";
    header("Location: index.php");
    exit;
}

$stmtEnc = $pdo->prepare("SELECT * FROM ENCUESTA WHERE id_encuesta = :id");
$stmtEnc->execute(["id" => $id_encuesta]);
$encuestaActual = $stmtEnc->fetch(PDO::FETCH_ASSOC);

if (!$encuestaActual) {
    $_SESSION["mensaje_respuesta"] = "Encuesta inválida";
    header("Location: index.php");
    exit;
}

$anonima     = $encuestaActual["requiere_identificacion"] ? 0 : 1;
$id_paciente = null; // por ahora, hasta que armemos la captura de cédula

//  ESTO ES LO QUE FALTA
$stmtPreg = $pdo->prepare("SELECT * FROM PREGUNTA WHERE id_encuesta = :id");
$stmtPreg->execute(["id" => $id_encuesta]);
$preguntas = $stmtPreg->fetchAll(PDO::FETCH_ASSOC);


if (empty($preguntas)) {
    $_SESSION["mensaje_respuesta"] = "Esta encuesta no tiene preguntas";
    header("Location: responder_encuesta.php?id=$id_encuesta");
    exit;
}

// Validar obligatorias
$mensaje = "";
foreach ($preguntas as $p) {
    if (!$p["obligatoria"]) continue;

    if ($p["tipo_pregunta"] === "texto") {
        if (trim($_POST["pregunta_" . $p["id_pregunta"]] ?? "") === "") {
            $mensaje = "Falta responder: " . $p["texto_pregunta"];
            break;
        }
    } else { // opcion_multiple o escala
        if (empty($_POST["opcion_" . $p["id_pregunta"]])) {
            $mensaje = "Falta responder: " . $p["texto_pregunta"];
            break;
        }
    }
}

if ($mensaje !== "") {
    $_SESSION["mensaje_respuesta"] = $mensaje;
    header("Location: responder_encuesta.php?id=$id_encuesta");
    exit;
}

try {
    $pdo->beginTransaction();

    $stmtResp = $pdo->prepare(
        "INSERT INTO RESPUESTA_ENCUESTA (id_encuesta, id_paciente, anonima, fecha_respuesta)
         VALUES (:id_encuesta, :id_paciente, :anonima, NOW())"
    );
    $stmtResp->execute([
        "id_encuesta" => $id_encuesta,
        "id_paciente" => $id_paciente,
        "anonima"     => $anonima
    ]);
    $id_respuesta_encuesta = $pdo->lastInsertId();

    $stmtDetalle = $pdo->prepare(
        "INSERT INTO RESPUESTA_DETALLE (id_pregunta, id_opcion, id_respuesta_encuesta, texto_libre)
         VALUES (:id_pregunta, :id_opcion, :id_respuesta_encuesta, :texto_libre)"
    );

    foreach ($preguntas as $p) {
        $id_pregunta = $p["id_pregunta"];

        if ($p["tipo_pregunta"] === "texto") {
            $texto = trim($_POST["pregunta_" . $id_pregunta] ?? "");
            if ($texto === "") continue; // opcional no respondida

            $stmtDetalle->execute([
                "id_pregunta"            => $id_pregunta,
                "id_opcion"              => null,
                "id_respuesta_encuesta"  => $id_respuesta_encuesta,
                "texto_libre"            => $texto
            ]);
        } else { // opcion_multiple o escala
            $id_opcion = $_POST["opcion_" . $id_pregunta] ?? "";
            if ($id_opcion === "") continue; // opcional no respondida

            $stmtDetalle->execute([
                "id_pregunta"            => $id_pregunta,
                "id_opcion"              => (int) $id_opcion,
                "id_respuesta_encuesta"  => $id_respuesta_encuesta,
                "texto_libre"            => null
            ]);
        }
    }

    $pdo->commit();
    $_SESSION["mensaje_respuesta"] = "¡Gracias! Tu respuesta fue registrada.";
    header("Location: index.html");
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION["mensaje_respuesta"] = "Error al guardar la respuesta" . $e->getMessage();
    header("Location: responder_encuesta.php?id=$id_encuesta");  
      exit;
}