<!doctype html>
<html lang="es">
  <head>
    <?php
            session_start();
            $mensaje = $_SESSION["mensaje"] ?? "";
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de administracion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="bg-white m-0 app-layout">
    
    <header class="shadow-sm py-2 px-3">
      <div class="row align-items-center g-2">
        <div class="col-auto">
          <a class="navbar-brand m-0" href="https://hc.edu.uy/">
            <img src="https://hc.edu.uy/images/imagenesarticulos/Logo_Hc.jpg" alt="Hospital de Clinicas" style="max-width: 150px;">
          </a>
        </div>
        <div class="col">
          <form role="search">
            <input class="form-control rounded-pill border-2" type="search" placeholder="Buscar" aria-label="Buscar">
          </form>
        </div>
      </div>
    </header>
    <?php 
    require "conectar.php";
    require "listar_cat_documentos.php"; 
    require "listar_subcategorias.php";
    require "listar_cat_encuestas.php"; 
    require "titulos_documento.php"; 
    require "listar_encuestas.php";
    require "listar_respuestas.php";
    ?>
    <main class="cards-grid">
      <!--CATEGORIA_DOCUMENTO -->
      <div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
        <img src="Logo/Documento.png" alt="Documento" class="mx-auto" style="max-width: 120px;">
        <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="Documentos">Categoria - Documentos</h5>
        <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Nombre</th>
                  <th scope="col">Descripcion</th>
                  <th scope="col">estado</th>
                </tr>
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
                  
            <?php if (!empty($mensaje_cat_doc)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($_SESSION["mensaje_cat_doc"]) ?></div>
                <?php unset($_SESSION["mensaje_cat_doc"]); ?>
            <?php endif; ?>
        
        <form action="registrar_cat_documento.php" method="POST" enctype="multipart/form-data">
          <input class="form-control mb-2" name="nombre_categoria" placeholder="Nombre de Categoria">
          <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

          <label for="imagen-categoria">Selecciona una imagen:</label>
          <input type="file" id="imagen-categoria" class="form-control mb-2" name="logo" accept="image/*" required>

          <label>
              <input type="checkbox" name="privado" value="1">
              Mantener privado
          </label>

          <button class="btn btn-primary">Registrar</button>
      </form>
</div>

<!--SUBCATEGORIA_DOCUMENTO -->
<div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
        <img src="Logo/Documento.png" alt="Documento" class="mx-auto" style="max-width: 120px;">
        <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="Documentos">SubCategoria - Documentos</h5>
        <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Nombre</th>
                  <th scope="col">Descripcion</th>
                  <th scope="col">estado</th>
                  <th scope="col">Categoria de Origen</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($subcategoria as $scat): ?>
                <tr>
                  <th scope="row"><?= htmlspecialchars($scat['id_categoria']) ?></th>
                  <td><?= htmlspecialchars($scat['nombre_subcategoria']) ?></td>
                  <td><?= htmlspecialchars($scat['descripcion']) ?></td>
                  <td><?= $scat['estado_activo'] ? 'true' : 'false' ?></td>
                  <td><?= $scat['nombre_categoria'] ?></td>
                </tr>
                <?php endforeach; ?>
               </tbody>
            </table>   
                  
            <?php if (!empty($mensaje_subcat_doc)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($_SESSION["mensaje_subcat_doc"]) ?></div>
                <?php unset($_SESSION["mensaje_subcat_doc"]); ?>
            <?php endif; ?>
        
       <form action="registrar_subcat_documento.php" method="POST" enctype="multipart/form-data">
    <input class="form-control mb-2" name="nombre_subcategoria" placeholder="Nombre de Subcategoria">
    <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

    <select class="form-select mb-2" name="id_categoria" required>
        <option value="">Seleccione una categoría</option>
        <?php foreach ($categoria as $cat): ?>
            <option value="<?= $cat['id_categoria'] ?>">
                <?= htmlspecialchars($cat['nombre_categoria']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="imagen-subcategoria">Selecciona una imagen:</label>
    <input type="file" id="imagen-subcategoria" class="form-control mb-2" name="logo" accept="image/*" required>

    <label>
        <input type="checkbox" name="privado" value="1">
        Mantener privado
    </label>

    <button class="btn btn-primary">Registrar</button>
</form>
</div>
    <div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
        <img src="Logo/Documento.png" alt="Documento" class="mx-auto" style="max-width: 120px;">
        <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="Documentos">Documentos</h5>



        
                  <!--TABLE CON TITULO DE DOCUEMNTOY ESO  -->
  
  <table class="table table-striped">
            <thead>
                  <tr>
    <th scope="col">ID</th>
    <th scope="col">Nombre</th>
    <th scope="col">Descripcion</th>
    <th scope="col">Categoria</th>
    <th scope="col">estado</th>
  </tr>
</thead>
<tbody>
  <?php foreach ($titulos as $doc): ?>
  <tr>
    <th scope="row"><?= htmlspecialchars($doc['id_documento']) ?></th>
    <td><?= htmlspecialchars($doc['titulo']) ?></td>
    <td><?= htmlspecialchars($doc['descripcion']) ?></td>
    <td>
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
      <?php if (!empty($mensaje_documento)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($_SESSION["mensaje_documento"]) ?></div>
                <?php unset($_SESSION["mensaje_documento"]); ?>
            <?php endif; ?>


            <form action="registrar_documento.php" method="POST" enctype="multipart/form-data">
          <input class="form-control mb-2" name="titulo" placeholder="Título">
          <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">

          <div class="mb-2">
              <label>
                  <input type="radio" name="tipo_destino" value="categoria" onclick="mostrarDestino('categoria')" checked>
                  Asociar a categoría
              </label>
              <label>
                  <input type="radio" name="tipo_destino" value="subcategoria" onclick="mostrarDestino('subcategoria')">
                  Asociar a subcategoría
              </label>
          </div>

          <select class="form-select mb-2" name="id_categoria" id="select-categoria">
              <option value="">Seleccione una categoría</option>
              <?php foreach ($categoria as $cat): ?>
                  <option value="<?= $cat['id_categoria'] ?>">
                      <?= htmlspecialchars($cat['nombre_categoria']) ?>
                  </option>
              <?php endforeach; ?>
          </select>

          <select class="form-select mb-2" name="id_subcategoria" id="select-subcategoria" style="display:none;">
              <option value="">Seleccione una subcategoría</option>
              <?php foreach ($subcategoria as $sc): ?>
                  <option value="<?= $sc['id_subcategoria'] ?>">
                      <?= htmlspecialchars($sc['nombre_subcategoria']) ?> (<?= htmlspecialchars($sc['nombre_categoria']) ?>)
                  </option>
              <?php endforeach; ?>
          </select>

          <label for="archivo-documento">Selecciona el archivo:</label>
          <input type="file" id="archivo-documento" class="form-control mb-2" name="archivo" accept=".pdf,image/*" required>

          <button class="btn btn-primary">Registrar</button>
      </form>

<script>
function mostrarDestino(tipo) {
    document.getElementById('select-categoria').style.display = (tipo === 'categoria') ? 'block' : 'none';
    document.getElementById('select-subcategoria').style.display = (tipo === 'subcategoria') ? 'block' : 'none';
}
</script>
      </div>

      </div>
              <!-- CAT FORMULARIO (TIPO_ENCUESTA) -->
<div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
  <img src="Logo/Formulario.png" alt="Formulario" class="mx-auto" style="max-width: 120px;">
  <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="CategoriaFormulario">Categoria Formulario</h5>

  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Nombre</th>
        <th scope="col">Descripcion</th>
        <th scope="col">estado</th>
      </tr>
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

  <?php if (!empty($mensaje_cat_enc)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($_SESSION["mensaje_cat_enc"]) ?></div>
      <?php unset($_SESSION["mensaje_cat_enc"]); ?>
  <?php endif; ?>

  <form action="registrar_cat_encuesta.php" method="POST">
    <input class="form-control mb-2" name="nombre_tipo" placeholder="Nombre de Tipo de Encuesta">
    <input class="form-control mb-2" name="descripcion" placeholder="Descripcion">
    <label>
        <input type="checkbox" name="privado" value="1">
        Mantener privado
    </label>
    <button class="btn btn-primary">Registrar</button>
  </form>
</div>

<!-- FORMULARIO (ENCUESTA + preguntas) -->
<div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
  <img src="Logo/Formulario.png" alt="Formulario" class="mx-auto" style="max-width: 120px;">
  <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="Formulario">Formulario</h5>

  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Titulo</th>
        <th scope="col">Descripcion</th>
        <th scope="col">Tipo</th>
        <th scope="col">estado</th>
      </tr>
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

  <?php if (!empty($mensaje_formulario)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($_SESSION["mensaje_formulario"]) ?></div>
      <?php unset($_SESSION["mensaje_formulario"]); ?>
  <?php endif; ?>

  <form method="POST" action="registrar_encuesta.php" id="form-encuesta">
    <input class="form-control mb-2" name="titulo" placeholder="titulo" required>
    <input class="form-control mb-2" name="descripcion" placeholder="descripcion" required>

    <select class="form-select mb-2" name="id_tipo_encuesta" required>
      <option value="">Seleccione un tipo de encuesta</option>
      <?php foreach ($tipo_encuestas as $te): ?>
        <option value="<?= (int) $te['id_tipo_encuesta'] ?>">
          <?= htmlspecialchars($te['nombre_tipo']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label class="mb-3 d-block">
      <input type="checkbox" name="privado" value="1">
      Mantener privado
    </label>

    <hr>
    <h6 class="fw-bold">Preguntas</h6>
    <div id="contenedor-preguntas"></div>

    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="btn-agregar-pregunta">
      + Agregar pregunta
    </button>

    <label class="mb-3 d-block">
    <input type="checkbox" name="requiere_cedula" value="1">
    Exigir cédula
  </label>
    <br>
    <button class="btn btn-primary">Registrar</button>
  </form>

  <!-- los <template id="template-pregunta">, <template id="template-opcion"> y el <script> quedan igual, sin cambios -->
</div>

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

<script>
let contadorPregunta = 0;

document.getElementById("btn-agregar-pregunta").addEventListener("click", agregarPregunta);

function agregarPregunta() {
  const idx = contadorPregunta++;
  const tpl = document.getElementById("template-pregunta").content.cloneNode(true);
  const bloque = tpl.querySelector(".bloque-pregunta");
  bloque.dataset.idx = idx;

  const selectTipo = bloque.querySelector('[data-campo="tipo_pregunta"]');
  const contenedorOpciones = bloque.querySelector(".contenedor-opciones");

  selectTipo.addEventListener("change", () => {
    contenedorOpciones.style.display =
      (selectTipo.value === "opcion_multiple" || selectTipo.value === "escala") ? "block" : "none";
  });

  bloque.querySelector(".btn-quitar-pregunta").addEventListener("click", () => bloque.remove());
  bloque.querySelector(".btn-agregar-opcion").addEventListener("click", () => agregarOpcion(bloque));

  document.getElementById("contenedor-preguntas").appendChild(bloque);
}

function agregarOpcion(bloquePregunta) {
  const tpl = document.getElementById("template-opcion").content.cloneNode(true);
  const bloqueOpcion = tpl.querySelector(".bloque-opcion");
  bloqueOpcion.querySelector(".btn-quitar-opcion").addEventListener("click", () => bloqueOpcion.remove());
  bloquePregunta.querySelector(".opciones-lista").appendChild(bloqueOpcion);
}

// Antes de enviar, convierto los bloques dinámicos en inputs hidden con nombres tipo array
document.getElementById("form-encuesta").addEventListener("submit", function (e) {
  const form = e.target;
  document.querySelectorAll(".bloque-pregunta").forEach((bloque, i) => {
    agregarHidden(form, `preguntas[${i}][texto_pregunta]`, bloque.querySelector('[data-campo="texto_pregunta"]').value);
    agregarHidden(form, `preguntas[${i}][tipo_pregunta]`, bloque.querySelector('[data-campo="tipo_pregunta"]').value);
    agregarHidden(form, `preguntas[${i}][obligatoria]`, bloque.querySelector('[data-campo="obligatoria"]').checked ? "1" : "0");
    agregarHidden(form, `preguntas[${i}][orden]`, i + 1);

    bloque.querySelectorAll(".bloque-opcion").forEach((op, j) => {
      agregarHidden(form, `preguntas[${i}][opciones][${j}][texto_opcion]`, op.querySelector('[data-campo="texto_opcion"]').value);
      agregarHidden(form, `preguntas[${i}][opciones][${j}][valor]`, op.querySelector('[data-campo="valor"]').value);
    });
  });
});

function agregarHidden(form, name, value) {
  const input = document.createElement("input");
  input.type = "hidden";
  input.name = name;
  input.value = value;
  form.appendChild(input);
}
</script>

      </div>
      <!-- RESPUESTAS -->
       <div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
        <img src="Logo/Formulario.png" alt="Formulario" class="mx-auto" style="max-width: 120px;">
  <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="Respuestas">Respuestas</h5>
  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Encuesta</th>
        <th scope="col">Fecha</th>
        <th scope="col">Anónima</th>
        <th scope="col">Dispositivo</th>
      </tr>
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

      <!-- administrador -->
       <div class="card border shadow-sm text-center text-decoration-none p-4 h-100 justify-content-center">
        <img src="Logo/Usuario.png" alt="Usuario" class="mx-auto" style="max-width: 120px;">
        <h5 class="card-title fw-bold text-dark fs-4 m-0 pt-3" id="Usuario">Administrador</h5>
             <?php if (!empty($mensaje_usuario)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($_SESSION["mensaje_usuario"]) ?></div>
                <?php unset($_SESSION["mensaje_usuario"]); ?>
            <?php endif; ?>
           <form method="POST" action="registrar_usuario.php">
            <input class="form-control mb-2" name="nombre" placeholder="Nombre">
             <input class="form-control mb-2" name="apellido" placeholder="Apellido">
            <input class="form-control mb-2" name="email" type="email" placeholder="Email">
             <input class="form-control mb-2" name="usuario" placeholder="Usuario">
            <input class="form-control mb-2" name="contrasena" type="password" placeholder="Contraseña">
        <button class="btn btn-primary">Registrar</button>
    </form>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>