<?php
session_start();
$mensaje_usuario = $_SESSION["mensaje_usuario"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <main class="position-absolute top-0 bottom-0 start-0 end-0 my-auto">
    <div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
        <img src="Logo/Usuario.png" alt="Usuario" class="mx-auto" style="max-width: 120px;">
        <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="Usuario">Administrador</h5>
             <?php if (!empty($mensaje_usuario)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($_SESSION["mensaje_usuario"]) ?></div>
                <?php unset($_SESSION["mensaje_usuario"]); ?>
            <?php endif; ?>
           <form method="POST" action="Verificar_usuario.php">
             <input class="form-control mb-2" name="usuario" placeholder="Usuario">
            <input class="form-control mb-2" name="contrasena" type="password" placeholder="Contraseña">
        <button class="btn btn-primary">Ingresar</button>
    </form>
      </div>
             </main>
</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</html>