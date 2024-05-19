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
        <div class="table-responsive">
            <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
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
                            <td><?php echo '$'. number_format($precio,2,'.',',') ?></td>
                            <td><input type="number" min="1" max="200" step="1" value="<?php echo $cantidad; ?>" size="5" id="cantidad_<?php echo $_id ?>" onchange="actualizaCantidad(this.value, <?php echo $_id?>)">
                        </td>
                        <td>
                            <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]">
                            <?php echo '$'. number_format($subtotal,2,'.',',');?>
                            </div>
                        </td>
                        <td>
                            <a href="#" id="eliminar" class="btn btn-outline-warning btn-sm" data-bs-id="<?php echo $_id ?>" data-bs-toggle="modal" data-bs-target="#eliminarModal">
                           Eliminar </a></td>
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
        <?php if($lista_carrito != null){ ?>
    <div class="row">
        <div class="col-md-5 offset-md-7 d-grid gap-2">
            <a href="pago.php" class="btn btn-primary btn-lg ">Realizar pago</a>
        </div>
    </div>
    <?php } ?>
    </div>
</main>

<div class="modal fade" id="eliminarModal" tabindex="-1" role="dialog" aria-labelledby="eliminarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eliminarModalLabel">Alerta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Desea elminar el producto de la lista?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-danger" id="btn-eliminar" onclick="eliminar()">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script>
    let eliminaModal = document.getElementById('eliminarModal')
    eliminaModal.addEventListener('show.bs.modal', function(event){
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        let buttonElimina = eliminaModal.querySelector('.modal-footer #btn-eliminar')
        buttonElimina.value=id
    })

    function actualizaCantidad(cantidad, id){
        let url = 'clases/actualizarCarrito.php';
        let formData = new FormData();
        formData.append('id', id);
        formData.append('action', 'agregar');
        formData.append('cantidad', cantidad);

        fetch(url,{
            method: 'POST',
            body: formData,
            mode:'cors'
        })
        .then(response => response.json())
        .then(data => {
            if(data.ok){

                let divSubtotal = document.getElementById('subtotal_' + id);
                divSubtotal.innerHTML = data.sub;

                let total = 0.00;
                let list = document.getElementsByName('subtotal[]'); 

                for(let i=0; i<list.length; i++){
                    total += parseFloat(list[i].innerHTML.replace(/[$,]/g,''))
                }
                total = new Intl.NumberFormat('es-MX',{
                    minimumFractionDigits: 2
                }).format(total)
                document.getElementById('total').innerHTML = '<?php echo '$'; ?>'+ total
                } else {
                console.error('No se pudo agregar el producto al carrito.');
            }
        })
        .catch(error => {
            console.error('Se produjo un error al realizar la solicitud:', error);
        });
    }

    function eliminar(){

        let botonElimina = document.getElementById('btn-eliminar');
        let id =botonElimina.value 

        let url = 'clases/actualizarCarrito.php';
        let formData = new FormData();
        formData.append('id', id);
        formData.append('action', 'eliminar');

        fetch(url,{
            method: 'POST',
            body: formData,
            mode:'cors'
        })
        .then(response => response.json())
        .then(data => {
            if(data.ok){
                location.reload()
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