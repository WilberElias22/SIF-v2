<?php
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
// comprobando la versión mínima de PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // si está utilizando PHP 5.3 o PHP 5.4, debe incluir password_api_compatibility_library.php
    // (esta biblioteca agrega las funciones de hashing de contraseñas de PHP 5.5 a versiones anteriores de PHP)
    require_once("../libraries/password_compatibility_library.php");
}		
		if (empty($_POST['user_id_mod'])){
			$errors[] = "ID vacío";
		}  elseif (empty($_POST['user_password_new3']) || empty($_POST['user_password_repeat3'])) {
            $errors[] = "Contraseña vacía";
        } elseif ($_POST['user_password_new3'] !== $_POST['user_password_repeat3']) {
            $errors[] = "la contraseña y la repetición de la contraseña no son lo mismo";
        }  elseif (
			 !empty($_POST['user_id_mod'])
			&& !empty($_POST['user_password_new3'])
            && !empty($_POST['user_password_repeat3'])
            && ($_POST['user_password_new3'] === $_POST['user_password_repeat3'])
        ) {
            require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
			
				$user_id=intval($_POST['user_id_mod']);
				$user_password = $_POST['user_password_new3'];
				
                // cifrar la contraseña del usuario con la función password_hash() de PHP 5.5, da como resultado un carácter de 60
                // cadena hash. la constante PASSWORD_DEFAULT está definida por PHP 5.5, o si está utilizando
                // PHP 5.3/5.4, por la biblioteca de compatibilidad de hash de contraseñas
				$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
					
               
					// escribir los datos del nuevo usuario en la base de datos
                    $sql = "UPDATE users SET user_password_hash='".$user_password_hash."' WHERE user_id='".$user_id."'";
                    $query = mysqli_query($con,$sql);

                    // Si el usuario se ha añadido correctamente
                    if ($query) {
                        $messages[] = "contraseña ha sido modificada con éxito.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
            
        } else {
            $errors[] = "Un error desconocido ocurrió.";
        }
		
		if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}

?>