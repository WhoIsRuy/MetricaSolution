<?php
    require 'config/config.php';
    require 'config/database.php';
    $db = new Database();

    $con = $db->conectar();

    $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

    $lista_carrito = array();

    if($productos != null){
        foreach($productos as $clave =>$cantidad){
            $sql = $con->prepare("SELECT id, nombre, precio, $cantidad AS cantidad FROM productos WHERE id = ? AND activo=1"); // Corregido
            $sql->execute([$clave]);
            $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
        }
    } else{
        header("Location : index.php");
        exit;
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
              <a href="index.php" class="nav-link active">Productos</a>
              </li>
              <li class="nav-item">
              <a href="#" class="nav-link">Contacto</a>
                </li>
            </ul>
            <a href="carrito.php" class="btn btn-outline-primary" >
                Carrito<span id="num_cart" class="badge bg-secondary"><?php echo $num_cart ?></span>
            </a>
    </div>
  </div>
</header>
<main>
    <div class="container">

        <div class="row">
            <div class="col-6">
                <h4>Detalles de pago</h4>
                <div id="paypal-button-container"></div>
            </div>
            <div class="col-6">

        <div class="table-responsive">
            <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Subtotal</th>
                            <th></th>      
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($lista_carrito == null){
                            echo '<tr><td colspan=5 class="text-center><b>Lista vacia</b><td></tr>';
                        }else{
                            $total = 0;
                            foreach($lista_carrito as $producto){
                                $_id = $producto['id'];
                                $nombre = $producto['nombre'];
                                $precio = $producto['precio'];
                                $cantidad = $producto['cantidad'];
                                $subtotal = $cantidad * $precio;
                                $total += $subtotal;
                                ?>
                          
                        <tr>
                            <td><?php echo $nombre ?></td>
                        <td>
                            <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]">
                            <?php echo '$'. number_format($subtotal,2,'.',',');?>
                            </div>
                        </td>
                        </tr>
                        <?php }
                        ?>
                        <tr>
                            <td colspan="3" class="h3">Total</td>
                            <td colspan="2">
                                <p class="h3" id="total"><?php echo '$'. number_format($total,2,'.',','); ?></p>
                            </td>
                        </tr>
                    </tbody>
                    <?php }?>
            </table>
        </div>
       
    </div>
    </div>
    </div>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&currency= <?php echo CURRENCY; ?>"></script>

<script>
    paypal.Buttons({
        style:{
            color:'blue',
            shape: 'pill',
            label: 'pay'
        }, 
        createOrder: function(data, actions){
            return actions.order.create({
                purchase_units:[{
                    amount: {
                        value: 100
                    }
                }]
            });
        },
        onApprove: function(data, actions){
            actions.order.capture().then(function(detalles){
                console.log(detalles);
            });
        },
        onCancel: function(data){
            alert("Pago cancelado")
            console.log(data);
        }

    }).render('#paypal-button-container')

</script>
</body>
</html>