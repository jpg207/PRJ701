<?php
    $CurrentBuild = $theModel->processBuild();
?>

<h3>Build complete</h3>
<p>
    The following results are what we recommend for your system:
</p>

<?php
foreach ($CurrentBuild as $key => $item) {
    echo $key . ": ";
    foreach($item as $key => $detail){
        if($detail != "null"){
            echo  "<br /> &nbsp &nbsp" . $key . ": " . $detail;
        }
    }
}
 ?>
<button type="submit" value="Submit" class="pure-button pure-button-primary">Submit</button>
