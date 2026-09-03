<?php
session_start();
require __DIR__ . "/conectar.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo            = trim($_POST["titulo"] ?? "");
    $descripcion       = trim($_POST["descripcion"] ?? "");
    $privado           = isset($_POST["privado"]);
    $estado            = $privado ? 0 : 1;
    $id_tipo_encuesta  = (int) ($_POST["id_tipo_encuesta"] ?? 0);
    $preguntas         = $_POST["preguntas"] ?? [];
    $requiere_identificacion = isset($_POST["requiere_identificacion"]) ? 1 : 0;

    // TEMPORAL hasta que exista login: id fijo para pruebas
    $id_funcionario_creador = 1;

    if ($titulo === "") {
        $mensaje = "Falta el título";
    } elseif ($descripcion === "") {
        $mensaje = "Falta la descripción";
    } elseif ($id_tipo_encuesta <= 0) {
        $mensaje = "Debe seleccionar un tipo de encuesta";
    } elseif (empty($preguntas)) {
        $mensaje = "Agregue al menos una pregunta";
    } else {
        try {
            $pdo->beginTransaction();

           $stmtEnc = $pdo->prepare(
                    "INSERT INTO ENCUESTA (titulo, descripcion, estado, id_tipo_encuesta, id_funcionario_creador, requiere_identificacion)
                    VALUES (:titulo, :descripcion, :estado, :id_tipo_encuesta, :id_funcionario_creador, :requiere_identificacion)"
                );
                $stmtEnc->execute([
                    "titulo"                   => $titulo,
                    "descripcion"              => $descripcion,
                    "estado"                   => $estado,
                    "id_tipo_encuesta"         => $id_tipo_encuesta,
                    "id_funcionario_creador"   => $id_funcionario_creador,
                    "requiere_identificacion"  => $requiere_identificacion
                ]);
            $id_encuesta = $pdo->lastInsertId();

            $stmtPreg = $pdo->prepare(
                "INSERT INTO PREGUNTA (id_encuesta, texto_pregunta, tipo_pregunta, obligatoria, orden)
                 VALUES (:id_encuesta, :texto_pregunta, :tipo_pregunta, :obligatoria, :orden)"
            );
            $stmtOp = $pdo->prepare(
                "INSERT INTO OPCION_RESPUESTA (id_pregunta, texto_opcion, valor)
                 VALUES (:id_pregunta, :texto_opcion, :valor)"
            );

            foreach ($preguntas as $p) {
                $texto_pregunta = trim($p["texto_pregunta"] ?? "");
                $tipo_pregunta  = $p["tipo_pregunta"] ?? "texto";
                $obligatoria    = !empty($p["obligatoria"]) ? 1 : 0;
                $orden          = (int) ($p["orden"] ?? 0);

                if ($texto_pregunta === "") continue;
                if (!in_array($tipo_pregunta, ["texto", "opcion_multiple", "escala"])) {
                    $tipo_pregunta = "texto";
                }

                $stmtPreg->execute([
                    "id_encuesta"    => $id_encuesta,
                    "texto_pregunta" => $texto_pregunta,
                    "tipo_pregunta"  => $tipo_pregunta,
                    "obligatoria"    => $obligatoria,
                    "orden"          => $orden
                ]);
                $id_pregunta = $pdo->lastInsertId();

                if (in_array($tipo_pregunta, ["opcion_multiple", "escala"]) && !empty($p["opciones"])) {
                    foreach ($p["opciones"] as $op) {
                        $texto_opcion = trim($op["texto_opcion"] ?? "");
                        $valor        = trim($op["valor"] ?? "");
                        if ($texto_opcion === "") continue;

                        $stmtOp->execute([
                            "id_pregunta"  => $id_pregunta,
                            "texto_opcion" => $texto_opcion,
                            "valor"        => $valor !== "" ? $valor : null
                        ]);
                    }
                }
            }

            $pdo->commit();
            $mensaje = "Encuesta registrada correctamente";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $mensaje = "Error al registrar la encuesta";
        }
    }

    $_SESSION["mensaje_formulario"] = $mensaje;
}

header("Location: Panel_Admin.php");
exit;