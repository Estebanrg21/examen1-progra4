<?php
session_start();
if($_SESSION['verification']){
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCOT:Login</title>
</head>
<body>
    <form action="#" method="POST">
		<label>Email</label><input type="email" name="email"><br><br>
		<label>Clave</label><input type="password" name="password"><br><br>
		<input type="submit" value="Ingresar">
	</form>
	<?php
        require_once "models/User.php";
		if(isset($_POST['email']) && isset($_POST['password'])){
			$user = new User($_POST['email'],$_POST['password']);
            if($user->login()){
                session_start();
                $_SESSION['user']=$user->email;
                $_SESSION['verification']=true;
                header("Location: dashboard.php");
            }else{
                echo "No login";
            }
		}
	?>
</body>
</html>