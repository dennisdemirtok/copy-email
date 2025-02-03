<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Flattered Platform Email</title>
    <link rel="icon" href="favicon.png" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-yfs1g+ttw12aAWPDAZ+J6CxNepqHeJaEq6h1BMBfF2lpz6RmPVN9L/4QxJJo1pG" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>

    <style>
        body {
            background-color: #ffffff;
            font-family: 'Montserrat';
            color: #ffffff;
        }

        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
        }

        .login-card {
            background-color: #333333;
            border: 1px solid #A23A21;
            border-radius: 5px;
            padding: 20px;
        }

        .login-card h2 {
            color: #ffffff;
            margin-bottom: 20px;
            text-align: center; /* Ajout de cette ligne pour centrer le texte */
        }

        .login-card input[type="text"],
        .login-card input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #A23A21;
            border-radius: 5px;
        }

        .login-card button {
            background-color: #A23A21;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Montserrat';
        }

        .login-card button:hover {
            background-color: #751b0e;
        }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="card login-card d-flex flex-column align-items-center justify-content-center"> <!-- Ajout de classes pour centrer -->
        <h2>Login</h2>
        <form action="<?php echo base_url('/login'); ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username :</label>
                <input type="text" name="username" value="" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password :</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-EV6IS8aF4pBUTyc8z0qA4sOF+Hy+3LTvQd9eBSvwqxE1CjcY1XIWgST7UZZCcJ45" crossorigin="anonymous"></script>

</body>
</html>
