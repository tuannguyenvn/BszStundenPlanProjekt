<!DOCTYPE html>
<html lang="de">
    <?php 
        $pdo = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '', array(PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        //$stand = "Select distinct Datum from lehrer where Status = 1";
        $varblock=-1;
        $varklasse=-1;
        $varlehrer=-1;
        $varzimmer=-1;
        $kstring='';
        $bstring='';
        $zstring='';
        $lstring='';
        $klstring='';
        $b=-1;
        $ar_get = array("block","lehrer","zimmer","klasse");
        if (array_key_exists("block",$_POST)){
            $varblock = $_POST['block'];
            $b=$varblock;
        }
        if (array_key_exists("lehrer",$_POST)){
            $varlehrer = $_POST['lehrer'];
        }
        if (array_key_exists("zimmer",$_POST)){
            $varzimmer = $_POST['zimmer'];
        }
        if (array_key_exists("klasse",$_POST)){
            $varklasse = $_POST['klasse'];
            $ar_klasse = explode("|", $varklasse);
            if($ar_klasse[1]>3){$varblock = $ar_klasse[1];$b = $varblock-4;}
        }
        $wherestring='';
        if ($varblock != -1){
            $wherestring .= " and (k.block = ". $varblock." or k.block= ".($varblock+4).")";
        } 
        if ($varlehrer != -1){
            $wherestring .= " and (p.Lehrer1 = ". $varlehrer." or p.Lehrer2 = ".$varlehrer.")";
        } 
        if ($varzimmer != -1){
            $wherestring .= " and (p.Zimmer1 = ". $varzimmer." or p.Zimmer2 = ".$varzimmer.")";
        } 
        if ($varklasse != -1){
            $wherestring .= " and p.Klassen_ID = ". $ar_klasse[0];
        }
        if($wherestring==''){
            $wherestring = "and 1=2";
        } 

        $sql = "SELECT p.Klassen_ID, p.Tag,p.Stunde,\n"
        . "ifnull(k.Name,'') Klasse, k.block , \n"
        . "ifnull(l.Name,'') Klassenlehrer, \n"
        . "ifnull(z1.Raum,'') z1, ifnull(z2.Raum,'') z2, \n"
        . "ifnull(l1.Name,'') l1, ifnull(l2.name,'') l2, ifnull(l1.Kuerzel,'') lk1, ifnull(l2.Kuerzel,'') lk2, \n"
        . "ifnull(f1.bezeichnung,'') f1, ifnull(f2.bezeichnung,'') f2\n"
        . "FROM plan p\n"
        . "left join klassen k on p.Klassen_ID = k.DAT_ID and k.Status=1\n"
        . "inner join lehrer l on k.klassenlehrer = l.DAT_ID and l.Status=1\n"
        . "left join zimmer z1 on p.Zimmer1 = z1.DAT_ID and z1.Status=1\n"
        . "left join zimmer z2 on p.Zimmer2 = z2.DAT_ID and z2.Status=1\n"
        . "left join Lehrer l1 on p.Lehrer1 = l1.DAT_ID and l1.Status=1\n"
        . "left join Lehrer l2 on p.Lehrer2 = l2.DAT_ID and l2.Status=1\n"
        . "left join faecher f1 on p.Fach1 = f1.DAT_ID and f1.Status=1\n"
        . "left join faecher f2 on p.Fach2 = f2.DAT_ID and f2.Status=1\n"
        . "WHERE p.STATUS = 1 ".$wherestring." order by p.Stunde, p.Tag, p.Klassen_ID";

        //Planarray anlegen
        $planarray = Array(
            array('','','','','','','','','',''),
            array('','','','','','','','','',''),
            array('','','','','','','','','',''),
            array('','','','','','','','','',''),
            array('','','','','','','','','',''),
            array('7.30 - 8.15','8.15 - 9.00','9.30 - 10.15','10.15 - 11.00','11.15 - 12.00','12.00 - 12.45','13.30 - 14.15','14.15 - 15.00','15.05 - 15.50','15.50 - 16.35')
        );

        //Datenbankanbingung / Abfrage
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $plan = $statement->fetchAll(PDO::FETCH_ASSOC); 

        //Planarray mit DB-Datne füllen
        foreach($plan as $key => $row){
            $ausgabe='';
            //Klasse (wenn nicht im Filter)
            if($ar_klasse[0]==-1){
                $ausgabe .= $row["Klasse"];
            }elseif($kstring==''){
                $kstring = "Klasse: ".$row["Klasse"];
                $klstring = "Klassenleiter: ".utf8_encode($row["Klassenlehrer"]);
                //switch ($b) {
                //    case 0 : $bstring = 'A-Block';
                //        break;
                //    case 1 : $bstring = 'B-Block';
                //        break;
                //    case 2 : $bstring = 'C-Block';
                //        break;
                //}
            }
            if($varlehrer==-1){
                if($ausgabe!=''){$ausgabe.='<br>';}
                $ausgabe .= utf8_encode($row["l1"]);
                if($row["l2"]!=""){$ausgabe.=" / ".utf8_encode($row["l2"]);}
            }elseif($lstring==''){
                $lstring = utf8_encode($row["lk1"]);
                if($lstring==''){$lstring = utf8_encode($row["lk2"]);}
            }
            if($varzimmer==-1){
                if($ausgabe!=''){$ausgabe.='<br>';}
                $ausgabe.=$row["z1"];
                if($row["z2"]!=""){$ausgabe.=" / ".$row["z2"];}
            }elseif ($zstring==''){
                $zstring = $row["z1"];
                if($zstring==''){$zstring = $row["z2"];}
            }
            if($ausgabe!=''){$ausgabe.='<br>';}
            $ausgabe.=$row["f1"];
            if($row["f2"]!=""){$ausgabe.=" / ".$row["f2"];}
            switch ($b) {
                case 0 : $bstring = 'A-Block';
                    break;
                case 1 : $bstring = 'B-Block';
                    break;
                case 2 : $bstring = 'C-Block';
                    break;
            }
            $planarray[$row['Tag']][$row['Stunde']] = $ausgabe;
        }
     ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stundenplan</title>
    <link rel="stylesheet" href="css/bootstrap.css">

</head>

<body style="font-family:arial;">
<script type="text/javascript" src="js/bootstrap.js"></script>  
    <?php   
        if(count($plan)<>0){
            echo $kstring." \t".$klstring;
            echo '<table border="1px" class="table table-sm table-bordered table-striped" id="calendar">';
            //Tabellenkopf schreiben
            // echo '<thead class="bg-light">';
            echo '<tr class="text-center">
                <th width="10%">'.$bstring.' '.$lstring.'</th>
                <th >Montag</th>
                <th >Dienstag</th>
                <th >Mittwoch</th>
                <th >Donnnerstag</th>
                <th >Freitag</th>
                </tr>';
                
            for($s=0;$s<10;$s++){
                echo '<tr class="text-center">';
                echo "<td>".$planarray[5][$s]."</td>";
                for($t=0;$t<5;$t++){
                    echo "<td>".$planarray[$t][$s]."</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }else{
            Echo "Kein Stundenplan für die Kriterien gefunden!";
        }
    ?>
    
</body>
</html>