<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid">
         <header class="row bg-white p-4 justify-content-center"> 
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#"> <img src="assets/logo_rgb_200x100.png"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Inserte información" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
    </form>
  </div>
</nav>

         </header>
        <div class="row">
                <aside class="col-md-3 text-white p-4 bg-info d-flex flex-column align-items-center"> <h1 class="h3 text-center m-1">Documentación de "Juan"</h1>  <img src="assets/qr-code.png" class="img-thumbnail mx-auto" width="200px" length="200px">

                </aside>
                <main class=" col-md-9 bg-white"><h1 class="h3">Panel de Administración - Resultados de búsqueda "Juan"</h1>

                    <div class="card"> <div class="card-title text-center my-2">Datos de Paciente <span class="font-weight-bold">Juan  </span><button class="button bg-info mx-2 border-0">Modificar</button></div> <div class="card-body">
                        <table class=" table table-striped mx-2">
                            <thead> <th> Nombre</th>
                            <th> Edad </th>
                            <th> Condicion </th>
                            <th> Turno </th>
                            <th> Historial Médico </th>
                            </thead>
                            <tbody>
                                <tr>
                                <td> Juan</td>
                                <td> 52</td>
                                <td> Diabetes</td>
                                <td> Pendiente </td>
                                <td> <button class="button bg-info mx-1 border-0">Acceder a historial Medico</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div></div>
                    <div class="card"> <div class="card-title text-center my-2"> Formulario <span class="font-weight-bold">Encuesta de Satisfacción  </span><button class="button bg-info mx-2 border-0">Modificar</button></div> <div class="card-body">
                        <form>
                            <div class="form-group">
                                 <label for="exampleInput1">Ingresar Cédula</label>
                                 <input type="text" class="form-control" id="exampleInput1" placeholder="Ingresar Cédula">
                                  <small id="InputHelp" class="form-text text-muted">Esta encuesta es anónima. Tu cedula no será registrada.</small>
                            </div>
                            <div class="form-group">
                                 <label for="miLista">Ingrese Sala de consulta en la que ha sido atendido/atendida  </label>
                                <select class="form-select" id="miLista">
                                      <option selected>Elige una opción...</option>
                                    <option value="1">Sala 1 (Pediatria)</option>
                                    <option value="2">Sala 2 (Odontologia)</option>
                                    <option value="3">Sala 3 (Ginecologia)</option>
                                  </select>
                            </div>
                            <div class="form-group">
                                <label for="Clasif">Clasifique su experiencia (Azul es positivo)</label>
                                
                                <input type="range" class="form-control-range" id="Clasif"> 
                            </div>
                            <div class="form-group">
                                 <label for="exampleFormControlTextarea1">Ingrese recomendación/opinion</label>
                                 <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                            </div>

                           <button type="submit" class="btn btn-outline-info">Enviar</button>
                        </form>
                    
                    </div></div>

                </main> <!-- Aqui va el panel de control, los medicos pueden subir archivos no modifcarlos, -->
        </div>
    
    <footer></footer>
</div>
     <script src="https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
