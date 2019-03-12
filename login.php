    <?php 
    if (isset($_SESSION)) {
        session_destroy();
    }
    session_start();
    ?>
    <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>Anmelden</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="css/bootstrap.css">
            <link rel="stylesheet" href="css/main.css">
            <style>
 body {
  margin: 0;
  padding: 0;
  /* background-color: #17a2b8; */
  height: 100vh;
}
#login .container #login-row #login-column #login-box {
  margin-top: 120px;
  max-width: 600px;
  height: 320px;
  border: 1px solid #9C9C9C;
  background-color: #EAEAEA;
}
#login .container #login-row #login-column #login-box #login-form {
  padding: 20px;
}
#login .container #login-row #login-column #login-box #login-form #register-link {
  margin-top: -85px;
}               
            </style>
        </head>
        <?php
        $connect = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '', array(PDO::ATTR_PERSISTENT => true));
        if (isset($_POST["btn_submit"])) {
            $userName = $_POST["loginName"];
            $password = $_POST["password"];
            $sql = "select * from nutzer where loginName = '$userName' and password = '$password'";

            if (isset($userName) || isset($password)) {
                $statement = $connect->prepare($sql);
                $statement->execute();

                $matchUser = $statement->fetchAll(PDO::FETCH_NUM);
                if (empty($matchUser)) {
                    echo "Falsche Name oder Passwort gegeben";
                } else {
                    //save Session
                    $_SESSION["loginName"] = $userName;
                    foreach ($matchUser as $key => $value) {
                        $_SESSION["rolle"] = $value[3];
                        $_SESSION["classe"] = $value[4];
                        $_SESSION["loginName"] = $value[1];
                    }
                    
                    header('Location: Anzeige_kopf.php');
                }
            }
        }
        ?>

        <body>
        <script type="text/javascript" src="js/bootstrap.js"></script>

        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center" >
              
                        <form id="login-form" class="form" action="login.php" method="post">
                            <h3 class="text-center text-info">Anmeldung</h3>
                            <div class="form-group">
                                <label for="username" class="text-info">Name:</label><br>
                                <input type="text" name="loginName" id="username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-info">Kenntwort:</label><br>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="btn_submit" class="btn btn-info btn-md" value="Anmelden">
                            </div>
                            <div id="register-link" class="text-right">
                        </form>
               
        </div>
    </div>
        </body>
        </html> 