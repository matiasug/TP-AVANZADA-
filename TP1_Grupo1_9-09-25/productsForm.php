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
<form id="productosData" name="productosData" method="POST" class="mainForm">
    <?php
    if ($editar && $_POST['id_producto']!='') {
        echo "<input type='hidden' name='idProducto' id='idProducto' value='" . $_POST['id_producto'] . "' >";
    }?>
    <fieldset id="saborDataFieldset" <?php echo $saborDataFieldsetDisplay; ?> >
        <!--<div class="rowElem block_title">
            <h5>Factura</h5>
        </div>-->
        <!--<div class="rowElem" >
            <div class="descripcion" style='width: 170px;margin-top: 6px;margin-left: 55px;display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Cargar factura</span>
                <label class='switch'><input type='checkbox' name='exp_' id='exp_' onchange="toggleFacturaSection();" /><div class='slider round'></div></label>
            </div>
            <label class="fact_sect">Adjuntar Factura: </label> //ver si se agrega para adjuntar imagenes
            <div class="descripcion fact_sect">
                <input 
                    type="file"  
                    id="attach_fileGasto" name="attach_fileGasto[]" 
                    accept="image/*, application/pdf"
                    multiple
                />

            </div>
        </div>-->
        <?php
        require_once CLASSES_PATH . 'Producto.class.php';
        $p = new Producto();
        $id_producto = $_POST['id_producto'];
        if($id_producto!=''){
            $producto = $p->getProductoById($id_producto);
        }
        $checkedSabores = "";
        $valueSabores = 0;
        $cantSabores = 0;
        $checkedToppings = "";
        $valueToppings = 0;
        $checkedPopular = "";
        $valuePopular = 0;
        $checkedDisponible = "";
        $valueDisponible = 0;
        $checkedActivo = "";
        $valueActivo = 0;
        $mode = 'alta';
        if ($editar && $producto) {
            $checkedSabores = ($producto['tieneSabores'==1]) ? "checked" : "";
            $valueSabores = ($producto['tieneSabores'==1]) ? 1 : 0;
            $cantSabores = ($producto['cantSabores'!=0]) ? "checked" : 0;
            $checkedToppings = ($producto['tieneToppings'==1]) ? "checked" : "";
            $valueToppings = ($producto['tieneToppings'==1]) ? 1 : 0;
            $checkedPopular = ($producto['esPopular'==1]) ? "checked" : "";
            $valuePopular = ($producto['esPopular'==1]) ? 1 : 0;
            $checkedDisponible = ($producto['estado'==1]) ? "checked" : "";
            $valueDisponible = ($producto['estado'==1]) ? 1 : 0;
            $checkedActivo = ($producto['activo'==1]) ? "checked" : "";
            $valueActivo = ($producto['activo'==1]) ? 1 : 0;
            $mode = 'modificacion';
        }
        ?>
        <!--NOMBRE Y DESCRIPCION-->
        <div class="rowElem" style="display:flex; justify-content:space-between;">
            <div style="display:flex;">
                <label>Nombre:</label>
                <div>
                    <input type="text" id="nombre_producto" name="nombre_producto" class="validate[required]" onkeyup="countChar(this)" maxlength="200" value="<?php
                        if ($editar && $producto) {
                            echo htmlspecialchars($producto['nombre']);
                        }
                        ?>" />
                    <div id="charNum1"></div>
                </div>
            </div>
            <div style="display:flex;">
                <label>Descripción:</label>
                <div>
                    <textarea rows="6" type="text" style="resize: none; width: 98%;" id="descrip_producto" name="descrip_producto" class="validate[required]" onkeyup="countChar(this)" maxlength="400"><?php
                        if ($editar && $producto) {
                            echo htmlspecialchars($producto['descripcion']);
                        }
                        ?></textarea>
                    <div id="charNum2"></div>
                </div>
            </div>
        </div>
        <!--PRECIO Y CATEGORIA-->
        <div class="rowElem">
            <div style="display:flex; justify-content:space-between;">
                <div style="display:flex;">
                    <label>Precio:</label>
                    <div> <!--ACOMODAR LA MASCARA-->
                        <input type="number" id="precio_producto" name="precio_producto" class="validate[required] monryMask" value="<?php
                            if ($editar && $producto) {
                                echo $producto['precio'];
                            }
                            ?>" />
                    </div>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <label>Categoría:</label>
                <div>
                    <select name="catId_producto" id="catId_producto">
                        <option value="0" disabled selected>(Ninguna)</option>
                        <?php
                        $categories = $cat->getCategoriesForAdmin($_SESSION['user_id']);
                        while ($category = mysqli_fetch_assoc($categories)) {
                            if ($editar && $category['id_categoria'] == $producto['id_categoria']) {
                                echo "<option value='" . $category['id_categoria'] . "' SELECTED>";
                            } else {
                                echo "<option value='" . $category['id_categoria'] . "'>";
                            }
                            echo htmlspecialchars($category['nombre']);
                            echo "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <!--SWICHES - SABOR Y TOPPING-->
        <div class="rowElem" style="display:flex; justify-content:space-between;">
            <div style='display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Tiene sabores</span>
                <label class='switch'>
                    <input type='checkbox' name='tieneSabores_producto' id='tieneSabores_producto' onchange="toggleSaboresRow();" <?php echo $checkedSabores;?> value="<?php 
                    echo $valueSabores;?>"/><div class='slider round'></div>
                </label>
            </div>
            <div style='display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Tiene toppings</span>
                <label class='switch'>
                    <input type='checkbox' name='tieneToppings_producto' id='tieneToppings_producto' <?php echo $checkedToppings;?> value="<?php 
                    echo $valueToppings;?>" /><div class='slider round'></div>
                </label>
            </div>
        </div>
        <!--SABORES: CANT Y LISTA-->
        <div class="rowElem" id="rowSabores_producto" style="display:none;"> <!--al mostrarse tiene que tener display:flex; justify-content:space-between;-->
            <!--ACA PONER LA CANTIDAD DE SABORES -->
            <div class="rowElem">
                <label>Cantidad de sabores:</label>
                <div>
                    <input type="number" id="cantSabores_producto" class="validate[required]" name="cantSabores_producto" maxlength="10" value="<?php
                        echo $cantSabores;?>" />
                </div>
            </div>
            <div class="rowElem">
                <label>Sabores</label>
                <div id='sabores_producto_container'>
                    <select name='sabores_producto[]' id='sabores_producto' multiple data-placeholder="Seleccione sabores..." >
                        <?php
                        require_once CLASSES_PATH . 'Sabor.class.php';
                        $s = new Sabor();
                        $sabores = $s->getSaboresAdmin(true,true,$_SESSION['user_id']);
                        while ($sabor = mysqli_fetch_array($sabores)) {
                            //acomodar lo de selected
                            echo "<option value='" . $sabor['id_sabor'] . "' $selected>" . htmlspecialchars($sabor['nombre']) . "</option>";
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
                        }
                        
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
                    echo $valuePopular;?>" /><div class='slider round'></div>
                </label>
            </div>
            <div style='display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Está en stock</span>
                <label class='switch'>
                    <input type='checkbox' name='disp_producto' id='disp_producto'  <?php echo $checkedDisponible;?> value="<?php 
                    echo $valueDisponible;?>"/><div class='slider round'></div>
                </label>
            </div>
            <div style='display: flex; align-items: center;justify-content:space-evenly;'>
                <span style='float:left;'>Se sigue encargando</span>
                <label class='switch'>
                    <input type='checkbox' name='activo_producto' id='activo_producto'  <?php echo $checkedActivo;?> value="<?php 
                    echo $valueActivo;?>" /><div class='slider round'></div>
                </label>
            </div>
        </div>
        <!--BOTON DE CONFIRMAR FORM PRODUCTO-->
        <div class="rowElem">
            <input type="button" value="Confirmar" class="submitForm" onclick="Productos.verify(<?php echo $mode;?>)" />
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