<?php
require_once ('connect.php');
/**andmete sorterimine*/
function kysiKaupadeAndmed($sorttulp="nimetus", $otsisona=""){
    global $yhendus;
    $lubatudtulbad=array("nimetus", "grupinimi", "hind", "varv");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }
    $otsisona=addslashes(stripslashes($otsisona));
    $kask=$yhendus->prepare("SELECT kaubad.id, nimetus, grupinimi, kaubagrupi_id, hind, varv FROM kaubad, kaubagrupid 
       WHERE kaubad.kaubagrupi_id=kaubagrupid.id 
       AND (nimetus LIKE '%$otsisona%' OR grupinimi LIKE '%$otsisona%' OR varv LIKE '%$otsisona%') ORDER BY $sorttulp");
    //echo $yhendus->error;
    $kask->bind_result($id, $nimetus, $grupinimi, $kaubagrupi_id, $hind, $varv);
    $kask->execute();
    $hoidla=array();
    while($kask->fetch()){
        $kaup=new stdClass();
        $kaup->id=$id;
        $kaup->nimetus=htmlspecialchars($nimetus);
        $kaup->grupinimi=htmlspecialchars($grupinimi);
        $kaup->kaubagrupi_id=$kaubagrupi_id;
        $kaup->hind=$hind;
        $kaup->varv=$varv;
        array_push($hoidla, $kaup);
    }
    return $hoidla;
}

/**
 * Luuakse HTML select-valik, kus võetakse väärtuseks sqllausest tulnud
 * esimene tulp ning näidatakse teise tulba oma.
 */

/**dropdown list tabelist kaubagrupid grupinimi*/
function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi'>";
    while($kask->fetch()){
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $tulemus.="<option value='$id' $lisand >$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}

/**lisa grupinimi tabelisse kaubagrupid*/
function lisaGrupp($grupinimi){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO kaubagrupid (grupinimi) VALUES (?)");
    $kask->bind_param("s", $grupinimi);
    $kask->execute();
}

/**lisa kaupanimi tabelisse kaubagrupid*/
function lisaKaup($nimetus, $kaubagrupi_id, $hind, $varv){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO kaubad (nimetus, kaubagrupi_id, hind, varv) VALUES (?, ?, ?, ?)");
    $kask->bind_param("siis", $nimetus, $kaubagrupi_id, $hind, $varv);
    $kask->execute();
}
/**kustuta tabelist andmed*/
function kustutaKaup($kauba_id){
    global $yhendus;
    $kask=$yhendus->prepare("DELETE FROM kaubad WHERE id=?");
    $kask->bind_param("i", $kauba_id);
    $kask->execute();
}
/**uuendab kaup tabelist*/
function muudaKaup($kauba_id, $nimetus, $kaubagrupi_id, $hind, $varv){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE kaubad SET nimetus=?, kaubagrupi_id=?, hind=?, varv=? WHERE id=?");
    $kask->bind_param("siisi", $nimetus, $kaubagrupi_id, $hind, $varv, $kauba_id);
    $kask->execute();
}

//if(array_pop(explode("/", $_SERVER["PHP_SELF"]))=="abifunktsioonid.php"):
?>