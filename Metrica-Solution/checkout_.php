<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métrica-Solution</title>

    <script src="https://www.paypal.com/sdk/js?client-id=AeO-6d0LMVGjDS1g5UYtsA-Z-V6zBvl6tqepDGBwVWhapjCJ191Pl_CU_AzJzHuXTgvTKLAlBro-TSCb&currency=MXN"></script>
</head>
<body>
<div id="paypal-button-container"></div>





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