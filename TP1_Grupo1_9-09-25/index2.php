<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
   <?php require_once("header.php")?>
      <link href="bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="css/estilos.css" rel="stylesheet">

<body class="d-flex flex-column min-vh-100"
      style="background: url('imgs/fondo1.jpg') no-repeat center center fixed; background-size: cover;">

  
  <main class="flex-fill d-flex justify-content-center align-items-center">

    <!-- Login centrado -->
    <div class="card shadow-lg rounded-4 p-4" style="background: #f6fff9; width: 100%; max-width: 350px; border: 2px solid #856133;">
      <div class="card-body">

      <h3 class="fw-bold text-center" style="color: #856133; -webkit-text-stroke: 1.5px #6e5215ff; font-size: 2rem;">
        Inicio
      </h3>
          <!-- Aca podrian ir generando la tabla dinamicamente con la clase que contenga todos los libros guardados
           O sea, hacer HTML de la tabla aca. En lo posible usar jquery para poder meterle a la tabla datatables.
           Pueden ver si inicializan la tabla en el mismo archivo (puse un scrip x las dudas) o hacerlo en un archivo a parte
           para que quede mas organizado. Si lo hacen en un archivo a parte, incluyanlo a lo ultimo xq les puede saltar error
           por haber inicializado la tabla antes de que haya sido creado el HTML. -->
          <div class="mb-2">
            <label for="nombre" class="form-label">FILTRO</label>
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="name">
          </div>

          <div class="mb-2">
            <!-- boton de nuevo libro para abrir el dialog -->
             <button id="btnAltaLibro">Nuevo libro</button>
          </div>

          <table id="miTabla">
              <!-- aca la van haciendo con php -->
               <thead>
                <!-- esto va si o si: el encabezado de las columnas-->
              </thead>

              <tbody>
                <!-- llamas a la BD parar ver si tiene registros de libros - si no tiene, no generas nada con php
                 y dejas que datatables te haga el mensaje al tener la tabla vacia. -->
              </tbody>

              <tfooter>
                <!-- lo mismo que tbody: podes generarle contenido si tiene registros, como la cantidad total de libros,
                 pero si no tiene nada podes dejarlo vacio
                 - hace la paginacion automaticamente -->
              </tfooter>
              

            </table>
      
    </div>

  </main>
  
    <!-- ACA BAJO INCLUYAN UN DIALOG de jquery para hacer la alta y modificacion de libros

    ! para ambos: hidden con modo y otro hidden idlibro.

    - la baja, podrian hacerla de forma que creas una ultima columna que sea "Acciones" donde se pueda ver, editar o eliminar el libro. Adentro
    del modal le pueden meter un input hidden para ocultar el modo en el que estan tratando el libro.
    - para el alta se va a usar el mismo modal y le mandan modo alta. Aca el hidden idlibro va vacio
    
    para estas operaciones hagan un controlador que se encargue de las tres cosas y antes de hacer modificaciones en la BD, se fija el modo -->
  <?php require_once('footer.php') ?> 

  <div id="myDialog" title="Esto cambia segun el modo">
      <?php require_once('dialogoLibro.php'); //el HTML de alta, modificacion y visualizacion del libro ?> 
  </div>

  <div id="myDialogEliminar" title="Eliminar libro">
      <?php require_once('dialogoLibro2.php'); //el HTML de baja ?> 
  </div>


<script src="sistema.js"></script><!-- opcion de hacer un .js a parte. -->
<!-- opcion de inicializar la datatable en el mismo archivo
 - tienen que incluir jquery sea cual sea la opcion que tomen de donde poner la inicializacion. !!!
 -  -->
<script>
  $(document).ready(function() {
      let table = $('#miTabla').DataTable({
          "paging": true,
          "width": "100%", // Opción nativa de DataTables
          "columnDefs": [
              { "orderable": false, "targets": 6 }
          ]
          //le pueden ir personalizando mas cosas - esto esta en el gemini de marianoooo
      });

      //el dialogo de alta, modificacion y visualizacion - para eliminar le haria un dialog a parte que pregunte si estas seguro de eliminar el libro
      $("#myDialog").dialog({
            autoOpen: false, // Prevents automatic opening on page load
            modal: true,     // Makes the dialog modal
            buttons: {
                "OK": function() {
                    $(this).dialog("close");
                },
                "Cancel": function() {
                    $(this).dialog("close");
                }
            }
        });

        $(".btnEditarLibro, .btnVerLibro, #btnAltaLibro").on("click", function() { //capta el click de esos tres botones
            //los primeros dos pueden ser de cualquiera de los botones que se generan de cada fila de la tabla
            $("#myDialog").dialog("open");
        });
        
    });
</script>





