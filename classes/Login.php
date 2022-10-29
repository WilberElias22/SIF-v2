<?php

/**
 * Inicio de sesión de clase
  * maneja el proceso de inicio y cierre de sesión del usuario
 */
class Login
{
    /**
     * @var object La conexión de la base de datos
     */
    private $db_connection = null;
    /**
     * @var array Recopilación de mensajes de error.
     */
    public $errors = array();
    /**
     * @var array Colección de mensajes de éxito/neutrales
     */
    public $messages = array();

    /**
     * la función "__construct()" se inicia automáticamente cada vez que se crea un objeto de esta clase,
      *  cuando haces "$login = new Login();"
     */
    public function __construct()
    {
        // Sesión de creación / lectura, absolutamente necesario
        session_start();

        // verifique las posibles acciones de inicio de sesión:
        // si el usuario intentó cerrar sesión (sucede cuando el usuario hace clic en el botón de cerrar sesión)
        if (isset($_GET["logout"])) {
            $this->doLogout();
        }
        // iniciar sesión a través de datos de publicación (si el usuario acaba de enviar un formulario de inicio de sesión)
        elseif (isset($_POST["login"])) {
            $this->dologinWithPostData();
        }
    }

    /**
     * iniciar sesión con los datos de la post
     */
    private function dologinWithPostData()
    {
        // comprobar el contenido del formulario de inicio de sesión
        if (empty($_POST['user_name'])) {
            $this->errors[] = "Username field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->errors[] = "Password field was empty.";
        } elseif (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {

            // crear una conexión de base de datos, usando las constantes de config/db.php (que cargamos en index.php)
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // cambie el conjunto de caracteres a utf8 y compruébelo
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }

            // si no hay errores de conexión (= conexión de base de datos en funcionamiento)
            if (!$this->db_connection->connect_errno) {

                // escapar de las cosas POST
                $user_name = $this->db_connection->real_escape_string($_POST['user_name']);

                // consulta de la base de datos, obteniendo toda la información del usuario seleccionado (permite iniciar sesión a través de la dirección de correo electrónico en el
                // campo de nombre de usuario)
                $sql = "SELECT user_id, user_name, user_email, user_password_hash
                        FROM users
                        WHERE user_name = '" . $user_name . "' OR user_email = '" . $user_name . "';";
                $result_of_login_check = $this->db_connection->query($sql);

                // if this user exists
                if ($result_of_login_check->num_rows == 1) {

                    // get result row (as an object)
                    $result_row = $result_of_login_check->fetch_object();

                    // using PHP 5.5's password_verify() function to check if the provided password fits
                    // the hash of that user's password
                    if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {

                        // write user data into PHP SESSION (a file on your server)
                        $_SESSION['user_id'] = $result_row->user_id;
						$_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['user_email'] = $result_row->user_email;
                        $_SESSION['user_login_status'] = 1;

                    } else {
                        $this->errors[] = "Usuario y/o contraseña no coinciden.";
                    }
                } else {
                    $this->errors[] = "Usuario y/o contraseña no coinciden.";
                }
            } else {
                $this->errors[] = "Problema de conexión de base de datos.";
            }
        }
    }

    /**
     * perform the logout
     */
    public function doLogout()
    {
        // delete the session of the user
        $_SESSION = array();
        session_destroy();
        // return a little feeedback message
        $this->messages[] = "Has sido desconectado.";

    }

    /**
     * simply return the current state of the user's login
     * @return boolean user's login status
     */
    public function isUserLoggedIn()
    {
        if (isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] == 1) {
            return true;
        }
        // default return
        return false;
    }
}
