<?php
require("abifunktsioonid.php");
$sorttulp="nimetus";
$otsisona="";

if(isset($_REQUEST['sorttulp']))
{
    $sorttulp=$_REQUEST['sorttulp'];
}
if(isset($_REQUEST['otsisona']))
{
    $otsisona=$_REQUEST['otsisona'];
}
////////////////////////////////////////////////////////
if(isSet($_REQUEST["grupilisamine"])){
    global $yhendus;
    $kaubagrupp=$_REQUEST["uuegrupinimi"];
    $query=mysqli_query($yhendus, "Select * from kaubagrupid where grupinimi='$kaubagrupp'");
    if(!empty(trim($_REQUEST["uuegrupinimi"])) && mysqli_num_rows($query) == 0){
        lisaGrupp($_REQUEST["uuegrupinimi"]);
        header("Location: kaubahaldus.php");
        exit();
    } else{
        $error="Selline kaubagrupinimi on juba olemas!";
    }
    $error="Kaubagrupp ei pea olema tühi";
}

if(isSet($_REQUEST["kaubalisamine"])){
    global $yhendus;
    $kaup2=$_REQUEST["nimetus"];
    $query=mysqli_query($yhendus, "Select * from kaubad where nimetus='$kaup2'");
    if (!empty($_REQUEST['nimetus']) && mysqli_num_rows($query) == 0){
        lisaKaup($_REQUEST["nimetus"], $_REQUEST["kaubagrupi_id"], $_REQUEST["hind"], $_REQUEST["varv"]);
        header("Location: kaubahaldus.php");
        exit();
    } else {
        $error2="Selline kauba nimetus on juba olemas!";
    }
    $error2="Kauba nimetus ei pea olema tühi";
}
//////////////////////////////////////////////////////////
if(isSet($_REQUEST["kustutusid"])){
    kustutaKaup($_REQUEST["kustutusid"]);
}
if(isSet($_REQUEST["muutmine"])){
    muudaKaup($_REQUEST["muudetudid"], $_REQUEST["nimetus"], $_REQUEST["kaubagrupi_id"], $_REQUEST["hind"], $_REQUEST["varv"]);
}
$kaubad=kysiKaupadeAndmed($sorttulp, $otsisona);
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <title>Kaupade leht</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

</head>
<header>
    <h1>Kaupade haldus</h1>
</header>
<body>
<!--Otsing-->
<form action="kaubahaldus.php">
    <div id="loetelu">
        <h2>Kaupade loetelu</h2>
        <form action="kaubahaldus.php">
            <input type="text" name="otsisona" placeholder="Otsi...">
        </form>
        <!--Kaubahaldus-->
        <table>
            <tr>
                <th>Haldus</th>
                <th><a href="kaubahaldus.php?sorttulp=nimetus">Nimetus</a></th>
                <th><a href="kaubahaldus.php?sorttulp=grupinimi">Kaubagrupp</a></th>
                <th><a href="kaubahaldus.php?sorttulp=hind">Hind</a></th>
                <th><a href="kaubahaldus.php?sorttulp=varv">Värv</a></th>
            </tr>
            <?php foreach($kaubad as $kaup): ?>
                <tr>
                    <?php if(isSet($_REQUEST["muutmisid"]) && intval($_REQUEST["muutmisid"])==$kaup->id): ?>
                        <td>
                            <input type="submit" name="muutmine" value="Muuda" />
                            <input type="submit" name="katkestus" value="Katkesta" />
                            <input type="hidden" name="muudetudid" value="<?=$kaup->id ?>" />
                        </td>
                        <td><input type="text" name="nimetus" value="<?=$kaup->nimetus ?>" /></td>
                        <td><?php echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid","kaubagrupi_id", $kaup->kaubagrupi_id); ?></td>
                        <td><input type="text" name="hind" value="<?=$kaup->hind ?>" /></td>
                        <td><input type="text" name="varv" value="<?=$kaup->varv ?>" /></td>
                    <?php else: ?>
                        <td>
                            <a class="tdx" href="kaubahaldus.php?kustutusid=<?=$kaup->id ?>" onclick="return confirm('Kas ikka soovid kustutada?')">Kustuta</a>
                            <a class="tdm" href="kaubahaldus.php?muutmisid=<?=$kaup->id ?>">Muuda</a>
                        </td>

                        <td><?=$kaup->nimetus ?></td>
                        <td><?=$kaup->grupinimi ?></td>
                        <td><?=$kaup->hind ?></td>
                        <td><?=$kaup->varv ?></td>
                    <?php endif ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</form><br>
<!--Kaubagrupi lisamine-->
<form action="kaubahaldus.php">
    <div id="grupi">
        <h2>Grupi lisamine</h2>
        <input type="text" name="uuegrupinimi" pattern="[A-Za-z].{3,}" />
        <input type="submit" name="grupilisamine" value="Lisa grupp" />
        <?php echo"<div style='color: red'>".($error ?? "")."</div>"; ?>
    </div>
</form><br>
<!--Kauba lisamine-->
<form action="kaubahaldus.php">
    <div id="kauba">
        <h2>Kauba lisamine</h2>
        <dl>
            <dt>Nimetus:</dt>
            <dd><input type="text" name="nimetus" /></dd>
            <dt>Kaubagrupp:</dt>
            <dd><?php echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid", "kaubagrupi_id");?></dd>
            <dt>Hind:</dt>
            <dd><input type="text" name="hind" /></dd>
            <dt>Värv:</dt>
            <dd><input type="text" name="varv" /></dd>
        </dl>
        <input type="submit" name="kaubalisamine" value="Lisa kaup" />
        <?php echo"<div style='color: red'>".($error2 ?? "")."</div>"; ?>
    </div>
</form>
</body>
</html>

<style>
    header {
        text-align: center;
    }
    body {
        background-color: lightsteelblue;
    }
    form {
        background-color: aliceblue;
        width: 400px;
        padding: 2%;
    }
    table {
        border: 2pt solid black;
    }
    td, th {
        border: 2pt solid black;
        font-size: 12pt;
    }
</style>