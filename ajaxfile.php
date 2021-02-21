<?php
include "config.php";

$data = json_decode(file_get_contents("php://input"));

$request = $data->request;

// Consulta todos los registros de la tabla orden
if($request == 'consulta_orden'){
  $sql = $con->prepare("SELECT orden.*, persona.nombre, persona.id as persona_id FROM orden INNER JOIN persona on (orden.persona_id = persona.id) WHERE fecha BETWEEN ? AND ? order by id desc");
  $fecha_desde=date('Y-m-d',strtotime('-29 days',strtotime("now"))) . " 00:00:00";
  $fecha_hasta=date("Y-m-d", strtotime("now")) . " 23:59:59";
  $sql->bindValue(1,$fecha_desde,PDO::PARAM_STR);
  $sql->bindValue(2,$fecha_hasta,PDO::PARAM_STR);
  $sql->execute();
  $response = array();
  $tipos = ['Latas CCN51','Seco CCN51 lbs','Latas Nacional','Seco Nacional','Latas Monilla'];

  while($row=$sql->fetch(PDO::FETCH_ASSOC)){
    $row['tipo_orden_id'] = $row['tipo_orden_id'] ? 'Venta' : 'Compra';
    $row['nombre'] = $row['nombre'];
    $row['tipo'] = $tipos[$row['tipo']];
    $row['cantidad'] = $row['cantidad'] . ' lb';
    $response[] = $row;
  }

  echo json_encode($response);
  exit;
}

// insertar en la tabla Orden
if($request == 'insertar_orden'){
  $sql = $con->prepare("INSERT INTO orden(persona_id,tipo_orden_id,tipo,cantidad,humedad,precio,total,observacion,fecha) VALUES (?,?,?,?,?,?,?,?,?)");
  $persona_id = $data->persona_id;
  $tipo_orden_id = $data->tipo_orden_id;
  $tipo = $data->tipo;
  $cantidad = $data->cantidad;
  $humedad = $data->humedad?:null;
  $precio = $data->precio;
  $total = $data->total;
  $observacion = $data->observacion?:null;
  $fecha = $data->fecha;

  $sql->bindValue(1,$persona_id,PDO::PARAM_INT);
  $sql->bindValue(2,$tipo_orden_id,PDO::PARAM_INT);
  $sql->bindValue(3,$tipo,PDO::PARAM_INT);
  $sql->bindValue(4,$cantidad,PDO::PARAM_STR);
  $sql->bindValue(5,$humedad,PDO::PARAM_INT);
  $sql->bindValue(6,$precio,PDO::PARAM_STR);
  $sql->bindValue(7,$total,PDO::PARAM_STR);
  $sql->bindValue(8,$observacion,PDO::PARAM_STR);
  $sql->bindValue(9,$fecha,PDO::PARAM_STR);
  $sql->execute();
  //Devuelve el id del ultimo registro ingresado
  $response=$con->lastInsertId();
  echo json_encode($response);

  exit;
}

// editar en la tabla Orden
if($request == 'editar_orden') {
  $sql = $con->prepare("UPDATE orden SET persona_id=?,tipo_orden_id=?,tipo=?,cantidad=?,humedad=?,precio=?,total=?,observacion=? WHERE id=?");
  $orden_id = $data->orden_id;
  $persona_id = $data->persona_id;
  $tipo_orden_id = $data->tipo_orden_id;
  $tipo = $data->tipo;
  $cantidad = $data->cantidad;
  $humedad = $data->humedad?:null;
  $precio = $data->precio;
  $total = $data->total;
  $observacion = $data->observacion?:null;

  $sql->bindValue(1,$persona_id,PDO::PARAM_INT);
  $sql->bindValue(2,$tipo_orden_id,PDO::PARAM_INT);
  $sql->bindValue(3,$tipo,PDO::PARAM_INT);
  $sql->bindValue(4,$cantidad,PDO::PARAM_STR);
  $sql->bindValue(5,$humedad,PDO::PARAM_INT);
  $sql->bindValue(6,$precio,PDO::PARAM_STR);
  $sql->bindValue(7,$total,PDO::PARAM_STR);
  $sql->bindValue(8,$observacion,PDO::PARAM_STR);
  $sql->bindValue(9,$orden_id,PDO::PARAM_INT);
  $response=$sql->execute();

  echo json_encode($response);
  exit;
}

// Se elimina la orden seleccionada
if($request == 'eliminar_orden') {
  $orden_id = $data->orden_id;
  $sql = $con->prepare("DELETE FROM orden WHERE id=?");
  $sql->bindValue(1,$orden_id,PDO::PARAM_INT);
  $response=$sql->execute();
  echo json_encode($response);
  exit;
}

// Consulta todos los registros de la tabla persona para el componente orden
if($request == 'consulta_personas_select'){
  $sql = $con->prepare("SELECT * FROM persona ORDER BY id DESC");
  $sql->execute();

  $response = array();
  while($row=$sql->fetch(PDO::FETCH_ASSOC)){
    $response[] = $row;
  }

  echo json_encode($response);
  exit;
}

// Consulta todos los registros de la tabla persona
if($request == 'consulta_personas'){
  $sql = $con->prepare("SELECT * FROM persona ORDER BY id DESC");
  $sql->execute();

  $response = array();
  while($row=$sql->fetch(PDO::FETCH_ASSOC)){
    $response[] = $row;
  }

  echo json_encode($response);
  exit;
}

// insertar en la tabla Persona
if($request == 'insertar_persona'){
  $sql = $con->prepare("INSERT INTO persona(nombre,cedula,telefono,mail,hectarea,direccion,parcela) VALUES (?,?,?,?,?,?,?)");
  $nombre = $data->nombre;
  $cedula = $data->cedula;
  $telefono = $data->telefono?:null;
  $mail = $data->mail?:null;
  $hectarea = $data->hectarea?:null;
  $direccion = $data->direccion?:null;
  $parcela = $data->parcela?:null;

  $sql->bindValue(1,$nombre,PDO::PARAM_STR);
  $sql->bindValue(2,$cedula,PDO::PARAM_INT);
  $sql->bindValue(3,$telefono,PDO::PARAM_INT);
  $sql->bindValue(4,$mail,PDO::PARAM_STR);
  $sql->bindValue(5,$hectarea,PDO::PARAM_STR);
  $sql->bindValue(6,$direccion,PDO::PARAM_STR);
  $sql->bindValue(7,$parcela,PDO::PARAM_STR);
  $sql->execute();
  //Devuelve el id del ultimo registro ingresado
  $response=$con->lastInsertId();

  echo json_encode($response);
  exit;
}

// editar en la tabla Persona
if($request == 'editar_persona') {
  $sql = $con->prepare("UPDATE persona SET nombre=?,cedula=?,telefono=?,mail=?,hectarea=?,direccion=?,parcela=? WHERE id=?");
  $persona_id = $data->persona_id;
  $nombre = $data->nombre;
  $cedula = $data->cedula;
  $telefono = $data->telefono?:null;
  $mail = $data->mail?:null;
  $hectarea = $data->hectarea?:null;
  $direccion = $data->direccion?:null;
  $parcela = $data->parcela?:null;

  $sql->bindValue(1,$nombre,PDO::PARAM_STR);
  $sql->bindValue(2,$cedula,PDO::PARAM_INT);
  $sql->bindValue(3,$telefono,PDO::PARAM_INT);
  $sql->bindValue(4,$mail,PDO::PARAM_STR);
  $sql->bindValue(5,$hectarea,PDO::PARAM_STR);
  $sql->bindValue(6,$direccion,PDO::PARAM_STR);
  $sql->bindValue(7,$parcela,PDO::PARAM_STR);
  $sql->bindValue(8,$persona_id,PDO::PARAM_INT);
  $response=$sql->execute();

  echo json_encode($response);
  exit;
}

// Consulta todos los registros de la tabla persona para el componente orden
if($request == 'eliminar_persona'){
  /*$persona_id = $data->persona_id;
  $respuesta = mysqli_query($con,"update persona set borrado = 1 where id =".$persona_id);

  echo json_encode($respuesta);*/
  exit;
}

// Consulta todos los registros de la tabla cuenta
if($request == 'consulta_cuenta') {
  $sql = $con->prepare("SELECT cuenta.*, persona.nombre, persona.id as persona_id FROM cuenta INNER JOIN persona on (cuenta.persona_id = persona.id) order by id desc");
  $sql->execute();
  $response = array();

  while($row=$sql->fetch(PDO::FETCH_ASSOC)){
    $response[] = $row;
  }

  echo json_encode($response);
  exit;
}

// insertar en la tabla Cuenta
if($request == 'insertar_cuenta'){
  $sql = $con->prepare("INSERT INTO cuenta(persona_id,numero,nombre_banco) VALUES (?,?,?)");
  $persona_id = $data->persona_id;
  $numero = $data->numero;
  $nombre_banco = $data->nombre_banco;
  $sql->bindValue(1,$persona_id,PDO::PARAM_INT);
  $sql->bindValue(2,$numero,PDO::PARAM_INT);
  $sql->bindValue(3,$nombre_banco,PDO::PARAM_STR);
  $sql->execute();
  //Devuelve el id del ultimo registro ingresado
  $response=$con->lastInsertId();
  echo json_encode($response);

  exit;
}

// editar en la tabla Cuenta
if($request == 'editar_cuenta') {
  $sql = $con->prepare("UPDATE cuenta SET persona_id=?,numero=?,nombre_banco=? WHERE id=?");
  $cuenta_id = $data->cuenta_id;
  $persona_id = $data->persona_id;
  $numero = $data->numero;
  $nombre_banco = $data->nombre_banco;
  $sql->bindValue(1,$persona_id,PDO::PARAM_INT);
  $sql->bindValue(2,$numero,PDO::PARAM_INT);
  $sql->bindValue(3,$nombre_banco,PDO::PARAM_STR);
  $sql->bindValue(4,$cuenta_id,PDO::PARAM_INT);
  $response=$sql->execute();

  echo json_encode($response);
  exit;
}

// Se elimina la cuenta seleccionada
if($request == 'eliminar_cuenta') {
  $cuenta_id = $data->cuenta_id;
  $sql = $con->prepare("DELETE FROM cuenta WHERE id=?");
  $sql->bindValue(1,$cuenta_id,PDO::PARAM_INT);
  $response=$sql->execute();
  echo json_encode($response);
  exit;
}

// Consulta todos los registros de la tabla otras_cuentas
if($request == 'consulta_otras_cuenta') {
  $sql = $con->prepare("SELECT otras_cuentas.*, persona.nombre, persona.id as persona_id FROM otras_cuentas INNER JOIN persona on (otras_cuentas.persona_id = persona.id) WHERE fecha BETWEEN ? AND ? order by id desc");
  $fecha_desde=date('Y-m-d',strtotime('-29 days',strtotime("now"))) . " 00:00:00";
  $fecha_hasta=date("Y-m-d", strtotime("now")) . " 23:59:59";
  $sql->bindValue(1,$fecha_desde,PDO::PARAM_STR);
  $sql->bindValue(2,$fecha_hasta,PDO::PARAM_STR);
  $sql->execute();
  $response = array();
  while($row=$sql->fetch(PDO::FETCH_ASSOC)){
    $row['tipo_orden_nombre'] = $row['tipo_orden_id'] ? ($row['tipo_orden_id'] == 1) ? 'Venta' : 'Gasto' : 'Compra';
    $response[] = $row;
  }

  echo json_encode($response);
  exit;
}

// insertar en la tabla Otras Cuentas
if($request == 'insertar_otras_cuenta') {
  $sql = $con->prepare("INSERT INTO otras_cuentas(persona_id,tipo_orden_id,cantidad,observacion,fecha) VALUES (?,?,?,?,?)");
  $persona_id = $data->persona_id;
  $tipo_orden_id = $data->tipo_orden_id;
  $cantidad = $data->cantidad;
  $observacion = $data->observacion;
  $fecha = $data->fecha;
  $sql->bindValue(1,$persona_id,PDO::PARAM_INT);
  $sql->bindValue(2,$tipo_orden_id,PDO::PARAM_INT);
  $sql->bindValue(3,$cantidad,PDO::PARAM_STR);
  $sql->bindValue(4,$observacion,PDO::PARAM_STR);
  $sql->bindValue(5,$fecha,PDO::PARAM_STR);
  $sql->execute();
  //Devuelve el id del ultimo registro ingresado
  $response=$con->lastInsertId();
  echo json_encode($response);

  exit;
}

// editar en la tabla Otras Cuenta
if($request == 'editar_otras_cuenta') {
  $sql = $con->prepare("UPDATE otras_cuentas SET persona_id=?,tipo_orden_id=?,cantidad=?,observacion=? WHERE id=?");
  $otras_cuenta_id = $data->otras_cuenta_id;
  $persona_id = $data->persona_id;
  $tipo_orden_id = $data->tipo_orden_id;
  $cantidad = $data->cantidad;
  $observacion = $data->observacion;
  $sql->bindValue(1,$persona_id,PDO::PARAM_INT);
  $sql->bindValue(2,$tipo_orden_id,PDO::PARAM_INT);
  $sql->bindValue(3,$cantidad,PDO::PARAM_STR);
  $sql->bindValue(4,$observacion,PDO::PARAM_STR);
  $sql->bindValue(5,$otras_cuenta_id,PDO::PARAM_INT);
  $response=$sql->execute();

  echo json_encode($response);
  exit;
}

// Se elimina la otras cuenta seleccionada
if($request == 'eliminar_otras_cuenta') {
  $otras_cuenta_id = $data->otras_cuenta_id;
  $sql = $con->prepare("DELETE FROM otras_cuentas WHERE id=?");
  $sql->bindValue(1,$otras_cuenta_id,PDO::PARAM_INT);
  $response=$sql->execute();
  echo json_encode($response);
  exit;
}
