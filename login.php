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
            <link rel="stylesheet" type="text/css" media="screen" href="main.css">
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
                    }
                    
                    header('Location: Anzeige_kopf.php');
                }
            }
        }
        ?>

        <body>
            <form method="POST" action="login.php">
                <fieldset>
                    <legend>Anmeldung</legend>
                    <table>
                        <tr>
                            <td>Name</td>
                            <td><input type="text" name="loginName" size="30"></td>
                        </tr>
                        <tr>
                            <td>Kenntwort</td>
                            <td><input type="password" name="password" size="30"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"> <input name="btn_submit" type="submit" value="login"></td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </body>
        </html> 