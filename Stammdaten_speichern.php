<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<?php
$pdo = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '','');
$path_Stamm = "C:\Users\ND\Documents\Projehtarbeit_Gruppe_2019\Stammdaten\Lehrer.txt";
?>
    
<body>
<?php
//$sql = "SELECT * FROM lehrer";
//foreach ($pdo->query($sql) as $row) {
//   echo $row['DAT_ID']." ".$row['Name']." ".$row['kuerzel']."<br />";
//}
$zeile = '';
$datei = file($path_Stamm);
if ($datei){
	$del = 'Delete From lehrer';
	$pdo->query($del);
	for($i=0;$i < count($datei); $i++){
   		//echo $i.": ".$datei[$i]."<br>";
		$zeile = $zeile.trim($datei[$i]) ;
	}
	$zeile = str_replace('(V:', '', str_replace('K:', '', str_replace('S:', '', str_replace(')', '', $zeile))));
	$larray = explode(",", $zeile);
	for($i=0;$i < count($larray);$i++){
		$iarray = explode(';', $larray[$i]);
		//echo $larray[$i]."<br>";
		//echo $i." ".$iarray[0]." ".$iarray[1]."<br>";
		$insert = 'Insert Into lehrer (Name, kuerzel,DAT_ID, datum) values ('.$iarray[0].','.$iarray[1].','.$i.',CURDATE())';
		$pdo->query($insert);
	}
}

$sql = "SELECT * FROM lehrer";
foreach ($pdo->query($sql) as $row) {
  echo $row['DAT_ID']." ".$row['Name']." ".$row['kuerzel']."<br />";
}

//$pdo->close();
?> 
    
</body>
</html>