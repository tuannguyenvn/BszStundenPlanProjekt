<!DOCTYPE html>
<html lang="de">

<head>
    <title>Document</title>
</head>

<?php
    //Datenbank anbinden    
    $pdo = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '',array(PDO::ATTR_PERSISTENT => true));
    //Stammdaten holen
    $stamm_l = ".\Stammdaten\Lehrer.txt";
    $stamm_z = ".\Stammdaten\Zimmer.txt";
    $stamm_f = ".\Stammdaten\Faecher.txt";
    $stamm_k = ".\Stammdaten\Klassen.txt";
    $plandat = ".\Stammdaten\plan.dat";
    //Funktion setzen
    $admin = true;
    $save = 0;
    $weiter = 0;
    $errorstring = '';
    //Get Parameter auslesen
    if (array_key_exists("save",$_GET)){
        $save = $_GET['save'];
    }

    function bit($dec){
        if( $dec != 255){
            return ($dec<<1)-1;
        }else{
            return $dec;
        }
    }  
    
    function hex4todec($hexstr){
        //if($hexstr != "FFFF"‬){
            //4-Stelligen Hexstring in Binärdaten Zerlegen
            $string = decbin(hexdec($hexstr));
            //Länge auslesen
            $length = strlen($string);
            //2er Hexcodes tauschen 
            $tausch = substr($string,0,$length-8);
            for($i=strlen($tausch);$i<8;$i++){
                $tausch="0".$tausch;
            }
            $tausch = substr($string,-8).$tausch;
            $length = strlen($tausch);
            //in 2 Parts splitten
            $zahl1 = bindec(substr($tausch,-7));    
            $zahl2 = bindec(substr(substr($tausch,0,$length-7),-7));
            //if($zahl2 != 0){
                //$zahl2 = ($zahl2<<1);
            //}
            $ausgabe = $zahl1.",".($zahl2-1);
        
            return $ausgabe;
        //}else{
        //    return "-1,-1";
        //}
    }
    
function dat_lesen($path){
        $arquivo = fopen($path, "r");
        $read = fread($arquivo,filesize($path));
        $hex = bin2hex($read);// return the hex of the binary
        $hexpart = chunk_split(strtoupper($hex), 2," ");// split the hex each 2 bytes
        $ar_hex = explode(' ',trim($hexpart));
        $anz = count($ar_hex);
        $k = 0; //Klassenindex
        $t = 0; //Tagindex
        $z = 0; //Zeitindex
        $ele = 0; //Elementzaehler
        $string=''; //insertstring
        for($ele;$ele<$anz;$ele++){
            if(($ele%2)==1 && hexdec($ar_hex[$ele])!=0 && hexdec($ar_hex[$ele])!=255){
                $string = $string.(hexdec($ar_hex[$ele])-1).","; //Dec für Einzelpos[2]  
            }else{
                $string = $string.hexdec($ar_hex[$ele]).","; //Dec für Einzelpos                
            }
            if(($ele%6)==5){    //Zeitraum auslesen
                echo "K:".$k." T:".($t%5)." S:".($z%10)."|".$string."<br>";
                $string='';
                $z++;
            }
            if(($ele%60)==59){  //tag auslesen 
                $t++;
            }
            if(($ele%300)==299){    //Klasse auslesen
                $k++;
            }
        }
    }    
    
function lese_plan($nome,$pdo){
    if(file_exists($nome)){
        $arquivo = fopen($nome, "r");
        $read = fread($arquivo,filesize($nome));
        $hex = bin2hex($read);// return the hex of the binary
        $hexdat = chunk_split(strtoupper($hex), 2, "");// split the hex each 2 bytes
        //Datenbank:
        //Datenbank leeren
        $del = 'Delete From plan'; 
        $pdo->query($del);
        //Datenbankeinträge auf inaktiv setzen
        $update  ='Update plan set Status = 0';
        $pdo -> query($update);
        $array_plaene= str_split( $hexdat, 600 ) ; 
        $max = sizeof($array_plaene);  
    
        for($i = 0; $i < $max;$i++){  
            $array_plan= str_split($array_plaene[$i], 120 ) ; 
            $len_woche= sizeof($array_plan); 
    
            for($j = 0; $j < $len_woche ;$j++){
                $tag_plan = str_split($array_plan[$j], 4 ) ;
                //print_r($tag_plan) ;
                $length_tag = sizeof($tag_plan);
                $s = 0;
                for($n = 0; $n < $length_tag ;$n+=3){
                    $insert= "Insert Into plan (Zimmer1,Zimmer2,Fach1,Fach2,Lehrer1,Lehrer2,Stunde,Tag,Klassen_ID,akt_Datum,Status) ";
                    $insert.="values (".hex4todec($tag_plan[$n]).",".hex4todec($tag_plan[$n+1]).",".hex4todec($tag_plan[$n+2]).",".($s).",".$j.",".$i.",CURDATE(),1)";
                    $pdo->query($insert);
                    $s++;
                }
            }   
        }  
    }else{
        global $errorstring;
        $errorstring = $errorstring."Das Dokument ".$nome." konnte nicht gefunden werden!<br>";
    }
}    
    
function lese_lehrer($path,$pdo){
    $zeile = '';    
    if(file_exists($path)){
        $datei = file($path); //Datei holen
        //Datenbank leeren
        $del = 'Delete From lehrer'; 
        $pdo->query($del);
        //Datenbankeinträge auf inaktiv setzen
        $update  ='Update lehrer set Status = 0 where status=1';
        $pdo -> query($update);
        //Datei in eine Zeile zusammenfassen
	   for($i=0;$i < count($datei); $i++){
   		   //echo $i.": ".$datei[$i]."<br>";
		  $zeile = $zeile.trim($datei[$i]) ;
	   }
	   $zeile = str_replace('(V:', '', str_replace('K:', '', str_replace('S:', '', str_replace(')', '', trim($zeile)))));
        //$zeile = preg_replace($zeile,'/\(V:\'([ ]*+[0-9A-Za-z]++[ ]*+)+\';  *K:\'([ ]*+[0-9A-Za-z]++[ ]*+)+\';/m',trim($zeile));
	   $larray = explode(",", $zeile);
	   for($i=0;$i < count($larray);$i++){
		  $iarray = explode(';', $larray[$i]);
		  //echo $larray[$i]."<br>";
		  //echo $i." ".$iarray[0]." ".$iarray[1]."<br>";
		  $insert = "Insert Into lehrer (Name, Kuerzel,DAT_ID, Datum, Status) values (".utf8_encode($iarray[0]).",".utf8_encode($iarray[1]).",".$i.",CURDATE(),1)";
		  $pdo->query(utf8_decode($insert));
	   }
        dataInFolderPass($path, ".txt");
    }else{
        global $errorstring;
        $errorstring = $errorstring."Das Dokument ".$path. " konnte nicht gefunden werden!<br>";
    }
}


function lese_zimmer($path,$pdo){
    $zeile = '';
    $i=0;
    if (file_exists($path)){
        $datei = file($path);
        //alle löschen
	   $del = 'Delete From zimmer';
	   $pdo->query($del);
        //status inaktiv
        $update  ='Update zimmer set Status = 0 where status=1';
        $pdo -> query($update);
	   foreach ($datei as $key => $value) {
            preg_match_all('/\(V:\'([A-Z a-z]*)\'; *K:\'([A-Z a-z]*)\';/m', $value, $matches, PREG_SET_ORDER, 0);
            foreach ($matches as $key => $value) {
                //echo $i." ".$value[1]." ".$value[2]."<br>";
                //echo $i." ".$iarray[0]." ".$iarray[1]."<br>";
                $insert = "Insert Into Zimmer (Raum,DAT_ID, Datum,Status) values ('".$value[2]."',".$i.",CURDATE(),1)";
                $pdo->query($insert);
                $i++;
            }
	   }
        dataInFolderPass($path, ".txt");
    }else{
        global $errorstring;
        $errorstring = $errorstring."Das Dokument ".$path. " konnte nicht gefunden werden!<br>";
    }
}

function lese_faecher($path,$pdo){
    $zeile = '';
    $i=0;
    if (file_exists($path)){
        $datei = file($path);
        //alle löschen
	   $del = 'Delete From faecher';
	   $pdo->query($del);
        //status inaktiv
        $update  ='Update faecher set Status = 0 where status=1';
        $pdo -> query($update);
	   foreach ($datei as $key => $value) {
            preg_match_all('/\(N:([0-9]*); *K:\'([0-9 A-Z a-z]*)\'\)/m', $value, $matches, PREG_SET_ORDER, 0);
            foreach ($matches as $key => $value) {
                //echo $i." ".$value[1]." ".$value[2]."<br>";
                //echo $i." ".$iarray[0]." ".$iarray[1]."<br>";
                $insert = "Insert Into faecher (bez_kat,bezeichnung,DAT_ID, Datum,Status) values (".$value[1]." ,'".$value[2]."' ,".$i.",CURDATE(),1)";
                $pdo->query($insert);
                $i++;
            }
	   }
       dataInFolderPass($path, ".txt");
    }else{
        global $errorstring;
        $errorstring = $errorstring."Das Dokument ".$path. " konnte nicht gefunden werden!<br>";
    }
} 

function lese_klassen($path,$pdo){
    $zeile = '';
    $i=0;
    if (file_exists($path)){
        $datei = file($path);
        //alle löschen
        $del = 'Delete From klassen';
	    $pdo->query($del);
        //status inaktiv
        $update  ='Update klassen set Status = 0 where status=1';
        $pdo -> query($update);
	   foreach ($datei as $key => $value) {
            preg_match_all('/\(N:( *[0-9]*); *K:\'([A-Za-z0-9]* *[A-Za-z0-9]*-[A-Za-z0-9])\'; *B:([A-Za-z0-9]); *L:( *[0-9]*)\)/m', $value, $matches, PREG_SET_ORDER, 0);
            foreach ($matches as $key => $value) {
                //echo $i." ".$value[1]." ".$value[2]." ".$value[3]." ".$value[4]."<br>";
                //echo $i." ".$iarray[0]." ".$iarray[1]."<br>";
                $insert = "Insert Into klassen (Klassen_kat,Name,block,klassenlehrer,DAT_ID, Datum, Status) values (".$value[1].",'".$value[2]."',".$value[3].",".$value[4].",".$i.",CURDATE(),1)";
                $pdo->query($insert);
                $i++;
            }
	   }
       dataInFolderPass($path, ".txt");
    }else{
        global $errorstring;
        $errorstring = $errorstring."Das Dokument ".$path. " konnte nicht gefunden werden!<br>";
    }
} 

function dataInFolderPass ($stammFile, $endung){
    if(file_exists($stammFile)) {
        $archiveFile = str_replace("Stammdaten", "Stammdaten/Archiv",$stammFile);
        $archiveFile = str_replace($endung, "_".str_replace("-", "", date("Y-m-d-h-i-s")).$endung,$archiveFile);
        copy($stammFile, $archiveFile); 
        unlink($stammFile);
    }else{
        global $errorstring;
        $errorstring = $errorstring."Das Dokument ".$stammFile. " konnte nicht gefunden werden!<br>";
    }
} 
   
//Wenn Button gedrückt alle Stammdaten holen
    if($save == 1){
        lese_lehrer($stamm_l,$pdo);
        lese_zimmer($stamm_z,$pdo);
        lese_faecher($stamm_f,$pdo);
        lese_klassen($stamm_k,$pdo);
        lese_plan($plandat,$pdo);
        $save = 0;
        $weiter = 1;
    } 
?>
    <script lang="Javascript">
        function reset(){
            <?php if($errorstring!=''){ ?>
                var error = '<?php echo $errorstring ?>';
                alert(error.replace(/<br>/g,"\n"));
            <?php } ?>
            alert("Aktualisierung abgeschlossen!");
            self.location = "Stammdaten_aktualisieren.php";
        }
    </script>
<body <?php if($weiter==1){?>onload="reset();"<?php } ?> >

</body>
</html>