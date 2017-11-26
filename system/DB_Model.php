<?php 
 // Se incluye el archivo de conexion de base de datos
 include 'core/ConexionDB.php';

 // Se crea la clase que ejecuta llama a las funciones de ejecución para interactuar con la Base de datos
 // Esta clase extiende a la clase db_model en el archivo db_model.php (hereda sus propiedades y metodos)

 class DB_Model extends ConexionDB {
  // Ya que la clase es generica, es importante poseer una variable que permitira identificar con que tabla se trabaja
  public $entity;
  // Almacena la informacion que sera enviada a la Base de datos
  public $data;

  private function get_fields_query($array_field=[]){

    $fields_query='';

    foreach ($array_field as $value) {
        
        $fields_query .= $value . ",";
    }

    return rtrim($fields_query,',');

  }

  public function get_login(array $dataArray){

    extract($dataArray);

    $array_field = $this->get_fields_query(["status", "user_name", "email"]);
    
    $sql = "SELECT $array_field FROM usuario WHERE user_name = '$user_name' AND password = '$password';";

    $response_query = $this->get_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "consulta exitosa");
      }else{
          return $this->response_json(-200, $response_query, "usuario o contraseña no son válidos");
      }

  }  

  public function get_menu($dataArray = array()){

    $array_field = $this->get_fields_query(["modulo.descripcion"]);
    
    extract($dataArray);

    $sql = '';

    $sql .= " SELECT DISTINCT ";  
    $sql .= " modulo_padre.descripcion AS padre_descripcion,";
    $sql .= " modulo_hijo.descripcion AS hijo_descripcion";
    $sql .= " FROM modulo AS modulo_padre";
    $sql .= " INNER JOIN modulo AS modulo_hijo ON modulo_padre.id_modulo = modulo_hijo.id_modulo_fk,";
    $sql .= " usuario";
    $sql .= " INNER JOIN perfil ON usuario.id_perfil_fk = perfil.id_perfil"; 
    $sql .= " INNER JOIN autorizacion ON autorizacion.id_perfil_fk = perfil.id_perfil";
    $sql .= " INNER JOIN modulo ON autorizacion.id_modulo_fk = modulo.id_modulo"; 
    $sql .= " WHERE usuario.`user_name` = '$user_name' AND autorizacion.acceso = 1 AND modulo.activo =1";//modulo activo
    $sql .= " order by  padre_descripcion";

    $response_query = $this->get_query($sql); 

    $array_opciones = [];

    foreach ($response_query as $key => $value) {
            
          $array_opciones[$key] = [ $value['padre_descripcion'] => $value['hijo_descripcion'] ] ;

    }

    $opciones_padres = [];

    $opciones_hijos = [];

    $opcion_padre='';
    $opcion_padre_aux='';

    $padre_hijo =[];

    foreach ($array_opciones as $key => $value) {

        if($opcion_padre == array_keys($value)[0] or empty($opcion_padre)){

          array_push($opciones_hijos, array_values($value)[0]);

        }else{

          $opciones_padres[array_keys($value)[0]] = $opciones_hijos;

          $padre_hijo[$opcion_padre] = $opciones_padres[array_keys($value)[0]];

          $opciones_hijos = [];

          array_push($opciones_hijos, array_values($value)[0]);

          $opcion_padre_aux = array_keys($value)[0];
          
        }

        $opcion_padre = array_keys($value)[0];
    } 

    $opciones_padres[array_keys($value)[0]] = $opciones_hijos;
    $padre_hijo[$opcion_padre_aux] = $opciones_padres[array_keys($value)[0]];

      if ($response_query) {
          return $this->response_json(200, $padre_hijo, "consulta exitosa");
      }else{
          return $this->response_json(-200, $padre_hijo, "no se pudo realizar la consulta");
      }

  }

  private function fields_query(array $fields){

    $fields_query = '';

    foreach ($fields as $key => $value) {
        
        $fields_query .= $key . ",";
    }

    return rtrim($fields_query,',');

  }

  private function values_query(array $values){

    $values_query = '';

    foreach ($values as $value) {
        
        $values_query .= '\''. $value .'\''. ",";
    }

    return rtrim($values_query,',');

  }

  public function registrar_cliente(array $dataArray){

    $sql = 'INSERT INTO CLIENTE ('. $this->fields_query($dataArray) .') VALUES ('. $this->values_query($dataArray) .')';

    $response_query = $this->set_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "registro exitoso");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar el registro");
      }

  }  

  public function consultar_cliente(){ 

    $sql  = ' SELECT cliente.nombre_cliente AS nombre,';
    $sql .= ' cliente.apellido_cliente AS apellido,'; 
    $sql .= " concat_ws('-', cliente.pre_doc_cliente , cliente.doc_cliente ) AS documento,";
    $sql .= ' cliente.telf_cliente AS telefono,';
    $sql .= ' cliente.otro_telf_cliente AS otro_tefl,';
    $sql .= ' cliente.email_cliente AS email,';
    $sql .= ' cliente.tipo_cliente,';
    $sql .= ' cliente.rason_social_cliente AS rason_social,';
    $sql .= ' cliente.direccion_cliente AS direccion';
    $sql .= ' FROM cliente';
  
    return json_encode( ['data' => $this->get_query($sql)] );

  }  

  public function modificar_cliente(array $dataArray){ 

    extract($dataArray);

    $sql  = " UPDATE `cliente` SET";
    $sql .= " nombre_cliente = '$nombre_cliente',";
    $sql .= " apellido_cliente = '$apellido_cliente',";
    $sql .= " pre_doc_cliente = '$pre_doc_cliente',"; 
    $sql .= " doc_cliente = '$new_doc_cliente',";
    $sql .= " email_cliente = '$email_cliente',";
    $sql .= " rason_social_cliente = '$rason_social_cliente',";
    $sql .= " direccion_cliente = '$direccion_cliente',";
    $sql .= " telf_cliente = '$telf_cliente',";
    $sql .= " otro_telf_cliente = '$otro_telf_cliente',";
    $sql .= " tipo_cliente = '$tipo_cliente'";
    $sql .= " WHERE doc_cliente = '$doc_cliente';";

    $response_query = $this->set_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "Actualizacion exitosa");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar el registro");
      }

  }

  public function eliminar_cliente(array $dataArray){ 

    extract($dataArray);

    $sql  = " DELETE FROM `cliente`";
    $sql .= " WHERE doc_cliente = '$documento';";

    $response_query = $this->set_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "Registro Eliminado exitosamente");
      }else{
          return $this->response_json(-200, $response_query, "No se pudo realizar la accion");
      }

  }

  public function registrar_Stock_MP(array $dataArray){

    $sql_insert = 'INSERT INTO stock ('. $this->fields_query($dataArray) .') VALUES ('. $this->values_query($dataArray) .')';

    extract($dataArray);

    $sql_material_actual  = 'SELECT material.id_material, material.tipo_material, material.cantidad_disponible';
    $sql_material_actual .= " FROM material WHERE tipo_material ='$tipo_material'";

    $cantidad_disponible = $this->get_query($sql_material_actual);

    $res_tipo_material = $cantidad_disponible[0]['tipo_material'];
    $res_cantidad_disponible = $cantidad_disponible[0]['cantidad_disponible'];

    $cantidad_actual  = ($unidades * $cantidad_material) + $res_cantidad_disponible;

    $sql_material  = 'UPDATE material set';
    $sql_material .= " cantidad_disponible = $cantidad_actual";
    $sql_material .= " where tipo_material = '$tipo_material'";

    $update_succes = $this->set_query($sql_material);

    $response_query = $this->set_query($sql_insert);
      
      if ($response_query &&  $update_succes) {
          return $this->response_json(200, $response_query, "registro exitoso");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar el registro");
      }

  }

  public function consultar_Stock_MP(){

    $sql =  ' SELECT stock.tipo_material,';
    $sql .= ' stock.id_stock,';
    $sql .= ' stock.cantidad_material,';
    $sql .= ' stock.unidades,';
    $sql .= ' stock.precio,';
    $sql .= ' stock.fecha_registro,';
    $sql .= ' stock.descripcion';
    $sql .= ' FROM stock';

    $response_query = $this->get_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "consulta exitosa");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar la consulta");
      }

  }  

  public function modificar_Stock_MP(array $dataArray){ 

    extract($dataArray);
  
    $sql  = " UPDATE `stock` SET";
    $sql .= " tipo_material = '$tipo_material',";
    $sql .= " unidades = '$unidades',"; 
    $sql .= " precio = '$precio',";
    $sql .= " fecha_registro = '$fecha_registro',";
    $sql .= " descripcion = '$descripcion'";
    $sql .= " WHERE id_stock = '$id_stock';";

    $response_query = $this->set_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "Actualizacion exitosa");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar el registro");
      }

  }

  public function eliminar_Stock_MP(array $dataArray){ 

    extract($dataArray);

    $sql  = " DELETE FROM `stock`";
    $sql .= " WHERE id_stock = '$id_stock';";

    $response_query = $this->set_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "Registro Eliminado exitosamente");
      }else{
          return $this->response_json(-200, $response_query, "No se pudo realizar la accion");
      }

  }

  public function registrar_proveedor(array $dataArray){

    $sql = 'INSERT INTO proveedor ('. $this->fields_query($dataArray) .') VALUES ('. $this->values_query($dataArray) .')';

    $response_query = $this->set_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "registro exitoso");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar el registro");
      }

  } 

  public function consultar_proveedor(){

    $sql  = ' SELECT ';
    $sql .= ' proveedor.cod_proveedor,'; 
    $sql .= ' proveedor.razon_social_proveedor,';
    $sql .= ' proveedor.nombre_contacto,';
    $sql .= ' proveedor.pre_rif,';
    $sql .= ' proveedor.rif,';
    $sql .= ' proveedor.telf,';
    $sql .= ' proveedor.otro_telf,';
    $sql .= ' proveedor.email,';
    $sql .= ' proveedor.direccion,';
    $sql .= ' proveedor.tipo_proveedor,';
    $sql .= ' proveedor.tipo_actividad';
    $sql .= ' FROM proveedor';
  
    return json_encode( ['data' => $this->get_query($sql)] );

  } 

  public function consultar_disponible_MP(){

    $sql  = 'SELECT material.id_material, material.tipo_material, material.cantidad_disponible';
    $sql  .= " FROM material";

    $response_query = $this->get_query($sql);
      
      if ($response_query) {
          return $this->response_json(200, $response_query, "consula exitosa");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar la consulta");
      }

  }

  public function actualizar_MP(array $dataArray){

    extract($dataArray);

    $sql_rollo  = " UPDATE `material` SET";
    $sql_rollo .= " cantidad_disponible = '$cantidad_rollo'";
    $sql_rollo .= " WHERE tipo_material = 'rollo';";

    $sql_carton  = " UPDATE `material` SET";
    $sql_carton .= " cantidad_disponible = '$cantidad_carton'";
    $sql_carton .= " WHERE tipo_material = 'carton';";

    $sql_goma  = " UPDATE `material` SET";
    $sql_goma .= " cantidad_disponible = '$cantidad_goma'";
    $sql_goma .= " WHERE tipo_material = 'goma';";

    $sql_resma  = " UPDATE `material` SET";
    $sql_resma .= " cantidad_disponible = '$cantidad_resma'";
    $sql_resma .= " WHERE tipo_material = 'resma';";

    $sql_insert  = 'INSERT INTO orden_produccion ( producto, cantidad_producto, fecha_orden )';
    $sql_insert .= " VALUES ( '$producto', '$cantidad_producto', NOW() )";

    $response_rollo  = $this->set_query($sql_rollo);
    $response_carton = $this->set_query($sql_carton);
    $response_goma   = $this->set_query($sql_goma);
    $response_resma  = $this->set_query($sql_resma);
    $response_insert = $this->set_query($sql_insert);
      
      if ($response_rollo && $response_carton && $response_goma && $response_resma && $response_insert) {
          return $this->response_json(200, null, "registro exitoso");
      }else{
          return $this->response_json(-200, $response_query, "no se pudo realizar el registro");
      }

  }

  public function consultar_Stock_Disponible(){

    $sql  = ' SELECT ';
    $sql .= ' orden_produccion.cod_orden,'; 
    $sql .= ' orden_produccion.producto,';
    $sql .= ' orden_produccion.cantidad_producto,';
    $sql .= ' orden_produccion.fecha_orden';
    $sql .= ' FROM orden_produccion';
  
    return json_encode( ['data' => $this->get_query($sql)] );

  } 


  protected function no_response(){

      $this->response_json(-200, false, "no es una peticion valida");
  }

  protected function response_json($status, $response, $mensaje) {
    
    header("HTTP/1.1 $status $mensaje");
    header("Content-Type: application/json; charset=UTF-8");

    $response = [ 
                  'rc'      => $status, 
                  'data'    => $response,
                  'mensaje' => $mensaje
                ];

    return json_encode($response, JSON_PRETTY_PRINT);

  }


 }
