<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();

$con = $db->conectar();

$id = isset ($_GET['id']) ? $_GET['id']: '';
$token = isset ($_GET['token']) ? $_GET['token']: '';

if($id== '' || $token==''){
    echo 'Error al procesar la petición';
    exit;
}else{

    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);
    if($token==$token_tmp){

        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo=1"); // Corregido
        $sql->execute([$id]);
        if($sql->fetchColumn()>0){
            $sql = $con->prepare("SELECT nombre, descripcion, categoria, precio, informacion FROM productos WHERE id=? AND activo=1 LIMIT 1");
            $sql->execute([$id]);
            $row= $sql->fetch(PDO::FETCH_ASSOC);
            $precio = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['nombre'];
            $dir_images = 'Imagenes/productos/'.$id.'/';

            $rutaImg = $dir_images.'principal.jpg';

            if(!file_exists($rutaImg)){
                $rutaImg = 'Imagenes/sin-foto.jpg';
            }
            $imagenes = array();
            if(file_exists($dir_images)){

            
            $dir = dir($dir_images);

            while(($archivo = $dir->read()) != false){
                if($archivo != 'principal.jpg' && (strpos($archivo, 'jpg')) ||(strpos($archivo, 'jpeg'))){
                    $imagenes[] = $dir_images.$archivo;
                }
            }
            $dir->close();
        }
        }
        
    }else{
        echo 'Error al procesar la petición';
        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="CSS/estilos.css">
    <title>Métrica-Solution</title>
</head>
<body>
<header>

  <div class="navbar navbar-expand-lg navbar-dark bg-dark" >
    <div class="container-fluid">
      <a href="index.php" class="navbar-brand" class="hola">
       <!--<img src="https://i.ibb.co/LZMBkHT/circuito-electrico-1.png" alt="Circuito-Electrico" width='55' > -->
       <!--<img src="https://i.ibb.co/NYfTG0Y/circuito-electrico-2.png" alt="Circuito-Electrico" width='55' >-->
       <img src="https://i.ibb.co/h2tQSvB/circuito-electricooo.png"alt="Circuito-Electrico" width='55' >
        <strong>Metrica Solution </strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarHeader">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
              <a href="#" class="nav-link active">Productos</a>
              </li>
              <li class="nav-item">
              <a href="#" class="nav-link">Contacto</a>
                </li>
            </ul>
            <a href="checkout.php" class="btn btn-outline-primary" >
                Carrito<span id="num_cart" class="badge bg-secondary"><?php echo $num_cart ?></span>
            </a>
    </div>
  </div>
</header>
<main>
    <div class="container">
       <div class="row">
            <div class="col-md-6 order-md-1">
            <div id="carouselImages" class="carousel slide" data-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100" src="<?php echo $rutaImg; ?>" >
    </div>
    <?php foreach($imagenes as $img) { ?>
    <div class="carousel-item">
    <img class="d-block w-100" src="<?php echo $img; ?>">
    </div>
    <?php } ?>
  </div>
  <a class="carousel-control-prev" href="carouselImages" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="carouselImages" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

             
            </div>
            <div class="col-md-6 order-md-2">
                <h2><?php echo $row['nombre']; ?></h2>
                <h2>$<?php echo $row['precio']; ?></h2>
                <p class = "lead"><?php echo $row['categoria']  ?> </p>
                <p class="lead"><?php echo $descripcion ?></p>
                <p class="lead"><?php  echo $row['informacion']?></p> 
                <div class="d-grid gap-3 col-10 mx-auto">
                    <button class="btn btn-primary" type="button">Comprar Ahora</button>
                    <button class="btn btn-outline-success" type="button" onclick="addProducto(<?php echo $id;?>, '<?php echo $token_tmp ?>')">Agregar al Carrito</button>

                </div>
                </div>
       </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script>
    function addProducto(id, token){
        let url = 'clases/carrito.php';
        let formData = new FormData();
        formData.append('id', id);
        formData.append('token', token);

        fetch(url,{
            method: 'POST',
            body: formData,
            mode:'cors'
        })
        .then(response => response.json())
        .then(data => {
            if(data.ok){
                let elemento = document.getElementById("num_cart");
                elemento.innerHTML = data.numero;
            } else {
                console.error('No se pudo agregar el producto al carrito.');
            }
        })
        .catch(error => {
            console.error('Se produjo un error al realizar la solicitud:', error);
        });
    }
</script>
</body>
</html>

