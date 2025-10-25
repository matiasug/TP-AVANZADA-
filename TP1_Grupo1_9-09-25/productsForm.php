<style>
    .combo_container {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1em;
    }
    .combo_container label {
        white-space: nowrap;
        flex-shrink: 0;
    }
    .combo_container .material-symbols-outlined {
        flex-grow: 1; 
    }
</style>
<?php
    require_once __DIR__ . '/Model/ConexionBD.php';
    
    // Iniciar sesión si no está iniciada
    if (!session_id()) session_start();

    // --- INICIALIZACIÓN DE VARIABLES ---
    // Para evitar errores de "Undefined variable"
    $mensaje = $_SESSION['mensaje'] ?? '';
    unset($_SESSION['mensaje']);

    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $editorial = $_POST['editorial'] ?? '';

    // Variables que parecían faltar en el formulario original
    $cantSabores = $_POST['cantSabores_producto'] ?? 0;
    $producto = ['id_producto' => null]; // Simulación, ajustar según sea necesario
    $editar = false; // Simulación, ajustar si es un form de edición
    $checkedPopular = '';
    $valuePopular = '1';
    $checkedDisponible = '';
    $valueDisponible = '1';
    $checkedActivo = '';
    $valueActivo = '1';
    $mode = $editar ? 1 : 0; // 0 para crear, 1 para editar

    // --- LÓGICA DE PROCESAMIENTO DEL FORMULARIO (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = false;
        
        // Validación básica
        if (empty($titulo) || empty($autor) || empty($editorial)) {
            $mensaje = "Por favor complete todos los campos.";
            $error = true;
        }
        
        if (!$error) {
            try {
                $libro = new Libro();
                $libro->setTitulo($titulo);
                $libro->setAutor($autor);
                $libro->setEditorial($editorial);
                
                // --- LÓGICA PARA MANEJAR EL ARCHIVO ---
                $filePath = null;
                if (isset($_FILES['archivo_libro']) && $_FILES['archivo_libro']['error'] == 0) {
                    $target_dir = __DIR__ . "/uploads/"; // Usamos una ruta absoluta
                    $fileType = strtolower(pathinfo($_FILES["archivo_libro"]["name"], PATHINFO_EXTENSION));
                    $unique_name = uniqid('libro_', true) . '.' . $fileType;
                    $target_file = $target_dir . $unique_name;

                    // --- VALIDACIÓN DE TAMAÑO (ej: 5MB) ---
                    $max_size = 5 * 1024 * 1024; // 5 MB en bytes
                    if ($_FILES['archivo_libro']['size'] > $max_size) {
                        throw new Exception('El archivo es demasiado grande. El tamaño máximo permitido es de 5MB.');
                    }

                    $allowed_types = ['pdf', 'txt'];
                    if (!in_array($fileType, $allowed_types)) {
                        throw new Exception('Solo se permiten archivos PDF y TXT.');
                    }

                    if (move_uploaded_file($_FILES["archivo_libro"]["tmp_name"], $target_file)) {
                        $filePath = $unique_name;
                    } else {
                        throw new Exception('Hubo un error al subir el archivo.');
                    }
                }

                // Obtenemos el ID del usuario de la sesión y lo pasamos al método save
                $idPersona = $_SESSION['DatosPersona']['id'] ?? null;
                if ($libro->save($idPersona, $filePath)) {
                    $_SESSION['abm_msg'] = "Libro guardado correctamente.";
                    header("Location: abmProductos.php"); // Redirigir a la lista
                    exit;
                } else {
                    $mensaje = "Error al guardar el libro.";
                }
            } catch (Exception $e) {
                $mensaje = "Error de base de datos: " . $e->getMessage();
            }
        }
    }
?>

<form id="productosData" name="productosData" method="POST" class="mainForm" enctype="multipart/form-data">
    <fieldset> <!-- Añadido para que el HTML sea válido -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <div class="rowElem" style="display:flex; justify-content:space-between;">
            <div style="display:flex;">
                <label>Título:</label>
                <div>
                    <input type="text" id="titulo" name="titulo" class="validate[required]" maxlength="200"
                           value="<?php echo htmlspecialchars($titulo); ?>" required />
                </div>
            </div>
            <div style="display:flex;">
                <label>Autor:</label>
                <div>
                    <input type="text" id="autor" name="autor" class="validate[required]" maxlength="200"
                           value="<?php echo htmlspecialchars($autor); ?>" required />
                </div>
            </div>
        </div>
        
        <div class="rowElem">
            <div style="display:flex; justify-content:space-between;">
                <div style="display:flex;">
                    <label>Editorial:</label>
                    <div>
                        <input type="text" id="editorial" name="editorial" class="validate[required]" maxlength="200"
                               value="<?php echo htmlspecialchars($editorial); ?>" required />
                    </div>
                </div>
            </div>
        </div>
        <div class="rowElem">
            <label>Archivo del Libro (PDF/TXT)</label>
            <input type="file" name="archivo_libro" id="archivo_libro" class="form-control" accept=".pdf,.txt">
        </div>
        <div class="rowElem" style="display:flex; justify-content:space-between;">
            <button type="submit" class="btn btn-primary">Guardar Libro</button>
            <a href="abmProductos.php" class="btn btn-secondary">Ver Listado</a>
        </div>
        <!--SABORES: CANT Y LISTA-->
        <div class="rowElem" id="rowSabores_producto" style="display:none;"> <!--al mostrarse tiene que tener display:flex; justify-content:space-between;-->
            <!--ACA PONER LA CANTIDAD DE SABORES -->
            <div class="rowElem">
                <label>Cantidad de sabores:</label>
                <div>
                    <input type="number" id="cantSabores_producto" class="validate[required]" name="cantSabores_producto" maxlength="10" value="<?php
                        echo htmlspecialchars($cantSabores);?>" />
                </div>
            </div>
            <div class="rowElem">
                <label>Sabores:</label>
                <div id='sabores_producto_container'>
                    <select name='sabores_producto[]' id='sabores_producto' multiple data-placeholder="Seleccione sabores..." >
                        <?php
                        // Este bloque requiere que la clase Sabor exista y funcione.
                        // Lo comento para evitar errores si la clase no está lista.
                        /*
                        if (file_exists(__DIR__ . '/Model/Sabor.class.php')) {
                            require_once __DIR__ . '/Model/Sabor.class.php';
                            $s = new Sabor();
                            // La siguiente línea probablemente falle si 'user_id' no está en la sesión.
                            $sabores = $s->getSaboresAdmin(true, true, $_SESSION['user_id'] ?? 0);
                            while ($sabor = mysqli_fetch_array($sabores)) {
                                $selected = ''; // La lógica para 'selected' necesita ser implementada
                                echo "<option value='" . $sabor['id_sabor'] . "' $selected>" . htmlspecialchars($sabor['nombre']) . "</option>";
                            }
                        }

                        if($producto['id_producto']){
                            $saboresP = $s->getSaboresForProducto($producto['id_producto']);
                        }

                        if($editar && !empty($saboresP)){
                            function check_selected($saboresP, $depto_id) { //acomodar lo de selected
                                mysqli_data_seek($saboresP, 0);
                                while ($s = mysqli_fetch_array($saboresP)) {
                                    if ($s['id_sabor'] == $depto_id) {
                                        return "selected";
                                    }
                                }
                                return "";
                            }
                        }*/
                        
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="fix"></div>
        </div>
        <!--SWICHES - SABOR Y TOPPING-->
        <div class="rowElem" style="display:flex; justify-content:space-between;">
            <div style='display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Es popular</span>
                <label class='switch'>
                    <input type='checkbox' name='es_popular' id='es_popular'  <?php echo $checkedPopular;?> value="<?php
                    echo htmlspecialchars($valuePopular);?>" /><div class='slider round'></div>
                </label>
            </div>
            <div style='display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Está en stock</span>
                <label class='switch'>
                    <input type='checkbox' name='disp_producto' id='disp_producto'  <?php echo $checkedDisponible;?> value="<?php
                    echo htmlspecialchars($valueDisponible);?>"/><div class='slider round'></div>
                </label>
            </div>
            <div style='display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Se sigue encargando</span>
                <label class='switch'>
                    <input type='checkbox' name='activo_producto' id='activo_producto'  <?php echo $checkedActivo;?> value="<?php
                    echo htmlspecialchars($valueActivo);?>" /><div class='slider round'></div>
                </label>
            </div>
        </div>
        <!--BOTON DE CONFIRMAR FORM PRODUCTO-->
        <div class="rowElem">
            <input type="button" value="Confirmar" class="submitForm" onclick="Productos.verify(<?php echo $mode; ?>)" />
        </div>
    </fieldset>
</form>
<script>
    $(document).ready(function(){
        //creo que por ahora nada
    });
    
    function validate(event) {
        var key = event.which || event.keyCode || 0;
        return ((key != 39))
    }

    $('#recibo').on('change', function () {
        if ($('#recibo').val() != '') {
            $('#monto_factura').prop('disabled', false);
        } else {
            $('#monto_factura').prop('disabled', true);
        }
    });

    $('#fechaInf').on('blur', function () {
        if (!$('#monto_factura').prop('disabled')) {
            $('#monto_factura').focus();
        }else{
            $('#proveedor').focus();
        }
    });

    function countChar(this) {
        let elem = this.id;
        var len = this.value.length;
        let lenPredet;
        let elemento = '';
        if(elem == "descrip_producto"){
            lenPredet = 500;
            elemento = document.getElementById('charNum2');
        }else{ //nombre_producto 
            lenPredet = 200;
            elemento = document.getElementById('charNum1');
        }
        if (len >= lenPredet) {
            this.value = this.value.substring(0, lenPredet);
        }
        if (len === lenPredet) {
            var mensaje = 'Disponible: 0';
            elemento.textContent = mensaje;
        } else {
            var length = lenPredet - len;
            var mensaje = 'Disponible: ' + length;
            elemento.textContent = mensaje;
        }
    }
</script>