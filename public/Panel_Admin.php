<?php
// =====================================================
// 1. LÓGICA (todo el PHP queda arriba, el HTML abajo)
// =====================================================
session_start();
$rol = $_SESSION['rol'] ?? '';
if ($rol != 'administrador' && $rol != 'medico') {
    header("Location: login.php"); // cambiá el nombre si tu login se llama distinto
    exit;
}
if($rol == 'administrador'){
// Conexión y listados que usa la página
require __DIR__ . "/../src/config/database.php";
require "../src/controllers/documento/listar_cat_documentos.php";
require "../src/controllers/encuesta/listar_cat_encuestas.php";
require "../src/controllers/documento/titulos_documento.php";
require "../src/controllers/encuesta/listar_encuestas.php";
require "../src/controllers/encuesta/listar_respuestas.php";
require "../src/controllers/documento/listar_subcategorias.php";
require "../src/controllers/usuario/listar_usuarios.php";

// Mensaje y resultado ("ok" / "error") que dejan los scripts CRUD_*.php
$mensaje_cat_doc      = $_SESSION["mensaje_cat_doc"] ?? "";
$resultado_cat_doc    = $_SESSION["resultado_cat_doc"] ?? "";
$mensaje_subcat_doc   = $_SESSION["mensaje_subcat_doc"] ?? "";
$resultado_subcat_doc = $_SESSION["resultado_subcat_doc"] ?? "";
$mensaje_documento    = $_SESSION["mensaje_documento"] ?? "";
$resultado_documento  = $_SESSION["resultado_documento"] ?? "";
$mensaje_cat_enc      = $_SESSION["mensaje_cat_enc"] ?? "";
$resultado_cat_enc    = $_SESSION["resultado_cat_enc"] ?? "";
$mensaje_formulario   = $_SESSION["mensaje_formulario"] ?? "";
$resultado_formulario = $_SESSION["resultado_formulario"] ?? "";
$mensaje_usuario      = $_SESSION["mensaje_usuario"] ?? "";
$resultado_usuario    = $_SESSION["resultado_usuario"] ?? "";
}else if($rol == 'medico'){
require __DIR__ . "/../src/config/database.php";
require "../src/controllers/documento/listar_cat_documentos.php";
require "../src/controllers/documento/titulos_documento.php";
require "../src/controllers/documento/listar_subcategorias.php";
$mensaje_documento    = $_SESSION["mensaje_documento"] ?? "";
$resultado_documento  = $_SESSION["resultado_documento"] ?? "";
}
// Se borran de la sesión para que no reaparezcan al recargar
foreach (["cat_doc", "subcat_doc", "documento", "cat_enc", "formulario", "usuario"] as $k) {
    unset($_SESSION["mensaje_$k"], $_SESSION["accion_$k"], $_SESSION["resultado_$k"]);
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administracion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="bg-white m-0 app-layout">

    <!-- ================= HEADER ================= -->
    <header class="shadow-sm py-2 px-3">
      <div class="row align-items-center g-2">
        <div class="col-auto">
          <a class="navbar-brand m-0" href="https://hc.edu.uy/">
            <img src="Logo/HDC.png" alt="Hospital de Clinicas" style="max-width: 150px;">
          </a>
        </div>
        <div class="col">
          <form role="search">
            <input class="form-control rounded-pill border-2" type="search" placeholder="Buscar" aria-label="Buscar">
          </form>
        </div>
      </div>
    </header>

    <main class="cards-grid">
      <div class="row justify-content-center">
              <?php if ($rol == 'administrador'): ?>
        <!-- ===================================================== -->
        <!-- TARJETA 1: CATEGORIA - DOCUMENTOS                     -->
        <!-- Script: CRUD_cat_documento.php                        -->
        <!-- Cada panel se abre con un botón; el id del botón      -->
        <!-- (data-bs-target) debe coincidir con el id del panel   -->
        <!-- ===================================================== -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
            <img src="Logo/Documento.png" alt="Documento" class="mx-auto" style="max-width: 120px;">
            <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Categoria - Documentos</h5>

            <!-- Mensaje de resultado: verde si salió bien, rojo si hubo error -->
            <?php if ($mensaje_cat_doc != ""): ?>
              <div class="alert <?= $resultado_cat_doc == 'ok' ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($mensaje_cat_doc) ?>
              </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
              <button class="btn btn-info text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catdoc-nuevo">Registrar</button>
              <button class="btn btn-warning text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catdoc-modificar">Modificar</button>
              <button class="btn btn-danger text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catdoc-borrar">Borrar</button>
              <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catdoc-lista">Ver Lista</button>
            </div>

            <!-- Panel: Registrar (la imagen solo se sube acá) -->
            <div class="collapse mb-4 text-start" id="catdoc-nuevo">
              <form action="CRUD_cat_documento.php" method="POST" enctype="multipart/form-data"  onsubmit="return validarCatDocumento(this)">
                <input type="hidden" name="accion" value="crear">
                <input class="form-control mb-2" name="nombre_categoria" placeholder="Nombre de Categoria">
                <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

                <label for="imagen-categoria" class="form-label">Selecciona una imagen:</label>
                <input type="file" id="imagen-categoria" class="form-control mb-2" name="logo" accept="image/*">

                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="privadoCatDoc" name="privado" value="1">
                  <label class="form-check-label" for="privadoCatDoc">Mantener privado</label>
                </div>

                <button class="btn btn-primary w-100">Registrar</button>
              </form>
            </div>

            <!-- Panel: Modificar (solo datos) -->
            <div class="collapse mb-4 text-start" id="catdoc-modificar">
              <form action="CRUD_cat_documento.php" method="POST"  onsubmit="return validarCatDocumento(this)">
                <input type="hidden" name="accion" value="modificar">

                <!-- Cuál modificar (solo las activas) -->
                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione una categoría</option>
                  <?php foreach ($categoria as $cat): ?>
                    <?php if ($cat['estado_activo']): ?>
                      <option value="<?= (int) $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <input class="form-control mb-2" name="nombre_categoria" placeholder="Nuevo nombre" required>
                <input class="form-control mb-2" name="descripcion" placeholder="Nueva descripcion" required>

                <button class="btn btn-warning text-white w-100">Modificar</button>
              </form>
            </div>

            <!-- Panel: Borrar (baja lógica) -->
            <div class="collapse mb-4 text-start" id="catdoc-borrar">
              <form action="CRUD_cat_documento.php" method="POST" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                <input type="hidden" name="accion" value="borrar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione una categoría</option>
                  <?php foreach ($categoria as $cat): ?>
                    <?php if ($cat['estado_activo']): ?>
                      <option value="<?= (int) $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-danger w-100">Borrar</button>
              </form>
            </div>

            <!-- Panel: Ver lista -->
            <div class="collapse mb-4" id="catdoc-lista">
              <div class="table-responsive">
                <table class="table table-striped text-start">
                  <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Descripcion</th><th>Estado</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($categoria as $cat): ?>
                    <tr>
                      <th scope="row"><?= htmlspecialchars($cat['id_categoria']) ?></th>
                      <td><?= htmlspecialchars($cat['nombre_categoria']) ?></td>
                      <td><?= htmlspecialchars($cat['descripcion']) ?></td>
                      <td><?= $cat['estado_activo'] ? 'true' : 'false' ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <!-- ===================================================== -->
        <!-- TARJETA 2: SUBCATEGORIA - DOCUMENTOS                  -->
        <!-- Script: CRUD_subcat_documento.php                     -->
        <!-- ===================================================== -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
            <img src="Logo/Documento.png" alt="Documento" class="mx-auto" style="max-width: 120px;">
            <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Subcategoria - Documentos</h5>

            <?php if ($mensaje_subcat_doc != ""): ?>
              <div class="alert <?= $resultado_subcat_doc == 'ok' ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($mensaje_subcat_doc) ?>
              </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
              <button class="btn btn-info text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#subcat-nuevo">Registrar</button>
              <button class="btn btn-warning text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#subcat-modificar">Modificar</button>
              <button class="btn btn-danger text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#subcat-borrar">Borrar</button>
              <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#subcat-lista">Ver Lista</button>
            </div>

            <!-- Panel: Registrar -->
            <div class="collapse mb-4 text-start" id="subcat-nuevo">
                <form action="CRUD_subcat_documento.php" method="POST" enctype="multipart/form-data" onsubmit="return validarSubcategoria(this)">
                <input type="hidden" name="accion" value="crear">
                <input class="form-control mb-2" name="nombre_subcategoria" placeholder="Nombre de Subcategoria">
                <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

                <!-- Categoría a la que pertenece -->
                <select class="form-select mb-2" name="id_categoria" required>
                  <option value="">Seleccione una categoría</option>
                  <?php foreach ($categoria as $cat): ?>
                    <option value="<?= (int) $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                  <?php endforeach; ?>
                </select>

                <label for="imagen-subcategoria" class="form-label">Selecciona una imagen:</label>
                <input type="file" id="imagen-subcategoria" class="form-control mb-2" name="logo" accept="image/*" required>

                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="privadoSubcat" name="privado" value="1">
                  <label class="form-check-label" for="privadoSubcat">Mantener privado</label>
                </div>

                <button class="btn btn-primary w-100">Registrar</button>
              </form>
            </div>

            <!-- Panel: Modificar (solo datos) -->
            <div class="collapse mb-4 text-start" id="subcat-modificar">
              <form action="CRUD_subcat_documento.php" method="POST" onsubmit="return validarSubcategoria(this)">
                <input type="hidden" name="accion" value="modificar">

                <!-- Cuál modificar (solo las activas) -->
                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione una subcategoría</option>
                  <?php foreach ($subcategoria as $sc): ?>
                    <?php if ($sc['estado_activo']): ?>
                      <option value="<?= (int) $sc['id_subcategoria'] ?>"><?= htmlspecialchars($sc['nombre_subcategoria']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <!-- Datos nuevos -->
                <input class="form-control mb-2" name="nombre_subcategoria" placeholder="Nuevo nombre" required>
                <input class="form-control mb-2" name="descripcion" placeholder="Nueva descripcion" required>

                <select class="form-select mb-2" name="id_categoria" required>
                  <option value="">Seleccione una categoría</option>
                  <?php foreach ($categoria as $cat): ?>
                    <?php if ($cat['estado_activo']): ?>
                      <option value="<?= (int) $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-warning text-white w-100">Modificar</button>
              </form>
            </div>

            <!-- Panel: Borrar (baja lógica: estado_activo = 0) -->
            <div class="collapse mb-4 text-start" id="subcat-borrar">
              <form action="CRUD_subcat_documento.php" method="POST" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                <input type="hidden" name="accion" value="borrar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione una subcategoría</option>
                  <?php foreach ($subcategoria as $sc): ?>
                    <?php if ($sc['estado_activo']): ?>
                      <option value="<?= (int) $sc['id_subcategoria'] ?>"><?= htmlspecialchars($sc['nombre_subcategoria']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-danger w-100">Borrar</button>
              </form>
            </div>

            <!-- Panel: Ver lista -->
            <div class="collapse mb-4" id="subcat-lista">
              <div class="table-responsive">
                <table class="table table-striped text-start">
                  <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Descripcion</th><th>Estado</th><th>Categoria de Origen</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($subcategoria as $scat): ?>
                    <tr>
                      <th scope="row"><?= htmlspecialchars($scat['id_subcategoria']) ?></th>
                      <td><?= htmlspecialchars($scat['nombre_subcategoria']) ?></td>
                      <td><?= htmlspecialchars($scat['descripcion']) ?></td>
                      <td><?= $scat['estado_activo'] ? 'true' : 'false' ?></td>
                      <td><?= htmlspecialchars($scat['nombre_categoria']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <!-- ===================================================== -->
        <!-- TARJETA 3: DOCUMENTOS                                 -->
        <!-- Script: CRUD_documento.php                            -->
        <!-- ===================================================== -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
            <img src="Logo/Documento.png" alt="Documento" class="mx-auto" style="max-width: 120px;">
            <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Documentos</h5>

            <?php if ($mensaje_documento != ""): ?>
              <div class="alert <?= $resultado_documento == 'ok' ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($mensaje_documento) ?>
              </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
              <button class="btn btn-info text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#doc-nuevo">Registrar</button>
              <button class="btn btn-warning text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#doc-modificar">Modificar</button>
              <button class="btn btn-danger text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#doc-borrar">Borrar</button>
              <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#doc-lista">Ver Lista</button>
            </div>

            <!-- Panel: Registrar -->
            <div class="collapse mb-4 text-start" id="doc-nuevo">
              <form action="CRUD_documento.php" method="POST" enctype="multipart/form-data" onsubmit="return validarDocumento(this)">
                <input type="hidden" name="accion" value="crear">
                <input class="form-control mb-2" name="titulo" placeholder="Título">
                <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

                <!-- El documento va asociado a una categoría O a una subcategoría -->
                <div class="mb-2">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_destino" id="destino-cat" value="categoria" onclick="mostrarDestino('categoria')" checked>
                    <label class="form-check-label" for="destino-cat">Asociar a categoría</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_destino" id="destino-subcat" value="subcategoria" onclick="mostrarDestino('subcategoria')">
                    <label class="form-check-label" for="destino-subcat">Asociar a subcategoría</label>
                  </div>
                </div>

                <select class="form-select mb-2" name="id_categoria" id="select-categoria">
                  <option value="">Seleccione una categoría</option>
                  <?php foreach ($categoria as $cat): ?>
                    <option value="<?= (int) $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                  <?php endforeach; ?>
                </select>

                <!-- Oculto hasta que se elige "Asociar a subcategoría" -->
                <select class="form-select mb-2" name="id_subcategoria" id="select-subcategoria" style="display:none;">
                  <option value="">Seleccione una subcategoría</option>
                  <?php foreach ($subcategoria as $sc): ?>
                    <option value="<?= (int) $sc['id_subcategoria'] ?>">
                      <?= htmlspecialchars($sc['nombre_subcategoria']) ?> (<?= htmlspecialchars($sc['nombre_categoria']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>

                <label for="archivo-documento" class="form-label">Selecciona el archivo:</label>
                <input type="file" id="archivo-documento" class="form-control mb-2" name="archivo" accept=".pdf,image/*" required>

                <button class="btn btn-primary w-100">Registrar</button>
              </form>
            </div>

            <!-- Panel: Modificar (solo título y descripción) -->
            <div class="collapse mb-4 text-start" id="doc-modificar">
              <form action="CRUD_documento.php" method="POST" onsubmit="return validarDocumento(this)">

                <input type="hidden" name="accion" value="modificar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione un documento</option>
                  <?php foreach ($titulos as $doc): ?>
                    <?php if ($doc['estado']): ?>
                      <option value="<?= (int) $doc['id_documento'] ?>"><?= htmlspecialchars($doc['titulo']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <input class="form-control mb-2" name="titulo" placeholder="Nuevo titulo" required>
                <input class="form-control mb-2" name="descripcion" placeholder="Nueva descripcion" required>

                <button class="btn btn-warning text-white w-100">Modificar</button>
              </form>
            </div>

            <!-- Panel: Borrar (baja lógica) -->
            <div class="collapse mb-4 text-start" id="doc-borrar">
              <form action="CRUD_documento.php" method="POST" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                <input type="hidden" name="accion" value="borrar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione un documento</option>
                  <?php foreach ($titulos as $doc): ?>
                    <?php if ($doc['estado']): ?>
                      <option value="<?= (int) $doc['id_documento'] ?>"><?= htmlspecialchars($doc['titulo']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-danger w-100">Borrar</button>
              </form>
            </div>

            <!-- Panel: Ver lista -->
            <div class="collapse mb-4" id="doc-lista">
              <div class="table-responsive">
                <table class="table table-striped text-start">
                  <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Descripcion</th><th>Categoria</th><th>Estado</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($titulos as $doc): ?>
                    <tr>
                      <th scope="row"><?= htmlspecialchars($doc['id_documento']) ?></th>
                      <td><?= htmlspecialchars($doc['titulo']) ?></td>
                      <td><?= htmlspecialchars($doc['descripcion']) ?></td>
                      <td>
                        <!-- Muestra la categoría directa, o la subcategoría con su categoría -->
                        <?php if ($doc['categoria_directa']): ?>
                          <?= htmlspecialchars($doc['categoria_directa']) ?>
                        <?php elseif ($doc['nombre_subcategoria']): ?>
                          <?= htmlspecialchars($doc['nombre_subcategoria']) ?> (<?= htmlspecialchars($doc['categoria_de_subcategoria']) ?>)
                        <?php else: ?>
                          <span class="text-muted">Sin categoría</span>
                        <?php endif; ?>
                      </td>
                      <td><?= $doc['estado'] ? 'true' : 'false' ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <!-- ===================================================== -->
        <!-- TARJETA 4: CATEGORIA - FORMULARIO (TIPO_ENCUESTA)     -->
        <!-- Script: CRUD_cat_encuesta.php (el campo "accion" le   -->
        <!-- dice si crea, modifica o borra)                       -->
        <!-- ===================================================== -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
            <img src="Logo/Formulario.png" alt="Formulario" class="mx-auto" style="max-width: 120px;">
            <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Categoria - Formulario</h5>

            <?php if ($mensaje_cat_enc != ""): ?>
              <div class="alert <?= $resultado_cat_enc == 'ok' ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($mensaje_cat_enc) ?>
              </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
              <button class="btn btn-info text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catenc-nuevo">Registrar</button>
              <button class="btn btn-warning text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catenc-modificar">Modificar</button>
              <button class="btn btn-danger text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catenc-borrar">Borrar</button>
              <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#catenc-lista">Ver Lista</button>
            </div>

            <!-- Panel: Registrar -->
            <div class="collapse mb-4 text-start" id="catenc-nuevo">
              <form action="CRUD_cat_encuesta.php" method="POST" onsubmit="return validarCatEncuesta(this)">
                <input type="hidden" name="accion" value="crear">
                <input class="form-control mb-2" name="nombre_tipo" placeholder="Nombre de Tipo de Encuesta">
                <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="privadoCatEnc" name="privado" value="1">
                  <label class="form-check-label" for="privadoCatEnc">Mantener privado</label>
                </div>

                <button class="btn btn-primary w-100">Registrar</button>
              </form>
            </div>

            <!-- Panel: Modificar -->
            <div class="collapse mb-4 text-start" id="catenc-modificar">
              <form action="CRUD_cat_encuesta.php" method="POST" onsubmit="return validarCatEncuesta(this)">
                <input type="hidden" name="accion" value="modificar">

                <!-- Cuál modificar (solo las activas) -->
                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione una categoría</option>
                  <?php foreach ($tipo_encuestas as $te): ?>
                    <?php if ($te['estado_activo']): ?>
                      <option value="<?= (int) $te['id_tipo_encuesta'] ?>"><?= htmlspecialchars($te['nombre_tipo']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <input class="form-control mb-2" name="nombre_tipo" placeholder="Nuevo nombre" required>
                <input class="form-control mb-2" name="descripcion" placeholder="Nueva descripcion" required>

                <button class="btn btn-warning text-white w-100">Modificar</button>
              </form>
            </div>

            <!-- Panel: Borrar (baja lógica) -->
            <div class="collapse mb-4 text-start" id="catenc-borrar">
              <form action="CRUD_cat_encuesta.php" method="POST" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                <input type="hidden" name="accion" value="borrar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione una categoría</option>
                  <?php foreach ($tipo_encuestas as $te): ?>
                    <?php if ($te['estado_activo']): ?>
                      <option value="<?= (int) $te['id_tipo_encuesta'] ?>"><?= htmlspecialchars($te['nombre_tipo']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-danger w-100">Borrar</button>
              </form>
            </div>

            <!-- Panel: Ver lista -->
            <div class="collapse mb-4" id="catenc-lista">
              <div class="table-responsive">
                <table class="table table-striped text-start">
                  <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Descripcion</th><th>Estado</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($tipo_encuestas as $enc): ?>
                    <tr>
                      <th scope="row"><?= htmlspecialchars($enc['id_tipo_encuesta']) ?></th>
                      <td><?= htmlspecialchars($enc['nombre_tipo']) ?></td>
                      <td><?= htmlspecialchars($enc['descripcion']) ?></td>
                      <td><?= $enc['estado_activo'] ? 'true' : 'false' ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <!-- ===================================================== -->
        <!-- TARJETA 5: FORMULARIO (ENCUESTA + PREGUNTAS)          -->
        <!-- Registrar: registrar_encuesta.php (lleva preguntas)   -->
        <!-- Modificar y Borrar: CRUD_encuesta.php                 -->
        <!-- ===================================================== -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
            <img src="Logo/Formulario.png" alt="Formulario" class="mx-auto" style="max-width: 120px;">
            <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Formulario</h5>

            <?php if ($mensaje_formulario != ""): ?>
              <div class="alert <?= $resultado_formulario == 'ok' ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($mensaje_formulario) ?>
              </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
              <button class="btn btn-info text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#enc-nuevo">Registrar</button>
              <button class="btn btn-warning text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#enc-modificar">Modificar</button>
              <button class="btn btn-danger text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#enc-borrar">Borrar</button>
              <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#enc-lista">Ver Lista</button>
            </div>

            <!-- Panel: Registrar (el JS arma las preguntas antes de enviar) -->
            <div class="collapse mb-4 text-start" id="enc-nuevo">
              <form method="POST" action="CRUD_encuesta.php" id="form-encuesta" onsubmit="return validarEncuesta(this)">
                <input class="form-control mb-2" name="titulo" placeholder="Titulo" required>
                <input class="form-control mb-2" name="descripcion" placeholder="Descripcion" required>

                <select class="form-select mb-2" name="id_tipo_encuesta" required>
                  <option value="">Seleccione un tipo de encuesta</option>
                  <?php foreach ($tipo_encuestas as $te): ?>
                    <option value="<?= (int) $te['id_tipo_encuesta'] ?>"><?= htmlspecialchars($te['nombre_tipo']) ?></option>
                  <?php endforeach; ?>
                </select>

                <div class="form-check mb-2">
                  <input type="checkbox" class="form-check-input" id="privadoEnc" name="privado" value="1">
                  <label class="form-check-label" for="privadoEnc">Mantener privado</label>
                </div>

                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="requiereCedula" name="requiere_cedula" value="1">
                  <label class="form-check-label" for="requiereCedula">Exigir cédula</label>
                </div>

                <hr>
                <h6 class="fw-bold">Preguntas</h6>

                <!-- Acá el JS agrega las preguntas -->
                <div id="contenedor-preguntas"></div>

                <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="btn-agregar-pregunta">
                  + Agregar pregunta
                </button>

                <button class="btn btn-primary w-100">Registrar</button>
              </form>
            </div>

            <!-- Panel: Modificar -->
            <div class="collapse mb-4 text-start" id="enc-modificar">
              <form action="CRUD_encuesta.php" method="POST" onsubmit="return validarEncuesta(this)">
>
                <input type="hidden" name="accion" value="modificar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione un formulario</option>
                  <?php foreach ($encuestas as $f): ?>
                    <?php if ($f['estado']): ?>
                      <option value="<?= (int) $f['id_encuesta'] ?>"><?= htmlspecialchars($f['titulo']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <input class="form-control mb-2" name="titulo" placeholder="Nuevo titulo" required>
                <input class="form-control mb-2" name="descripcion" placeholder="Nueva descripcion" required>

                <select class="form-select mb-2" name="id_tipo_encuesta" required>
                  <option value="">Seleccione un tipo de encuesta</option>
                  <?php foreach ($tipo_encuestas as $te): ?>
                    <?php if ($te['estado_activo']): ?>
                      <option value="<?= (int) $te['id_tipo_encuesta'] ?>"><?= htmlspecialchars($te['nombre_tipo']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-warning text-white w-100">Modificar</button>
              </form>
            </div>

            <!-- Panel: Borrar (baja lógica) -->
            <div class="collapse mb-4 text-start" id="enc-borrar">
              <form action="CRUD_encuesta.php" method="POST" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                <input type="hidden" name="accion" value="borrar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione un formulario</option>
                  <?php foreach ($encuestas as $f): ?>
                    <?php if ($f['estado']): ?>
                      <option value="<?= (int) $f['id_encuesta'] ?>"><?= htmlspecialchars($f['titulo']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-danger w-100">Borrar</button>
              </form>
            </div>

            <!-- Panel: Ver lista -->
            <div class="collapse mb-4" id="enc-lista">
              <div class="table-responsive">
                <table class="table table-striped text-start">
                  <thead>
                    <tr><th>ID</th><th>Titulo</th><th>Descripcion</th><th>Tipo</th><th>Estado</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($encuestas as $f): ?>
                    <tr>
                      <th scope="row"><?= htmlspecialchars($f['id_encuesta']) ?></th>
                      <td><?= htmlspecialchars($f['titulo']) ?></td>
                      <td><?= htmlspecialchars($f['descripcion']) ?></td>
                      <td><?= htmlspecialchars($f['id_tipo_encuesta']) ?></td>
                      <td><?= $f['estado'] ? 'true' : 'false' ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <!-- ===================================================== -->
        <!-- TARJETA 6: RESPUESTAS        -->
        <!-- ===================================================== -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
            <img src="Logo/Formulario.png" alt="Formulario" class="mx-auto" style="max-width: 120px;">
            <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Respuestas</h5>

            <!-- Solo botón de lista -->
            <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
              <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#resp-lista">Ver Lista</button>
            </div>

            <!-- Panel: Ver lista -->
            <div class="collapse mb-4" id="resp-lista">
              <div class="table-responsive">
                <table class="table table-striped text-start">
                  <thead>
                    <tr><th>ID</th><th>Encuesta</th><th>Fecha</th><th>Anónima</th><th>Dispositivo</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($respuestas as $r): ?>
                    <tr>
                      <th scope="row"><?= htmlspecialchars($r['id_respuesta']) ?></th>
                      <td><?= htmlspecialchars($r['titulo']) ?></td>
                      <td><?= htmlspecialchars($r['fecha_respuesta']) ?></td>
                      <td><?= $r['anonima'] ? 'Sí' : 'No' ?></td>
                      <td><?= $r['dispositivo'] ? htmlspecialchars($r['dispositivo']) : '<span class="text-muted">Desconocido</span>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <!-- ===================================================== -->
        <!-- TARJETA 7: ADMINISTRADOR                              -->
        <!-- Script: CRUD_usuario.php (tabla administrativo)       -->
        <!-- ===================================================== -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
            <img src="Logo/Usuario.png" alt="Usuario" class="mx-auto" style="max-width: 120px;">
            <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Administrador</h5>

            <?php if ($mensaje_usuario != ""): ?>
              <div class="alert <?= $resultado_usuario == 'ok' ? 'alert-success' : 'alert-danger' ?> mt-3">
                <?= htmlspecialchars($mensaje_usuario) ?>
              </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
              <button class="btn btn-info text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#adm-nuevo">Registrar</button>
              <button class="btn btn-warning text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#adm-modificar">Modificar</button>
              <button class="btn btn-danger text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#adm-borrar">Borrar</button>
              <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#adm-lista">Ver Lista</button>
            </div>

            <!-- Panel: Registrar -->
            <div class="collapse mb-4 text-start" id="adm-nuevo">
              <form method="POST" action="CRUD_usuario.php" onsubmit="return validarUsuario(this)">
                <input type="hidden" name="accion" value="crear">
                <input class="form-control mb-2" name="nombre" placeholder="Nombre" required>
                <input class="form-control mb-2" name="apellido" placeholder="Apellido" required>
                <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
                <input class="form-control mb-2" name="usuario" placeholder="Usuario" required>
                <input class="form-control mb-2" name="contrasena" type="password" placeholder="Contraseña (mínimo 8)" required>              
              <select class="form-select mb-2" name="es_medico" required>
                <option value="">Seleccione rol</option>
                <option value="1">Medico</option>
                <option value="0">Administrador</option>
              </select>
                <button class="btn btn-primary w-100">Registrar</button>
              </form>
            </div>

            <!-- Panel: Modificar -->
            <div class="collapse mb-4 text-start" id="adm-modificar">
              <form method="POST" action="CRUD_usuario.php" onsubmit="return validarUsuario(this)">
                <input type="hidden" name="accion" value="modificar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione un administrador</option>
                  <?php foreach ($usuarios as $u): ?>
                    <?php if ($u['estado_activo']): ?>
                      <option value="<?= (int) $u['id_funcionario'] ?>"><?= htmlspecialchars($u['usuario']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <input class="form-control mb-2" name="nombre" placeholder="Nuevo nombre" required>
                <input class="form-control mb-2" name="apellido" placeholder="Nuevo apellido" required>
                <input class="form-control mb-2" name="email" type="email" placeholder="Nuevo email" required>
                <input class="form-control mb-2" name="usuario" placeholder="Nuevo usuario" required>

                <button class="btn btn-warning text-white w-100">Modificar</button>
              </form>
            </div>

            <!-- Panel: Borrar (baja lógica) -->
            <div class="collapse mb-4 text-start" id="adm-borrar">
              <form method="POST" action="CRUD_usuario.php" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                <input type="hidden" name="accion" value="borrar">

                <select class="form-select mb-2" name="id" required>
                  <option value="">Seleccione un administrador</option>
                  <?php foreach ($user as $u): ?>
                    <?php if ($u['estado_activo']): ?>
                      <option value="<?= (int) $u['id_funcionario'] ?>"><?= htmlspecialchars($u['usuario']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <button class="btn btn-danger w-100">Borrar</button>
              </form>
            </div>

            <!-- Panel: Ver lista -->
            <div class="collapse mb-4" id="adm-lista">
              <div class="table-responsive">
                <table class="table table-striped text-start">
                  <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Usuario</th><th>Estado</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($user as $u): ?>
                    <tr>
                      <th scope="row"><?= htmlspecialchars($u['id_funcionario']) ?></th>
                      <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></td>
                      <td><?= htmlspecialchars($u['email']) ?></td>
                      <td><?= htmlspecialchars($u['usuario']) ?></td>
                      <td><?= $u['estado_activo'] ? 'true' : 'false' ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

      </div><!-- /row -->
 
    

    <!-- ===================================================== -->
    <!-- PLANTILLAS: moldes HTML que el JS copia para armar     -->
    <!-- preguntas y opciones del formulario (no se ven)        -->
    <!-- ===================================================== -->
    <template id="template-pregunta">
      <div class="card mb-3 p-3 bloque-pregunta">
        <button type="button" class="btn-close float-end btn-quitar-pregunta"></button>
        <input class="form-control mb-2" data-campo="texto_pregunta" placeholder="Texto de la pregunta" required>

        <select class="form-select mb-2" data-campo="tipo_pregunta">
          <option value="texto">Texto libre</option>
          <option value="opcion_multiple">Opción múltiple</option>
          <option value="escala">Escala</option>
        </select>

        <label class="mb-2">
          <input type="checkbox" data-campo="obligatoria" value="1">
          Obligatoria
        </label>

        <!-- Solo se ve si el tipo es opción múltiple o escala -->
        <div class="contenedor-opciones" style="display:none;">
          <div class="opciones-lista mb-2"></div>
          <button type="button" class="btn btn-outline-secondary btn-sm btn-agregar-opcion">+ Agregar opción</button>
        </div>
      </div>
    </template>

    <template id="template-opcion">
      <div class="input-group input-group-sm mb-1 bloque-opcion">
        <input type="text" class="form-control" data-campo="texto_opcion" placeholder="Texto de la opción">
        <input type="text" class="form-control" data-campo="valor" placeholder="Valor (opcional)" style="max-width: 100px;">
        <button type="button" class="btn btn-outline-danger btn-quitar-opcion">×</button>
      </div>
    </template>

    
                      
  <?php elseif ($rol == 'medico'): ?>

    <!-- ===================================================== -->
    <!-- PANEL MÉDICO: solo crear y listar documentos          -->
    <!-- Script: CRUD_documento.php                            -->
    <!-- ===================================================== -->
    <div class="col-lg-4 col-md-6 col-sm-12">
      <div class="card border shadow-sm text-center text-decoration-none p-4 m-4 h-100 justify-content-center">
        <img src="Logo/Documento.png" alt="Documento" class="mx-auto" style="max-width: 120px;">
        <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3">Documentos</h5>

        <?php if ($mensaje_documento != ""): ?>
          <div class="alert <?= $resultado_documento == 'ok' ? 'alert-success' : 'alert-danger' ?> mt-3">
            <?= htmlspecialchars($mensaje_documento) ?>
          </div>
        <?php endif; ?>

        <!-- Botones: solo Registrar y Ver Lista -->
        <div class="d-flex justify-content-center flex-wrap gap-2 shadow-sm p-3 bg-white rounded-4 border mx-auto my-4 w-100">
          <button class="btn btn-info text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#doc-nuevo">Registrar</button>
          <button class="btn btn-secondary text-white fs-5 px-4 shadow-sm flex-fill" type="button" data-bs-toggle="collapse" data-bs-target="#doc-lista">Ver Lista</button>
        </div>

        <!-- Panel: Registrar -->
        <div class="collapse mb-4 text-start" id="doc-nuevo">
          <form action="CRUD_documento.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="crear">
            <input class="form-control mb-2" name="titulo" placeholder="Título">
            <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

            <!-- El documento va asociado a una categoría O a una subcategoría -->
            <div class="mb-2">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_destino" id="destino-cat" value="categoria" onclick="mostrarDestino('categoria')" checked>
                <label class="form-check-label" for="destino-cat">Asociar a categoría</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_destino" id="destino-subcat" value="subcategoria" onclick="mostrarDestino('subcategoria')">
                <label class="form-check-label" for="destino-subcat">Asociar a subcategoría</label>
              </div>
            </div>

            <select class="form-select mb-2" name="id_categoria" id="select-categoria">
              <option value="">Seleccione una categoría</option>
              <?php foreach ($categoria as $cat): ?>
                <option value="<?= (int) $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
              <?php endforeach; ?>
            </select>

            <!-- Oculto hasta que se elige "Asociar a subcategoría" -->
            <select class="form-select mb-2" name="id_subcategoria" id="select-subcategoria" style="display:none;">
              <option value="">Seleccione una subcategoría</option>
              <?php foreach ($subcategoria as $sc): ?>
                <option value="<?= (int) $sc['id_subcategoria'] ?>">
                  <?= htmlspecialchars($sc['nombre_subcategoria']) ?> (<?= htmlspecialchars($sc['nombre_categoria']) ?>)
                </option>
              <?php endforeach; ?>
            </select>

            <label for="archivo-documento" class="form-label">Selecciona el archivo:</label>
            <input type="file" id="archivo-documento" class="form-control mb-2" name="archivo" accept=".pdf,image/*" required>

            <button class="btn btn-primary w-100">Registrar</button>
          </form>
        </div>

        <!-- Panel: Ver lista -->
        <div class="collapse mb-4" id="doc-lista">
          <div class="table-responsive">
            <table class="table table-striped text-start">
              <thead>
                <tr><th>ID</th><th>Nombre</th><th>Descripcion</th><th>Categoria</th><th>Estado</th></tr>
              </thead>
              <tbody>
                <?php foreach ($titulos as $doc): ?>
                <tr>
                  <th scope="row"><?= htmlspecialchars($doc['id_documento']) ?></th>
                  <td><?= htmlspecialchars($doc['titulo']) ?></td>
                  <td><?= htmlspecialchars($doc['descripcion']) ?></td>
                  <td>
                    <!-- Muestra la categoría directa, o la subcategoría con su categoría -->
                    <?php if ($doc['categoria_directa']): ?>
                      <?= htmlspecialchars($doc['categoria_directa']) ?>
                    <?php elseif ($doc['nombre_subcategoria']): ?>
                      <?= htmlspecialchars($doc['nombre_subcategoria']) ?> (<?= htmlspecialchars($doc['categoria_de_subcategoria']) ?>)
                    <?php else: ?>
                      <span class="text-muted">Sin categoría</span>
                    <?php endif; ?>
                  </td>
                  <td><?= $doc['estado'] ? 'true' : 'false' ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

  <?php endif; ?>

</div><!-- /row -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
   <!-- Bootstrap (necesario para que abran los paneles) -->
 
    <script src="js/main.js"></script>
      </main>
  </body>
</html>
