<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<?php
//Funktion setzen
$admin = 0;
$kstring= '';
$admin = isset($_SESSION["rolle"]) ? $_SESSION["rolle"] : '';
$classId = isset($_SESSION["classe"]) ? $_SESSION["classe"] : '';

if ($classId>-1){
    $kstring = ' and DAT_ID = '.$classId;
}
//Datenbank anbinden    
$pdo = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '', array(PDO::ATTR_PERSISTENT => true));
$zsql = "SELECT Raum, DAT_ID FROM zimmer Where Status = 1 order by Raum";
$lsql = "SELECT Name, Kuerzel, DAT_ID FROM lehrer Where Status = 1 order by Name";
$ksql = "SELECT Klassen_kat,Name,block, DAT_ID FROM klassen Where Status = 1". $kstring ." order by Name";

//sollang schüler keine Klasse mitgibt
$hide = '';
if ($admin == 0) {
    $hide = "visibility:hidden;";
}
?>
<script lang="Javascript">
    function akt() {
        document.getElementById("akt").src = "Stammdaten_aktualisieren.php?save=1";
    }

    function Logout() {
        self.location = 'Login.php';
        
    }

    function SchuelerSelected() {
        document.Daten.action = "anzeige.php";
        document.Daten.submit();
    }
    function anzeigePlan(art) {
        switch (art) {
            case 0:
                if (document.getElementById('klasse').value == '-1|-1' && document.getElementById('lehrer').value == -1 && document.getElementById('zimmer').value == -1) {
                    return;
                }
                if (document.getElementById('block').value == -1) {
                    document.getElementById('lehrer').value = -1;
                    document.getElementById('zimmer').value = -1;
                    document.getElementById('klasse').value = '-1|-1';
                }
                break;
            case 1:
                w = document.getElementById('klasse').value.split("|");
                block = w[1];
                //document.getElementById('lehrer').value= -1;
                //document.getElementById('zimmer').value= -1;
                if (document.getElementById('block').value == -1) {
                    document.getElementById('block').value = 0;
                }
                if (block > 3) {
                    document.getElementById('block').value = block - 4;
                } else if (block != -1) {
                    document.getElementById('block').value = block;
                }
                break;
            case 2:
                //document.getElementById('klasse').value= '-1|-1';
                //document.getElementById('zimmer').value= -1;
                if (document.getElementById('block').value == -1) {
                    document.getElementById('block').value = 0;
                }
                break;
            case 3:
                //document.getElementById('lehrer').value= -1;
                //document.getElementById('klasse').value= '-1|-1';
                if (document.getElementById('block').value == -1) {
                    document.getElementById('block').value = 0;
                }
                break;
        }
        document.Daten.action = "anzeige.php";
        document.Daten.submit();
        //document.anzeige.reload();
    }
</script>

<body style="padding:0;margin0;" <?php if($classId > -1) echo 'onload ="SchuelerSelected()" '?>>
    <div id="kopf" style="height:150px;width:calc(100% - 58px);padding:20px;position:absolute;">
        <h2 style="margin:0;">Stundenplan</h2>

        <?php if ($admin >= 0) { ?>
        <form name="Daten" method="post" target="anzeige">
            <div style="float:left;padding-right:10px;<?php echo $hide; ?>">
                <label for="block">Block</label><br>
                <select id="block" name="block" onchange="anzeigePlan(0);">
                    <option value="-1">-- Block wählen --</option>
                    <option value="0">A-Block</option>
                    <option value="1">B-Block</option>
                    <option value="2">C-Block</option>
                </select>
            </div>
            <div style="float:left;padding-right:10px;" onchange="anzeigePlan(1);">
                <label for="klasse">Klasse</label><br>
                <select id="klasse" name="klasse">
                    <option value="-1|-1" <?php if($classId > -1) echo 'disabled '?>>-- Klasse wählen --</option>
                    <?php 
                    foreach ($pdo->query($ksql) as $row) {
                        switch (($row['block'] % 4)) {
                            case 0:
                                $b = 'A';
                                break;
                            case 1:
                                $b = 'B';
                                break;
                            case 2:
                                $b = 'C';
                                break;
                            default:
                                $b = '';
                                break;
                        }
                        $sel = '';
                        if($row['DAT_ID'] == $classId){$sel = ' selected ';}
                        echo '<option value="' . $row['DAT_ID'] . '|' . $row['block'] . '"'.$sel.'>' . $row['Name'] . ' (' . $b . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="float:left;padding-right:10px;<?php echo $hide; ?>" onchange="anzeigePlan(2);">
                <label for="lehrer">Lehrer</label><br>
                <select id="lehrer" name="lehrer">
                    <option value="-1">-- Lehrer wählen --</option>
                    <?php 
                    foreach ($pdo->query($lsql) as $row) {
                        echo '<option value="' . $row['DAT_ID'] . '">' . utf8_encode($row['Name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="float:left;padding-right:10px;<?php echo $hide; ?>" onchange="anzeigePlan(3);">
                <label for="zimmer">Zimmer</label><br>
                <select id="zimmer" name="zimmer">
                    <option value="-1">-- Zimmer wählen --</option>
                    <?php 
                    foreach ($pdo->query($zsql) as $row) {
                        echo '<option value="' . $row['DAT_ID'] . '">' . $row['Raum'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </form>
        
        <div style="width:100%; text-align:right; float:right;">
        <?php
        if ($admin == 2) {
            ?>
            <button type="button" onclick="akt()">Stammdaten aktualisieren</button>
        <?php 
        } ?>
            <button type="button" onclick="Logout()">Abmelden</button>
        </div>
        
        <iframe id="akt" src="" style="width:0px;height:0px;border:0;"></iframe>
        <?php 
    } ?>
    </div>
    <div id="inhalt" style="height:calc(100% - 170px);width:calc(100% - 18px);margin-top:150px;position:absolute;">
        <iframe id="anzeige" name="anzeige" src="anzeige.php" style="width:100%;height:100%;border:0;"></iframe>
    </div>

</body>

</html> 