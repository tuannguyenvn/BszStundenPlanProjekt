<!DOCTYPE html>
<html lang="de">
    <?php 
        $pdo = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '', array(PDO::ATTR_PERSISTENT => true));
        //$stand = "Select distinct Datum from lehrer where Status = 1";
        $varblock=-1;
        $varklasse=-1;
        $varlehrer=-1;
        $varzimmer=-1;
        $ar_get = array("block","lehrer","zimmer","klasse");
        if (array_key_exists("block",$_POST)){
            $varblock = $_POST['block'];
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
        }
        $wherestring='';
        if ($varblock != -1){
            $wherestring .= " and k.block = ". $varblock;
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

        $sql = "SELECT p.Klassen_ID, p.Tag,p.Stunde,\n"
        . "ifnull(k.Name,\'\') Klasse, k.block , \n"
        . "ifnull(l.Name,\'\') Klassenlehrer, \n"
        . "ifnull(z1.Raum,\'\') z1, ifnull(z2.Raum,\'\') z2, \n"
        . "ifnull(l1.Name,\'\') l1, ifnull(l2.name,\'\') l2, \n"
        . "ifnull(f1.bezeichnung,\'\') f1, ifnull(f2.bezeichnung,\'\') f2\n"
        . "FROM plan p\n"
        . "left join klassen k on p.Klassen_ID = k.DAT_ID and k.Status=1\n"
        . "inner join lehrer l on k.klassenlehrer = l.DAT_ID and l.Status=1\n"
        . "left join zimmer z1 on p.Zimmer1 = z1.DAT_ID and z1.Status=1\n"
        . "left join zimmer z2 on p.Zimmer2 = z2.DAT_ID and z2.Status=1\n"
        . "left join Lehrer l1 on p.Lehrer1 = l1.DAT_ID and l1.Status=1\n"
        . "left join Lehrer l2 on p.Lehrer2 = l2.DAT_ID and l2.Status=1\n"
        . "left join faecher f1 on p.Fach1 = f1.DAT_ID and f1.Status=1\n"
        . "left join faecher f2 on p.Fach2 = f2.DAT_ID and f2.Status=1\n"
        . "WHERE p.STATUS = 1 ".$wherestring." order by p.Klassen_ID, p.Stunde, p.Tag";

        //foreach ($pdo->query($sql) as $row) {
        //}
     ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anzeige Stundenplan</title>

</head>

<body>  
    <?php //echo $sql ?>
    <table border="1px" cellpadding="5px" cellspacing="0px" width="100%" >
        <tr bgcolor="yellow">
            <td width="10%">Block</td>
            <td width="18%">Montag</td>
            <td width="18%">Dienstag</td>
            <td width="18%">Mittwoch</td>
            <td width="18%">Donnnerstag</td>
            <td width="18%">Freitag</td>
        </tr>
    <?php 
        //echo $varblock." ".$varlehrer." ".$varzimmer." ".$varklasse." <br>";
        if($varblock==-1 && $varlehrer==-1 && $varzimmer==-1 && $varklasse==-1){
    ?>  
        <tr>
            <td>7.30 - 8.15</td>
            <td ></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>8.15 - 9.00</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>9.30 - 10.15</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>10.15 - 11.00</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>11.15 - 12.00</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>12.00 - 12.45</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>13.30 - 14.15</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>14.15 - 15.00</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>15.05 - 15.50</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>15.50 - 16.35</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php 
    }else{ $merkstunde = 0; $merktag=0; 
        //foreach($pdo->query($sql) as $row){ 
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $row = $statement->fetchAll(PDO::FETCH_NUM);    
        // $dbplan = $pdo->query($sql or die ($pdo->error));

            if($row["Stunde"] == $merkstunde){
                echo "<tr>";
                switch ($row['Stunde']) {
                    case 0: echo "<td>7.30 - 8.15</td>";
                        break;
                    case 1: echo "<td>8.15 - 9.00</td>";
                        break;
                    case 2: echo "<td>9.30 - 10.15</td>";
                        break;
                    case 3: echo "<td>10.15 - 11.00</td>";
                        break;
                    case 4: echo "<td>11.15 - 12.00</td>";
                        break;
                    case 5: echo "<td>12.00 - 12.45</td>";
                        break;
                    case 6: echo "<td>13.30 - 14.15</td>";
                        break;
                    case 7: echo "<td>14.15 - 15.00</td>";
                        break;
                    case 8: echo "<td>15.05 - 15.50</td>";
                        break;
                    case 9: echo "<td>15.50 - 16.35</td>";
                        break;
                }
                $merkstunde++;
            }
            if($row["Tag"]==4 ){
                echo "</tr>";
            }
        
        $dbplan->free();
    } ?>
    </table>
</body>
</html>