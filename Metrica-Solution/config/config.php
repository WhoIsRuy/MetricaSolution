<?php
    define ("CLIENT_ID", "AeO-6d0LMVGjDS1g5UYtsA-Z-V6zBvl6tqepDGBwVWhapjCJ191Pl_CU_AzJzHuXTgvTKLAlBro-TSCb");
    define("CURRENCY", "MXN");
    define ("KEY_TOKEN", "RuYFr030530jur0s0l3mn3m3nt3Qu3M151nt3nc1oN3sn0sONBu3n4s#?*");

    session_start();

    $num_cart = 0;
    if(isset($_SESSION['carrito']['productos'])){
        $num_cart = count($_SESSION['carrito']['productos']);
    }

?>