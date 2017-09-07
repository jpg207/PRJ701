<?php
    $CurrentBuild = $theModel->processBuild();
    $price = 0;
?>

<h3>Build complete</h3>
<p>
    The following results are what we recommend for your system:
</p>

<div class="header_backgroundall">
    <div class="sub_header">
        <p><img class="expand_icon" alt="" src="../Images/plus.png">Toggle All details</p>
    </div>
</div>

<div class="pure-g">
    <?php
    foreach ($CurrentBuild as $key => $item) {?>
        <div class="result-container">
            <div class="result-item">
                <div class="result-head">
                    <p>
                        <a href="<?php echo $item["CompLink"]?>" target="_blank">
                            <h3><?php echo "<br />" . $key . ": <br />";?></h3>
                            <img class="detail-image" src="../Images/<?php echo $key; ?>.png" alt="">
                        </a>
                        <a href="<?php echo $item["CompLink"]?>" target="_blank">
                            <?php
                            echo  "<br /> <b>" . preg_replace('#\s*\(.+\)\s*#U', '', $item['CompName'] ) . "</b>";
                            echo  "<br /> $" . $item['CompPrice'];
                            $price = $price + $item['CompPrice'];
                            ?>
                        </a>
                    </p>
                    <div class="header_background">
                        <div class="sub_header">
                            <p><img class="expand_icon" alt="" src="../Images/plus.png">Details</p>
                        </div>
                    </div>
                </div>
                <div class="result-details" style="display:none">
                    <p class="item-details">
                        <?php foreach($item as $key => $detail){
                            if ($detail != "0" && !preg_match("/Comp/",$key)) {
                                echo "<b>" . $key . ": </b> " . $detail . "<br />";
                            }
                        }?>
                    </p>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="result-container">
        <div class="result-item">
            <p>
                <h3><br />Total price <br /></h3>
                <img class="detail-image" src="../Images/Dollar.png" alt="">
                    <br /><b>Price to build system:</b></b>
                    <?php
                        echo  "<br /> $" . $price;
                    ?>
                </a>
            </p>
            <div class="header_background">
                <div class="sub_header">
                    <p><img class="expand_icon" alt="" src="../Images/plus.png">Details</p>
                </div>
            </div>
            <div class="section" style="display:none">
                <p class="item-details">
                    <?php
                    foreach ($CurrentBuild as $key => $item) {
                        echo  "<br /> <b>" . $key . ":</b> $" . $item['CompPrice'];
                    } ?>
                </p>
            </div>
        </div>
    </div>
</div>
