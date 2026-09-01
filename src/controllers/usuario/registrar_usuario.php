<?php
session_start();
require "conectar.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = trim($_POST["nombre"] ?? "");
    $apellido       = trim($_POST["apellido"] ?? "");
    $email       = trim($_POST["email"] ?? "");
    $usuario       = trim($_POST["usuario"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

   
    if ($nombre == "") {
        $mensaje = "Falta el nombre";
    } elseif ($apellido == "") {
        $mensaje = "Falta el apellido";
    } elseif ($email == "") {
        $mensaje = "email invalido";
    } elseif ($usuario == "") {
        $mensaje = "Usuario inválido";
    } elseif ($contrasena == "") {
        $mensaje = "Falta la contraseña";
    } else {
        // Todo válido: guardamos
        try {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $sql  = "INSERT INTO ADMINISTRATIVO (nombre, apellido, email, usuario, contrasena, fecha_registro) 
                    VALUES (:nombre, :apellido, :email, :usuario, :contrasena, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "nombre"      => $nombre,
                "apellido"       => $apellido,
                "email"       => $email,
                "usuario"       => $usuario,
                "contrasena" => $hash
                
            ]);
            $mensaje = "Usuario registrado correctamente";
        } catch (PDOException $e) {
            // 23000 = viola una restricción (email repetido)
            if ($e->getCode() == "23000") {
                $mensaje = "Ese email ya está registrado";
            } else {
                $mensaje = "Error al registrar";
            }
        }
    }  $_SESSION["mensaje_usuario"] = $mensaje;
}   header("Location: Panel_Admin.php");
exit;
?>
  