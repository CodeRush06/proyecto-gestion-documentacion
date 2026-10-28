
    // ---- Documentos: muestra el select de categoría o el de subcategoría ----
    function mostrarDestino(tipo) {
      document.getElementById('select-categoria').style.display    = (tipo === 'categoria') ? 'block' : 'none';
      document.getElementById('select-subcategoria').style.display = (tipo === 'subcategoria') ? 'block' : 'none';
    }

    // ---- Formulario: preguntas y opciones dinámicas ----
    let contadorPregunta = 0;

    document.getElementById("btn-agregar-pregunta").addEventListener("click", agregarPregunta);

    // Copia la plantilla de pregunta y la agrega al formulario
    function agregarPregunta() {
      const idx = contadorPregunta++;
      const tpl = document.getElementById("template-pregunta").content.cloneNode(true);
      const bloque = tpl.querySelector(".bloque-pregunta");
      bloque.dataset.idx = idx;

      const selectTipo = bloque.querySelector('[data-campo="tipo_pregunta"]');
      const contenedorOpciones = bloque.querySelector(".contenedor-opciones");

      // Muestra las opciones solo si el tipo las necesita
      selectTipo.addEventListener("change", () => {
        contenedorOpciones.style.display =
          (selectTipo.value === "opcion_multiple" || selectTipo.value === "escala") ? "block" : "none";
      });

      bloque.querySelector(".btn-quitar-pregunta").addEventListener("click", () => bloque.remove());
      bloque.querySelector(".btn-agregar-opcion").addEventListener("click", () => agregarOpcion(bloque));

      document.getElementById("contenedor-preguntas").appendChild(bloque);
    }

    // Copia la plantilla de opción dentro de una pregunta
    function agregarOpcion(bloquePregunta) {
      const tpl = document.getElementById("template-opcion").content.cloneNode(true);
      const bloqueOpcion = tpl.querySelector(".bloque-opcion");
      bloqueOpcion.querySelector(".btn-quitar-opcion").addEventListener("click", () => bloqueOpcion.remove());
      bloquePregunta.querySelector(".opciones-lista").appendChild(bloqueOpcion);
    }

    // Antes de enviar: convierte las preguntas en inputs ocultos con nombres tipo array
    // (preguntas[0][texto_pregunta], preguntas[0][opciones][0][texto_opcion], ...)
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

    // Crea un input oculto y lo agrega al formulario
    function agregarHidden(form, name, value) {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = name;
      input.value = value;
      form.appendChild(input);
    }



    // aca terminan las funciones de encuesta y empiezan las de validas formularios


    // =====================================================
// Validaciones de formato para los formularios de Panel_Admin.php
//
// cada <form> llama a su función desde
// el atributo onsubmit, por ejemplo:
//     <form onsubmit="return validarUsuario(this)">
// Si la función devuelve false, el formulario NO se envía.
// Si devuelve true, se envía normal.
//
// =====================================================

// ---------- Formatos (expresiones regulares) ----------
const REGEX_TITULO      = /^\S.{0,99}$/;                     // 1 a 100 caracteres, no empieza con espacio
const REGEX_DESCRIPCION = /^\S.{0,254}$/;                    // 1 a 255 caracteres
const REGEX_NOMBRE      = /^[\p{L}][\p{L}\s'.-]{1,99}$/u;    // solo letras, espacios, ' . -
const REGEX_EMAIL       = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;      // algo@algo.algo
const REGEX_USUARIO     = /^[A-Za-z0-9._-]{3,30}$/;          // 3 a 30, sin espacios
const REGEX_CONTRASENA  = /^.{8,72}$/;                       // 8 a 72 (límite de bcrypt)
const REGEX_IMAGEN      = /\.(jpe?g|png)$/i;                 // el servidor solo acepta jpg y png
const REGEX_ARCHIVO     = /\.(pdf|jpe?g|png)$/i;             // documentos: pdf o imagen

const MAX_BYTES = 5 * 1024 * 1024;                           // 5 MB (ajustalo si hace falta)


// =====================================================
// CATEGORIA - DOCUMENTOS  (CRUD_cat_documento.php)
// =====================================================
function validarCatDocumento(form) {
  const f = form.elements;
  const accion = f["accion"].value;

  // Modificar: tiene que haber elegido cuál
  if (accion === "modificar" && f["id"].value === "") {
    alert("Seleccione una categoría");
    return false;
  }

  if (!REGEX_TITULO.test(f["nombre_categoria"].value.trim())) {
    alert("El nombre es obligatorio (máximo 100 caracteres)");
    return false;
  }

  if (!REGEX_DESCRIPCION.test(f["descripcion"].value.trim())) {
    alert("La descripción es obligatoria (máximo 255 caracteres)");
    return false;
  }

  // La imagen solo se pide al crear
  if (accion === "crear") {
    if (f["logo"].files.length === 0) {
      alert("Seleccione una imagen");
      return false;
    }
    if (!REGEX_IMAGEN.test(f["logo"].files[0].name)) {
      alert("La imagen debe ser jpg o png");
      return false;
    }
    if (f["logo"].files[0].size > MAX_BYTES) {
      alert("La imagen supera los 5 MB");
      return false;
    }
  }

  return true;
}


// =====================================================
// SUBCATEGORIA - DOCUMENTOS  (CRUD_subcat_documento.php)
// =====================================================
function validarSubcategoria(form) {
  const f = form.elements;
  const accion = f["accion"].value;

  if (accion === "modificar" && f["id"].value === "") {
    alert("Seleccione una subcategoría");
    return false;
  }

  if (!REGEX_TITULO.test(f["nombre_subcategoria"].value.trim())) {
    alert("El nombre es obligatorio (máximo 100 caracteres)");
    return false;
  }

  if (!REGEX_DESCRIPCION.test(f["descripcion"].value.trim())) {
    alert("La descripción es obligatoria (máximo 255 caracteres)");
    return false;
  }

  if (f["id_categoria"].value === "") {
    alert("Seleccione la categoría a la que pertenece");
    return false;
  }

  // La imagen (solo en el formulario de registrar): si eligió una, se revisa el formato
  if (f["logo"] && f["logo"].files.length > 0) {
    if (!REGEX_IMAGEN.test(f["logo"].files[0].name)) {
      alert("La imagen debe ser jpg o png");
      return false;
    }
    if (f["logo"].files[0].size > MAX_BYTES) {
      alert("La imagen supera los 5 MB");
      return false;
    }
  }

  return true;
}


// =====================================================
// DOCUMENTOS  (CRUD_documento.php)
// =====================================================
function validarDocumento(form) {
  const f = form.elements;
  const accion = f["accion"].value;

  if (accion === "modificar" && f["id"].value === "") {
    alert("Seleccione un documento");
    return false;
  }

  if (!REGEX_TITULO.test(f["titulo"].value.trim())) {
    alert("El título es obligatorio (máximo 100 caracteres)");
    return false;
  }

  if (!REGEX_DESCRIPCION.test(f["descripcion"].value.trim())) {
    alert("La descripción es obligatoria (máximo 255 caracteres)");
    return false;
  }

  // Solo al crear: categoría O subcategoría (según el radio) y el archivo
  if (accion === "crear") {
    if (f["tipo_destino"].value === "categoria" && f["id_categoria"].value === "") {
      alert("Seleccione una categoría");
      return false;
    }
    if (f["tipo_destino"].value === "subcategoria" && f["id_subcategoria"].value === "") {
      alert("Seleccione una subcategoría");
      return false;
    }

    if (f["archivo"].files.length === 0) {
      alert("Seleccione un archivo");
      return false;
    }
    if (!REGEX_ARCHIVO.test(f["archivo"].files[0].name)) {
      alert("El archivo debe ser pdf, jpg o png");
      return false;
    }
    if (f["archivo"].files[0].size > MAX_BYTES) {
      alert("El archivo supera los 5 MB");
      return false;
    }
  }

  return true;
}


// =====================================================
// CATEGORIA - FORMULARIO / TIPO_ENCUESTA  (CRUD_cat_encuesta.php)
// =====================================================
function validarCatEncuesta(form) {
  const f = form.elements;
  const accion = f["accion"].value;

  if (accion === "modificar" && f["id"].value === "") {
    alert("Seleccione una categoría");
    return false;
  }

  if (!REGEX_TITULO.test(f["nombre_tipo"].value.trim())) {
    alert("El nombre es obligatorio (máximo 100 caracteres)");
    return false;
  }

  if (!REGEX_DESCRIPCION.test(f["descripcion"].value.trim())) {
    alert("La descripción es obligatoria (máximo 255 caracteres)");
    return false;
  }

  return true;
}


// =====================================================
// FORMULARIO (ENCUESTA + PREGUNTAS)  (CRUD_encuesta.php)
// Sirve para registrar y para modificar
// =====================================================
function validarEncuesta(form) {
  const f = form.elements;

  // Modificar: tiene que haber elegido cuál (el de registrar no tiene este campo)
  if (f["id"] && f["id"].value === "") {
    alert("Seleccione un formulario");
    return false;
  }

  if (!REGEX_TITULO.test(f["titulo"].value.trim())) {
    alert("El título es obligatorio (máximo 100 caracteres)");
    return false;
  }

  if (!REGEX_DESCRIPCION.test(f["descripcion"].value.trim())) {
    alert("La descripción es obligatoria (máximo 255 caracteres)");
    return false;
  }

  if (f["id_tipo_encuesta"].value === "") {
    alert("Seleccione un tipo de encuesta");
    return false;
  }

  // Las preguntas solo existen en el formulario de registrar
  const contenedor = document.getElementById("contenedor-preguntas");
  if (contenedor && form.contains(contenedor)) {
    const bloques = contenedor.querySelectorAll(".bloque-pregunta");

    if (bloques.length === 0) {
      alert("Agregue al menos una pregunta");
      return false;
    }

    for (let i = 0; i < bloques.length; i++) {
      const numero = i + 1;
      const texto  = bloques[i].querySelector('[data-campo="texto_pregunta"]').value.trim();
      const tipo   = bloques[i].querySelector('[data-campo="tipo_pregunta"]').value;

      if (texto === "") {
        alert("Escriba el texto de la pregunta " + numero);
        return false;
      }

      // Opción múltiple y escala necesitan al menos 2 opciones con texto
      if (tipo === "opcion_multiple" || tipo === "escala") {
        const opciones = bloques[i].querySelectorAll('[data-campo="texto_opcion"]');
        let conTexto = 0;
        for (let j = 0; j < opciones.length; j++) {
          if (opciones[j].value.trim() !== "") conTexto++;
        }
        if (conTexto < 2) {
          alert("La pregunta " + numero + " necesita al menos 2 opciones con texto");
          return false;
        }
      }
    }
  }

  return true;
}


// =====================================================
// ADMINISTRADOR / USUARIO  (CRUD_usuario.php)
// Sirve para registrar y para modificar
// =====================================================
function validarUsuario(form) {
  const f = form.elements;
  const accion = f["accion"].value;

  if (accion === "modificar" && f["id"].value === "") {
    alert("Seleccione un administrador");
    return false;
  }

  if (!REGEX_NOMBRE.test(f["nombre"].value.trim())) {
    alert("El nombre solo puede tener letras");
    return false;
  }

  if (!REGEX_NOMBRE.test(f["apellido"].value.trim())) {
    alert("El apellido solo puede tener letras");
    return false;
  }

  if (!REGEX_EMAIL.test(f["email"].value.trim())) {
    alert("El email no es válido");
    return false;
  }

  if (!REGEX_USUARIO.test(f["usuario"].value.trim())) {
    alert("El usuario debe tener de 3 a 30 caracteres: letras, números, punto, guion o guion bajo");
    return false;
  }

  // Contraseña y rol: solo existen en el formulario de registrar
  if (f["contrasena"] && !REGEX_CONTRASENA.test(f["contrasena"].value)) {
    alert("La contraseña debe tener entre 8 y 72 caracteres");
    return false;
  }

  if (f["es_medico"] && f["es_medico"].value === "") {
    alert("Seleccione un rol");
    return false;
  }

  return true;
}
