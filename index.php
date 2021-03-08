<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
ini_set('error_reporting', E_ALL);

//abrir el archivo
if (file_exists("archivo.txt")){
    //leer archivo
   $jsonClientes = file_get_contents("archivo.txt");
   //convertir el json en array
    $aClientes = json_decode($jsonClientes, true);
} else {
    $aClientes = array();
}
//asigna el id a cada cliente.
$id = isset($_GET["id"])? $_GET["id"] : "";

if(isset($_GET["accion"]) && $_GET["accion"]=="eliminar"){
//elimina la imagen fisicamente
    $imgBorrar = "archivos/" . $aClientes[$id]["imagen"];
    if (file_exists($imgBorrar)){
        unlink($imgBorrar);
    }

    //eliminamos el cliente del array
    unset($aClientes[$id]);

    //actualizo el archivo con el nuevo array de clientes modificado
    file_put_contents("archivo.txt", json_encode($aClientes));
    // redirecciona al index.
    header("location: index.php");

}


if ($_POST) {
    $dni = $_POST["txtDni"];
    $nombre = $_POST["txtNombre"];
    $telefono = $_POST["txtTelefono"];
    $correo = $_POST["txtCorreo"];


    $nuevoNombre = "";
    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nuevoNombre = "$nombreAleatorio.$extension";
        if ($extension == ".jpg" || $extension == ".jpeg" || $extension == ".png"){}
        move_uploaded_file($archivo_tmp, "archivos/".$nuevoNombre);
    }

    if ($id >= 0){
        if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $nuevoNombre = $aClientes[$id]["imagen"];
        } else{
            if (file_exists($aClientes[$id]["imagen"])){
                unlink("archivos/".$nuevoNombre);
            }
        }
        //Actualizo cliente
        $aClientes[$id] = array("dni" => $dni,
                            "nombre" => $nombre,
                            "telefono" => $telefono,
                            "correo" => $correo,
                            "imagen" => $nuevoNombre);
                            $mensaje = "Actualizado correctamente";
    }else {
        //armar un array con los datos
        $aClientes[] = array("dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nuevoNombre);
            $mensaje = "Cargado correctamente";
    }
    //Codificar el array en Json
    $jsonCliente = json_encode($aClientes);
    //guardar el array en un archivo "archivo.txt"
    file_put_contents("archivo.txt", $jsonCliente);

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="css/estilos.css">


</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12 my-5 text-center">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="alert alert-success">


                        </div>
                        <div class="col-12 form-group">
                            <label for="txtDni">DNI: *</label>
                            <input type="text" id="txtDni" name="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["dni"] : ""?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtNombre">Nombre: *</label>
                            <input type="text" id="txtNombre" name="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["nombre"] : ""?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtTelefono">Teléfono:</label>
                            <input type="text" id="txtTelefono" name="txtTelefono" class="form-control" value="<?php echo isset($aClientes[$id])? $aClientes[$id]["telefono"] : ""?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Correo: *</label>
                            <input type="text" id="txtCorreo" name="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["correo"] : ""?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Archivo adjunto:</label>
                            <input type="file" id="archivo" name="archivo" class="form-control-file" accept=".jpg, .jpeg, .png">
                            <small class="d-block">Archivos admitidos: .jpg, .jpeg, .png</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-3">
                            <button type="submit" id="btnGuardar" name="btnGuardar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-6">
                <table class="table table-hover border">
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach($aClientes as $key => $cliente): ?>
                        <tr>
                            <td><img src="archivos/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>
                            <td><?php echo $cliente["nombre"]; ?></td>
                            <td><?php echo $cliente["correo"]; ?></td>
                            <td style="width: 110px;">
                                <a href="?id=<?php echo $key; ?>"><i class="fas fa-edit"></i></a>
                                <a href="?id=<?php echo $key; ?>&accion=eliminar"><i class="fas fa-trash-alt"></i></a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <a href="index.php"><i class="fas fa-plus"></i></a>
            </div>
        </div>
    </div>
</body>