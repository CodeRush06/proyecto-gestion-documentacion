<?php
session_start();
require __DIR__ . "/../src/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$usuario    = trim($_POST["usuario"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";   

if ($usuario === "" || $contrasena === "") {
    $_SESSION["mensaje_usuario"] = "Complete usuario y contraseña";
    header("Location: login.php");
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM ADMINISTRATIVO WHERE usuario = :usuario AND estado_activo = 1");
$stmt->execute(["usuario" => $usuario]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || !password_verify($contrasena, $admin["contrasena"])) {
    $_SESSION["mensaje_usuario"] = "Usuario o contraseña incorrectos";
    header("Location: login.php");
    exit;
}

session_regenerate_id(true);


if ((int) $admin["es_medico"] === 1) {
    $_SESSION["rol"] = "medico";
} else {
    $_SESSION["rol"] = "administrador";
}

$_SESSION["id_funcionario"] = $admin["id_usuario"];
$_SESSION["nombre"]         = $admin["nombre"];

header("Location: Panel_Admin.php");
exit;
