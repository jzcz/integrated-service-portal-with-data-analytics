<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/counselor.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: auto !important;
            background-color: white;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }
     
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html,
        body {
            height: 100%;
            overflow: hidden;
            padding: 0;
        }
        
        body {
            display: flex;
        }
        
        .container-fluid {
            height: 100vh;
            display: flex;
            padding: 0;
            padding-left: 0;
        }
        
        .row {
            display: flex;
            height: 100%;
        }
        
        .image-container {
            width: 50%;
            height: 100%;
            overflow: hidden;
        }
    </style>
</head>
<body>
<main class="container-fluid h-100 mt-0">
    <div class="row h-100 w-100">
        <div class="col-md-7 image-container mt-0">
            <img src="../../static/QCU-BUILDING2.png" alt="Login Image" class="login-image">
        </div>
        <div class="col-md-4 pt-5 mt-3 login-container">
            <img src="../../static/qcu-logo-login.png" alt="QCU Logo" class="login-logo">
            <h5 class="text-center p-2 mb-2"><b>Guidance & Counseling Unit</b></h5>
            <p class="text-center mb-3">Welcome back! Please enter your credentials to login.</p>

            <form>
                <div class="mb-3">
                    <label for="email" class="text-secondary form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Email">
                </div>
                <div class="mb-3">
                    <label for="password" class="text-secondary form-label">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password">
                </div>
                <div class="mb-3">
                    <label for="role" class="text-secondary form-label">Role</label>
                    <select class="text-secondary form-select" id="role">
                        <option selected>Role</option>
                        <option value="admin">Admin</option>
                        <option value="counselor">Counselor</option>
                        </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="login-btn btn-primary p-1">Login</button>
                </div>
            </form>

            <p class="mt-3 text-center text-muted"><i>*Please contact the admin for assistance with login issues.</i></p>
        </div>
    </div>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html> 