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
    //Datenbank anbinden    
$pdo = new PDO('mysql:host=localhost;dbname=stundenplan', 'root', '', array(PDO::ATTR_PERSISTENT => true));
$zsql = "SELECT Raum, DAT_ID FROM zimmer Where Status = 1 order by Raum";
$lsql = "SELECT Name, Kuerzel, DAT_ID FROM lehrer Where Status = 1 order by Name";
$ksql = "SELECT Klassen_kat,Name,block, DAT_ID FROM klassen Where Status = 1 order by Name";

$classId = isset($_SESSION["classe"]) ? $_SESSION["classe"] : '';
$classSql = "SELECT Name FROM klassen Where ID = $classId";
//Funktion setzen
$admin = 0;

$admin = isset($_SESSION["rolle"]) ? $_SESSION["rolle"] : '';

?>
<script lang="Javascript">
    function akt() {
        document.getElementById("akt").src = "Stammdaten_aktualisieren.php?save=1";
    }

    function anzeige(art) {
        switch (art) {
            case 0:
                if (document.getElementById('klasse').value == -1 && document.getElementById('lehrer').value == -1 && document.getElementById('zimmer').value == -1) {
                    //return;
                }
                if (document.getElementById('block').value == -1) {
                    document.getElementById('lehrer').value = -1;
                    document.getElementById('zimmer').value = -1;
                    document.getElementById('klasse').value = -1;
                }
                break;
            case 1:
                document.getElementById('lehrer').value = -1;
                document.getElementById('zimmer').value = -1;
                if (document.getElementById('block').value == -1) {
                    document.getElementById('block').value = 0;
                }
                break;
            case 2:
                document.getElementById('klasse').value = -1;
                document.getElementById('zimmer').value = -1;
                if (document.getElementById('block').value == -1) {
                    document.getElementById('block').value = 0;
                }
                break;
            case 3:
                document.getElementById('lehrer').value = -1;
                document.getElementById('klasse').value = -1;
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

<body style="padding:0;margin0;">
    <div id="kopf" style="height:150px;width:calc(100% - 58px);padding:20px;position:absolute;">
        <h2 style="margin:0;">Stundenplan</h2>

        <?php if ($admin == 0) { ?>
        <h3>
            <?php 
            foreach ($pdo->query($classSql) as $row) {
                echo '<h4 align=center>Klasse: ' . $row['Name'] . '</h4>';
            }
            ?>
        </h3>
        <?php 
    } ?>

        <?php if ($admin >= 1) { ?>
        <form name="Daten" method="post" target="anzeige">
            <div style="float:left;padding-right:10px;">
                <label for="block">Block</label><br>
                <select id="block" name="block" onchange="anzeige(0);">
                    <option value="-1">-- Block wählen --</option>
                    <option value="0">A-Block</option>
                    <option value="1">B-Block</option>
                    <option value="2">C-Block</option>
                </select>
            </div>
            <div style="float:left;padding-right:10px;" onchange="anzeige(1);">
                <label for="klasse">Klasse</label><br>
                <select id="klasse" name="klasse">
                    <option value="-1">-- Klasse wählen --</option>
                    <?php 
                    foreach ($pdo->query($ksql) as $row) {
                        echo '<option value="' . $row['DAT_ID'] . '|' . $row['block'] . '">' . $row['Name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="float:left;padding-right:10px;" onchange="anzeige(2);">
                <label for="lehrer">Lehrer</label><br>
                <select id="lehrer" name="lehrer">
                    <option value="-1">-- Lehrer wählen --</option>
                    <?php 
                    foreach ($pdo->query($lsql) as $row) {
                        echo '<option value="' . $row['DAT_ID'] . '">' . $row['Name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="float:left;padding-right:10px;" onchange="anzeige(3);">
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
        <?php
        if ($admin == 2) {
            ?>
        <div style="width:100%; text-align:right; float:right;">
            <button type="button" onclick="akt()">Stammdaten aktualisieren</button>
        </div>
        <?php 
    } ?>
        <iframe id="akt" src="" style="width:0px;height:0px;border:0;"></iframe>
        <?php 
    } ?>
    </div>
    <div id="inhalt" style="height:calc(100% - 170px);width:calc(100% - 18px);margin-top:150px;position:absolute;">
        <iframe id="anzeige" name="anzeige" src="anzeige.php" style="width:100%;height:100%;border:0;"></iframe>
    </div>

</body>

</html> 