<?php
include "config.php";

$data = json_decode(file_get_contents("php://input"));

$request = $data->request;

// Consulta todos los registros de la tabla orden
if($request == 'consulta_orden'){
  $sql = $con->prepare("SELECT orden.*, persona.nombre FROM orden INNER JOIN persona on (orden.persona_id = persona.id) WHERE fecha BETWEEN ? AND ? order by id desc");
  $fecha_desde=date('Y-m-d',strtotime('-29 days',strtotime("now"))) . " 00:00:00";
  $fecha_hasta=date("Y-m-d", strtotime("now")) . " 23:59:59";
  $sql->bindValue(1,$fecha_desde,PDO::PARAM_STR);
  $sql->bindValue(2,$fecha_hasta,PDO::PARAM_STR);
  $sql->execute();
  $response = array();
  $tipos = ['Latas CCN51','Seco CCN51 lbs','Latas Nacional','Seco Nacional','Latas Monilla'];

  while($row=$sql->fetch(PDO::FETCH_ASSOC)){
    $row['tipo_orden_id'] = $row['tipo_orden_id'] ? 'Venta' : 'Compra';
    $row['persona_id'] = $row['nombre'];
    $row['tipo'] = $tipos[$row['tipo']];
    $row['cantidad'] = $row['cantidad'] . ' lb';
    $response[] = $row;
  }

  echo json_encode($response);
  exit;
}

// insertar en la tabla Orden
if($request == 'insertar_orden'){
  $sql = $con->prepare("INSERT INTO orden(persona_id,tipo_orden_id,tipo,cantidad,precio,total,observacion,fecha) VALUES (?,?,?,?,?,?,?,?)");
  $persona_id = $data->persona_id;
  $tipo_orden_id = $data->tipo_orden_id;
  $tipo = $data->tipo;
  $cantidad = $data->cantidad;
  $precio = $data->precio;
  $total = $data->total;
  $observacion = $data->observacion;
  $fecha = $data->fecha;

  $sql->bindValue(1,$persona_id,PDO::PARAM_STR);
  $sql->bindValue(2,$tipo_orden_id,PDO::PARAM_INT);
  $sql->bindValue(3,$tipo,PDO::PARAM_INT);
  $sql->bindValue(4,$cantidad,PDO::PARAM_STR);
  $sql->bindValue(5,$precio,PDO::PARAM_STR);
  $sql->bindValue(6,$total,PDO::PARAM_STR);
  $sql->bindValue(7,$observacion,PDO::PARAM_STR);
  $sql->bindValue(8,$fecha,PDO::PARAM_STR);
  $response=$sql->execute();
  echo json_encode($response);

  exit;
}

// Consulta todos los registros de la tabla persona para el componente orden
if($request == 'consulta_personas_orden'){
  $sql = $con->prepare("SELECT * FROM persona ORDER BY id DESC");
  $sql->execute();

  $response = array();
  while($row=$sql->fetch(PDO::FETCH_ASSOC)){
    $response[] = $row;
  }

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
