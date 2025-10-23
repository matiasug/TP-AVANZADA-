<?php
header('Content-Type: application/json');

/* ALTA, BAJA Y MODIFICACION DE PRODUCTOS */
if (isset($_POST['operacion']) && ($_POST['operacion']=='alta' || $_POST['operacion']=='modificacion')) {   

    // Validar y asignar datos del producto
    if(isset($_POST['nombre']) && !empty(trim($_POST['nombre']))) {
        $nombre = htmlspecialchars(trim($_POST['nombre']));
    } else {
        echo json_encode(["result" => 0, "msg" => "El nombre es obligatorio"]);
        exit();
    }

    if(isset($_POST['descripcion']) && !empty($_POST['descripcion'])) {
        $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    } else {
        echo json_encode(["result" => 0, "msg" => "La descripción es obligatoria"]);
        exit();
    }

    if(isset($_POST['precio']) && $_POST['precio']!='') {
        $precio = str_replace(',', '.', $_POST['precio'] ?? ''); // Reemplaza coma por punto si es necesario
        $precio = floatval($_POST['precio']);
        if(!is_numeric($_POST['precio']) || $_POST['precio'] <= 0) {
            echo json_encode(["result" => 0, "msg" => "El precio debe ser un número positivo"]);
            exit();
        }
    } else {
        echo json_encode(["result" => 0, "msg" => "El precio es obligatorio"]);
        exit();
    }

    if(isset($_POST['categoria_id']) && is_numeric($_POST['categoria_id'])) {
        require_once(CLASSES_PATH . "Categoria.class.php");
        $categoria = new Categoria();
        $idCategoria = intval($_POST['categoria_id']);
        $categoria = $categoria->getCategoryById($idCategoria);
        if($categoria === null) {
            echo json_encode(["result" => 0, "msg" => "La categoría no existe"]);
            exit();
        }
    } else {
        echo json_encode(["result" => 0, "msg" => "La categoría es obligatoria"]);
        exit();
    }

    if(isset($_POST['tieneToppings']) && in_array($_POST['tieneToppings'], ['0', '1'])) {
        $tieneToppings = intval($_POST['tieneToppings']);
    } else {
        echo json_encode(["result" => 0, "msg" => "Debe indicar si el producto tiene toppings"]);
        exit();
    }
    if($tieneToppings==1 && (!isset($_POST['toppings']) || empty($_POST['toppings']))) {
        echo json_encode(["result" => 0, "msg" => "Debe seleccionar al menos un topping"]);
        exit();
    }
    if($tieneToppings==1 && isset($_POST['toppings']) && is_array($_POST['toppings'])) {
        $toppings = array_map('intval', $_POST['toppings']);
    } else {
        $toppings = [];
    }
    
    if(isset($_POST['tieneSabores']) && in_array($_POST['tieneSabores'], ['0', '1'])) {
        $tieneSabores = intval($_POST['tieneSabores']);
    } else {
        echo json_encode(["result" => 0, "msg" => "Debe indicar si el producto tiene sabores"]);
        exit();
    }
    if($tieneSabores==1 && (!isset($_POST['sabores']) || empty($_POST['sabores']))) {
        echo json_encode(["result" => 0, "msg" => "Debe seleccionar al menos un sabor"]);
        exit();
    }
    if($tieneSabores==1 && isset($_POST['sabores']) && is_array($_POST['sabores'])) {
        $cantSabores = count($_POST['sabores']);
        $sabores = array_map('intval', $_POST['sabores']);
    } else {
        $cantSabores = 0;
        $sabores = [];
    }

    if(isset($_POST['esPopular']) && in_array($_POST['esPopular'], ['0', '1'])) {
        $esPopular = intval($_POST['esPopular']);
    } else {
        echo json_encode(["result" => 0, "msg" => "Se debe indicar si el producto es popular o no"]);
        exit();
    }

    if(isset($_POST['estado']) && in_array($_POST['estado'], ['0', '1'])) {
        $estado = intval($_POST['estado']);
    } else {
        echo json_encode(["result" => 0, "msg" => "Se debe indicar el estado de disponibilidad del producto"]);
        exit();
    }

    if(isset($_POST['activo']) && in_array($_POST['activo'], ['0', '1'])) {
        $activo = intval($_POST['activo']);
    } else {
        echo json_encode(["result" => 0, "msg" => "Se debe indicar si el producto está activo o no"]);
        exit();
    }

    /* Por si algun dia pinta agregar imagen
    if(isset($_POST['imagen']) && !empty(trim($_POST['imagen']))) {
        $imagen = htmlspecialchars(trim($_POST['imagen']));
    } else {
        $imagen = ''; // Imagen es opcional
    }*/

    if(($_POST['operacion']=='alta')){
        $fechaCreacion = date('Y-m-d H:i:s');

        try {
            require_once(CLASSES_PATH . "Producto.class.php");
            $product = new Producto();
            $creado = $product->setNewProducto($nombre,$descripcion,$precio,$idCategoria,$fechaCreacion,$tieneToppings,$tieneSabores,$cantSabores,$sabores,$toppings,$esPopular,$estado,$activo);
            if ($creado) {
                echo json_encode(["result" => 1, "msg" => "El producto ha sido creado exitosamente"]);
            } else {
                echo json_encode(["result" => 0, "msg" => "Hubo un error al crear el producto"]);
            }

        } catch (Exception $e) {
            echo json_encode(["result" => 0, "msg" => $e->getMessage()]);
        }

    } else if($_POST['operacion']=='modificacion'){
        
        $idProducto = intval($_POST['id_producto'] ?? 0);
        if($idProducto <= 0) {
            echo json_encode(["result" => 0, "msg" => "ID de producto inválido"]);
            exit();
        }

        // Verificar si el producto existe
        require_once(CLASSES_PATH . "Producto.class.php");
        $product = new Producto();
        $existingProduct = $product->getProductoById($idProducto);
        if(!$existingProduct) {
            echo json_encode(["result" => 0, "msg" => "El producto no existe"]);
            exit();
        }
        
        $modificado = $product->updateProducto($idProducto,$nombre,$descripcion,$precio,$idCategoria,$tieneToppings,$tieneSabores,$cantSabores,$sabores,$toppings,$esPopular,$estado,$activo);
        if ($modificado) {
            echo json_encode(["result" => 1, "msg" => "El producto ha sido modificado exitosamente"]);
        } else {    
            echo json_encode(["result" => 0, "msg" => "Hubo un error al modificar el producto"]);
        }
        exit();
    }

    
} else if(isset($_POST['operacion']) && $_POST['operacion']=='baja') {
    // Aquí iría el código para dar de baja un producto
    
    $idProducto = intval($_POST['id_producto'] ?? 0);
    if($idProducto <= 0) {
        echo json_encode(["result" => 0, "msg" => "ID de producto inválido"]);
        exit();
    }

    require_once(CLASSES_PATH . "Producto.class.php");
    $product = new Producto();
    // Verificar si el producto existe
    $existingProduct = $product->getProductoById($idProducto);
    if(!$existingProduct) {
        echo json_encode(["result" => 0, "msg" => "El producto no existe"]);
        exit();
    }
    
    //Si el producto está en algún pedido, no dejar dar de baja
    //Si el producto no está en ningún pedido, permitir dar de baja (definitiva del sistema, eliminar la fila de la BD)
    require_once(CLASSES_PATH . "PedidosManager.class.php");
    $pm = new PedidosManager();
    $perteneceAPedidos = $pm->getPedidosForProducto($idProducto); // Aquí deberías implementar la lógica real
    if(!$perteneceAPedidos) {
        $product->removeProducto($idProducto);
    } else {
        $product->removeSaboresDelProducto($idProducto);
        $product->removeToppingsDelProducto($idProducto);
        $product->updateProductoActiveStatus($idProducto, 0); // Desactivar el producto en lugar de eliminarlo
    }
    echo json_encode(["result" => 1, "msg" => "El producto ha sido eliminado"]);
    exit();

} else {
    echo json_encode(["result" => 0, "msg" => "Parámetros inválidos"]);
    exit();
}
