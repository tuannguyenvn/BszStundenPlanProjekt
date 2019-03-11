<<!DOCTYPE html>
<html>
<head>
    <!-- <meta charset="utf-8"> -->
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
    <script src="main.js"></script>
</head>
<body>
    

<?php 
 $pdo = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '',array(PDO::ATTR_PERSISTENT => true));
 //Stammdaten holen
 $stamm_l = ".\Stammdaten\Lehrer.txt";
 lese_LehrerN($stamm_l,$pdo);
function lese_LehrerN($path,$pdo){
    $zeile = '';
    $i=0;
    if (file_exists($path)){
        $datei = file($path);
        //alle löschen
	//    $del = 'Delete From lehrer';
	//    $pdo->query($del);
        //status inaktiv
        // $update  ='Update lehrer set Status = 0 where status=1';
        // $pdo -> query($update);
	   foreach ($datei as $key => $value) {
           echo utf8_encode($value);
            //preg_match_all('/\(V:\'([A-Z a-z]*)\'; *K:\'([A-Z a-z]*)\';/m', $value, $matches, PREG_SET_ORDER, 0);
            //foreach ($matches as $key => $value) {
                //echo $i." ".$value[1]." ".$value[2]."<br>";
                //echo $i." ".$iarray[0]." ".$iarray[1]."<br>";
                //$insert = "Insert Into lehrer (name,kurzel, Datum,Status) values ('".$value[1].'; '.$value[2]."',".$i.",CURDATE(),1)";
                //echo $insert.'<br>';
                // $pdo->query($insert);
               // $i++;
            //}
	   }
        // dataInFolderPass($path, ".txt");
    }else{
        global $errorstring;
        $errorstring = $errorstring."Das Dokument ".$path. " konnte nicht gefunden werden!<br>";
    }
}
?>
</body>
</html>