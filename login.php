<?php
// comprobando la versión mínima de PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Lo sentimos, el inicio de sesión simple de PHP no se ejecuta en una versión de PHP inferior a 5.3.7.");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // si está utilizando PHP 5.3 o PHP 5.4, debe incluir password_api_compatibility_library.php
    // (esta biblioteca agrega las funciones de hashing de contraseñas de PHP 5.5 a versiones anteriores de PHP)
    require_once("libraries/password_compatibility_library.php");
}

// incluir las config/constantes para la conexión de la base de datos
require_once("config/db.php");

// cargar la clase de inicio de sesión
require_once("classes/Login.php");

// crear un objeto de inicio de sesión. cuando se crea este objeto, hará todas las cosas de inicio/cierre de sesión automáticamente
// por lo que esta sola línea maneja todo el proceso de inicio de sesión. en consecuencia, simplemente puede -->
$login = new Login();

// <-- preguntar si estamos registrados aquí:
if ($login->isUserLoggedIn() == true) {
    // Si el usuario está conectado. Puedes hacer lo que quieras aquí.
    // mostramos la vista "usted ha iniciado sesión".
   header("location: facturas.php");

} else {
    // Si el usuario no ha iniciado sesión. Puedes hacer lo que quieras aquí.
    // mostramos la vista "no ha iniciado sesión".
    ?>
	<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
  <title>S.I.F | Login</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

   <link href="css/login.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body style="background-color: #15202B;">
 <div class="container">
        <div class="card card-container" style="background-color: #0F0F0F;">
            <img id="profile-img" class="profile-img-card" src="https://es.calcuworld.com/wp-content/uploads/sites/2/2019/09/generador-de-nombres-de-usuario.png" />
            <p id="profile-name" class="profile-name-card"></p>
            <form method="post" accept-charset="utf-8" action="login.php" name="loginform" autocomplete="off" role="form" class="form-signin">
			<?php
				// show potential errors / feedback (from login object)
				if (isset($login)) {
					if ($login->errors) {
						?>
						<div class="alert alert-danger alert-dismissible" role="alert">
						    <strong>Error!</strong> 
						
						<?php 
						foreach ($login->errors as $error) {
							echo $error;
						}
						?>
						</div>
						<?php
					}
					if ($login->messages) {
						?>
						<div class="alert alert-success alert-dismissible" role="alert">
						    <strong>Aviso!</strong>
						<?php
						foreach ($login->messages as $message) {
							echo $message;
						}
						?>
						</div> 
						<?php 
					}
				}
				?>
                <span id="reauth-email" class="reauth-email"></span>
                <input class="form-control" placeholder="Usuario" name="user_name" type="text" value="" autofocus="" required>
                <input class="form-control" placeholder="Contraseña" name="user_password" type="password" value="" autocomplete="off" required>
                <button type="submit" class="btn btn-lg btn-success btn-block btn-signin" name="login" id="submit">Iniciar Sesión</button>
            </form><!-- /form -->
            
        </div><!-- /card-container -->
    </div><!-- /container -->
  </body>
</html>

	<?php
}


