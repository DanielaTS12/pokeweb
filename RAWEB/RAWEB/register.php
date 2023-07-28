<?php
// index.php

session_start();

// Función para generar un token CSRF
function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Función para verificar el token CSRF
function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
}

// Función para conectar a la base de datos
function conectarDB()
{
    $host = "localhost"; // Cambiar por el nombre de host de tu base de datos
    $username = "root"; // Cambiar por el nombre de usuario de tu base de datos
    $password = ""; // Cambiar por la contraseña de tu base de datos
    $dbname = "pokeapi"; // Cambiar por el nombre de la base de datos

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para registrar un nuevo usuario
function registrarUsuario($username, $password)
{
    $db = conectarDB();

    // Verificar si el usuario ya existe en la base de datos
    $query = $db->prepare('SELECT usuario FROM sesion WHERE usuario = :username');
    $query->execute(array(':username' => $username));
    $result = $query->fetch();

    if ($result) {
        return false; // El usuario ya existe
    }

    // Insertar el nuevo usuario en la base de datos
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = $db->prepare('INSERT INTO sesion (usuario, pass) VALUES (:username, :password)');
    $query->execute(array(':username' => $username, ':password' => $hashedPassword));

    return true; // Registro exitoso
}

// Procesar el formulario de registro cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['usuario'];
    $password = $_POST['pass'];
    $csrfToken = $_POST['csrf_token'];

    // Verificar el token CSRF
    if (!verifyCSRFToken($csrfToken)) {
        die('Token CSRF no válido.');
    }

    // Registrar al usuario
    if (registrarUsuario($username, $password)) {
        echo '¡Registro exitoso!';
    } else {
        echo 'El usuario ya existe.';
    }
}

// Generar el token CSRF
generateCSRFToken();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Registro</title>
    <!-- Archivos CSS -->
    <link href="./dist/css/tabler.min.css?1684106062" rel="stylesheet" />
    <link href="./dist/css/tabler-flags.min.css?1684106062" rel="stylesheet" />
    <link href="./dist/css/tabler-payments.min.css?1684106062" rel="stylesheet" />
    <link href="./dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet" />
    <link href="./dist/css/demo.min.css?1684106062" rel="stylesheet" />
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
</head>

<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/International_Pok%C3%A9mon_logo.svg/800px-International_Pok%C3%A9mon_logo.svg.png"
                        height="70" alt="">
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Registro</h2>
                    <form action="" method="post" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <label class="mb-3 form-label">Correo electrónico</label>
                            <input type="email" class="mb-5 form-control" name="usuario"
                                placeholder="tucorreo@ejemplo.com" autocomplete="off">
                        </div>
                        <div class="mb-2">
                            <label class="mb-3 form-label">
                                Contraseña
                            </label>
                            <div class="mb-5 input-group input-group-flat">
                                <input type="password" class="form-control" name="pass" placeholder="Tu contraseña"
                                    autocomplete="off">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Mostrar contraseña"
                                        data-bs-toggle="tooltip"><!-- Descargar el icono SVG de http://tabler-icons.io/i/eye -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="form-footer">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" name="register" class="btn btn-primary w-100">Registrarse</button>
                        </div>
                    </form>
                </div>
                <div class="text-center text-muted mt-3">
                    ¿Ya tienes una cuenta? <a href="./sign-in.html" tabindex="-1">Iniciar sesión</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Librerías JS -->
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js?1684106062" defer></script>
    <script src="./dist/js/demo.min.js?1684106062" defer></script>
</body>

</html>
