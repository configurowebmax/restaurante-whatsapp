<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Delicias de Elida</title>
    <link rel="shortcut icon" href="../icono.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            margin: auto;
        }

        .login-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .login-header h4 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .login-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px 12px 45px;
            height: auto;
            border: 2px solid #e1e1e1;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .btn-login {
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        .back-to-store {
            display: inline-block;
            padding: 15px;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-to-store:hover {
            color: #007bff;
            text-decoration: none;
        }

        .login-footer {
            padding: 15px;
            background: #f8f9fa;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 15px;
            }

            .login-card {
                margin: 0 auto;
            }

            .login-header h4 {
                font-size: 1.25rem;
            }

            .login-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h4><i class="fas fa-utensils mr-2"></i>Panel Administrativo</h4>
            </div>
            <div class="login-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php
                        switch ($_GET['error']) {
                            case 'invalid':
                                echo 'Usuario o contraseña incorrectos';
                                break;
                            case 'empty':
                                echo 'Por favor complete todos los campos';
                                break;
                            default:
                                echo 'Error al iniciar sesión';
                        }
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form action="auth.php" method="post">
                    <div class="form-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            placeholder="Usuario"
                            required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Contraseña"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-login btn-block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                    </button>
                </form>
            </div>
            <div class="login-footer">
                <a href="../index.html" class="back-to-store">
                    <i class="fas fa-store mr-2"></i>Volver a la Tienda
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>