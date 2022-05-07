
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCOT</title><!-- Sistema de Control de Tiempos de Comida -->
</head>
<body>
    <nav>
        <div>
            <a href="<?php echo (!isset($_SESSION['verificar']))?'/login':'/dashboard' ?>"><?php echo (!isset($_SESSION['verificar']))?'Login':'Panel de control' ?></a>
            
        </div>
    </nav>
</body>
</html>