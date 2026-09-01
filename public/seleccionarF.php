<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seleccionar Especialidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <?php require "listar_cat_encuestas.php"; ?>
    <style>
      body, html { height: 100%; margin: 0; background: #fff; overflow-x: hidden; }
      .slide-button {
        border: none; background: transparent !important; width: 100%; height: 100%; padding: 0;
        outline: none !important; box-shadow: none !important; transform: none !important; transition: none !important;
        -webkit-tap-highlight-color: transparent;
      }
    </style>
  </head>
  <body class="min-vh-100 position-relative">

    <header class="shadow-sm py-2 px-3">
      <div class="row align-items-center g-2">
        <div class="col-auto">
          <a class="navbar-brand m-0" href="#">
            <img src="https://hc.edu.uy/images/imagenesarticulos/Logo_Hc.jpg" style="max-width: 150px; height: auto;" alt="Hospital de Clinicas">
          </a>
        </div>
        <div class="col">
          <form role="search">
            <input class="form-control rounded-pill text-muted" type="search" placeholder="Buscar" aria-label="Buscar" style="border: 2px solid #ccc;"/>
          </form>
        </div>
      </div>
    </header>
    <main class="position-absolute top-0 bottom-0 start-0 end-0 my-auto" style="height: fit-content;">
     <div id="carouselExample" class="carousel slide w-100">
  <div class="carousel-inner text-center">
    <?php $primero = true; foreach ($tipo_encuestas as $enc): ?>
    <div class="carousel-item <?= $primero ? 'active' : '' ?>">
      <button class="slide-button" onclick="location.href='ver_encuesta.php?id=<?= (int) $enc['id_tipo_encuesta'] ?>'">
        <div class="card border-0 bg-transparent mx-auto" style="max-width: 18rem;">
          <img src="Logo/Formulario.png" alt="<?= htmlspecialchars($enc['nombre_tipo']) ?>" class="mx-auto" style="max-width: 150px; height: auto;">
          <div class="card-body pt-2">
            <h5 class="card-title fw-bold text-dark fs-3"><?= htmlspecialchars($enc['nombre_tipo']) ?></h5>
          </div>
        </div>
      </button>
    </div>
    <?php $primero = false; endforeach; ?>
  </div>
</div>
               


        </div>
      </div>
    </main>

    <footer class="w-100 position-fixed bottom-0 start-0" style="border-top: 2px solid #222; height: 300px;">
      <div class="row g-0 h-100">
        <div class="col h-100">
          <button class="btn rounded-0 w-100 h-100 bg-light border-0 text-secondary shadow-none" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <svg viewBox="0 0 24 24" width="56" height="56" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
        </div>
        
        <div class="col-auto h-100" style="border-left: 2px solid #222;"></div>
        
        <div class="col h-100">
          <button class="btn rounded-0 w-100 h-100 bg-light border-0 text-secondary shadow-none" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <svg viewBox="0 0 24 24" width="56" height="56" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>