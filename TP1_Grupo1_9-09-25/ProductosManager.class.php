<?php
require_once(CLASSES_PATH . "DatabaseAdmin.class.php");

class Producto {

    function __construct() {
        //Nada por ahora
    }

    public function setNewProducto($nombre,$descripcion,$precio,$idCategoria,$tieneToppings,$tieneSabores,$cantSabores,$sabores,$toppings,$esPopular,$estado,$activo) {
        $db = DataBaseAdmin::getInstance();
        $pId = $db->getNextId("productos", "id_producto");
        $q = "INSERT INTO productos (id_producto,nombre,descripcion,precio,id_categoria,tieneSabores,tieneToppings,cantSabores,esPopular,estado,activo) 
            VALUES ('$pId','$nombre','$descripcion','$precio','$idCategoria','$tieneToppings','$tieneSabores','$cantSabores','$sabores','$esPopular','$estado','$activo')";
        $q2 = "";
        if($tieneSabores==1 && is_array($sabores) && count($sabores)>0) { //se guarda en la tabla intermedia productossabores
            $q2 ="INSERT INTO productos_sabores (id_producto, id_sabor) VALUES ";
            $values = [];
            foreach($sabores as $saborId) {
                $values[] = "('$pId', '$saborId')";
            }
            $q2 .= implode(", ", $values);
            $q2 = str_replace(", )", ")", $q2);
        }

        $q3 = "";
        if($tieneToppings==1) { //se crea la tabla intermedia productostoppings
            /*$q3 = "CREATE TABLE IF NOT EXISTS productostoppings (
                id_producto INT NOT NULL,
                id_topping INT NOT NULL,
                PRIMARY KEY (id_producto, id_topping),
                FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
                FOREIGN KEY (id_topping) REFERENCES toppings(id_topping) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";*/
            $q3 = "INSERT INTO productos_toppings (id_producto, id_topping) VALUES ";
            $values = [];
            // Suponiendo que los toppings posibles son del 1 al 10, se insertan todos inicialmente
            foreach($toppings as $toppingId) {
                $values[] = "('$pId', '$toppingId')";
            }
            $q3 .= implode(", ", $values);
            $q3 = str_replace(", )", ")", $q3);
        } else {
            $q3 = "";
        }
        $db->addQuery($q);
        $db->addQuery($q2);
        $db->addQuery($q3);
        return $db->persistAll();
    }

    function getProductoById($pId) {
        $db = DataBaseAdmin::getInstance();
        $q = "SELECT * FROM productos WHERE id_producto='$pId' LIMIT 1";
        $row = $db->executeQueryUniqueResult($q);
        // Return the row or false if not found. executeQueryUniqueResult may return null/false when not found.
        if (is_array($row) && count($row) > 0) {
            return $row;
        }
        return false;
    }

    function getProductsForAdmin($idUser) { //para poder ver/editar todos sus atributos en el panel de admin
        $db = DataBaseAdmin::getInstance();
        require_once(CLASSES_PATH . "UsuarioManager.class.php");
        $um = new UsuarioManager();
        if($um->getUser($idUser)) { //si es admin ve todos los productos
            $q = "SELECT * FROM productos ORDER BY id_producto";
        } else { //si es usuario normal ve solo los productos activos
            $q = "SELECT * FROM productos WHERE activo=1 ORDER BY id_producto";
        }
        $rows = $db->executeQueryResult($q);
        return $rows;
    }

    function getProductosAdmin($catSelect, $showPopulares, $showDisponibles, $showActivos){
        $db = DataBaseAdmin::getInstance();
        $q = "SELECT * FROM productos";
        if($catSelect != "all"){
            $q .= " WHERE id_categoria='$catSelect' ";
        }
        if($showPopulares == "1"){
            if($catSelect!='all'){
                $q .= " AND esPopular=1 ";
            }else {
                $q .= " WHERE esPopular=1 ";
            }
        }
        if($showDisponibles == '1'){
            if($catSelect!='all' || $showPopulares == "1"){
                $q .= " AND estado=1 ";
            }else{
                $q .= " WHERE estado=1 ";
            }
        }
        if($showActivos == '1'){
            if($catSelect!='all' || $showPopulares == "1" || $showDisponibles == '1'){
                $q .= " AND activo=1 ";
            }else{
                $q .= " WHERE activo=1 ";
            }
        }
        $q .= " ORDER BY id_producto";
        $rows = $db->executeQueryResult($q);
        return $rows;
    }

    function getActiveProducts() { //ver si traerlos teniendo activo=1 o si o si que este activo y disponible
        $db = DataBaseAdmin::getInstance();
        $q = "SELECT *
                FROM productos 
                WHERE estado=1 AND activo=1 ORDER BY id_producto";
        $rows = $db->executeQueryResult($q);
        return $rows;
    }
    
    function getPopularProducts() {
        $db = DataBaseAdmin::getInstance();
        $q = "SELECT id_producto, nombre, descripcion, precio 
                FROM productos 
                WHERE esPopular=1 ORDER BY id_producto";
        $rows = $db->executeQueryResult($q);
        return $rows;
    }

    function getPopulaProductById($pId) {
        $db = DataBaseAdmin::getInstance();
        $q = "SELECT id_producto
                FROM productos 
                WHERE esPopular=1 LIMIT 1";
        $rows = $db->executeQueryResult($q);
        return $rows;
    }

    function admiteSabores($pId) { //para el modal de agregar sabores en el index
        $db = DataBaseAdmin::getInstance();
        $q = "SELECT id_producto FROM productos WHERE tieneSabores=1 AND id_producto='$pId' LIMIT 1";
        //aca habria que ejecutar la query $q
        return $db->executeQueryUniqueResult($q);
    }

    function admiteToppings($pId) { //para el modal de agregar toppings en el index
        $db = DataBaseAdmin::getInstance();
        $q = "SELECT id_producto FROM productos WHERE tieneToppings=1 AND id_producto='$pId' LIMIT 1";
        return $db->executeQueryUniqueResult($q);
    }

    public function updateProducto($pId, $nombre, $descripcion, $precio, $cId, $tieneToppings,$tieneSabores,$cantSabores,$sabores,$toppings,$esPopular,$estado,$activo) {
        $db = DataBaseAdmin::getInstance();
        $q = "UPDATE productos SET nombre=$nombre, descripcion='$descripcion', precio='$precio', id_categoria='$cId', 
                tieneToppings='$tieneToppings',tieneSabores='$tieneSabores',cantSabores='$cantSabores',
            esPopular='$esPopular',estado='$estado', activo='$activo' WHERE id_producto = '$pId' LIMIT 1";
        
        $q2_1 = "";
        $q2 = "";
        if($tieneSabores==1 && is_array($sabores) && count($sabores)>0) { //se guarda en la tabla intermedia productossabores
            //aca se deberian borrar los registros que ya existen con dicho producto y sabores
            //asi no se generan confusiones trayendo registros que ya no se usan
            $q2_1 = "DELETE FROM productos_sabores WHERE id_producto ='$pId'";

            //se actualizan los registros
            $q2 ="INSERT INTO productos_sabores (id_producto, id_sabor) VALUES ";
            $values = [];
            foreach($sabores as $saborId) {
                $values[] = "('$pId', '$saborId')";
            }
            $q2 .= implode(", ", $values);
            $q2 = str_replace(", )", ")", $q2);
        }


        $q3 = "";
        if($tieneToppings==1) {
            //aca se deberian borrar los registros que ya existen con dicho producto y toppings
            //asi no se generan confusiones trayendo registros que ya no se usan
            $q3_1 = "DELETE FROM productos_toppings WHERE id_producto ='$pId'";

            //se actualizan los registros
            $q3 = "INSERT INTO productos_toppings (id_producto, id_topping) VALUES ";
            $values = [];
            // Suponiendo que los toppings posibles son del 1 al 10, se insertan todos inicialmente
            foreach($toppings as $toppingId) {
                $values[] = "('$pId', '$toppingId')";
            }
            $q3 .= implode(", ", $values);
            $q3 = str_replace(", )", ")", $q3);
        } else {
            $q3 = "";
        }
        $db->addQuery($q);
        if (!empty($q2_1)) $db->addQuery($q2_1);
        if (!empty($q2)) $db->addQuery($q2);
        if (!empty($q3_1)) $db->addQuery($q3_1);
        if (!empty($q3)) $db->addQuery($q3);
        return $db->persistAll();
    }

    public function updateProductoAbleStatus($pId, $status) {
        $db = DataBaseAdmin::getInstance();
        $q = "UPDATE productos SET estado='$status' WHERE id_producto = '$pId' LIMIT 1";
        $db->addQuery($q);
        return $db->persistAll();
    }

    public function updateProductoActiveStatus($pId, $activo) {
        //sirve para soft borrado. Lo borraría poniendo activo=0 porque es info que no quiero perder:
        // algun pedido viejo podria tener ese producto.
        $db = DataBaseAdmin::getInstance();
        $q = "UPDATE productos SET activo='$activo' WHERE id_producto = '$pId' LIMIT 1";
        $db->addQuery($q);
        return $db->persistAll();
    }

    public function updateProductPopularStatus($pId, $estado) {
        $db = DataBaseAdmin::getInstance();
        $q = "UPDATE productos SET esPopular='$estado' WHERE id_producto = '$pId' LIMIT 1";
        $db->addQuery($q);
        return $db->persistAll();
    }
    
    public function removeProducto($pId) {
        //solamente se va a aplicar si el producto no tiene pedidos asociados
        $db = DataBaseAdmin::getInstance();
        $q = "DELETE FROM productos WHERE id_producto ='$pId' LIMIT 1";
        $q2 = "DELETE FROM productossabores WHERE id_producto ='$pId'";
        $q3 = "DELETE FROM productostoppings WHERE id_producto ='$pId'";
        $db->addQuery($q2);
        $db->addQuery($q3);
        $db->addQuery($q);
        return $db->persistAll();
    }

    public function removeSaboresDelProducto($pId) {
        //se quitan los sabores que tenia el producto asi no ocupa almacenamiento en la BS
        $db = DataBaseAdmin::getInstance();
        $q = "DELETE FROM productos_sabores WHERE id_producto ='$pId'";
        $db->addQuery($q);
        return $db->persistAll();
    }

    public function removeToppingsDelProducto($pId) {
        //se quitan los toppings que tenia el producto asi no ocupa almacenamiento en la BS
        $db = DataBaseAdmin::getInstance();
        $q = "DELETE FROM productos_toppings WHERE id_producto ='$pId'";
        $db->addQuery($q);
        return $db->persistAll();
    }
}
