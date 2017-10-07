<?php
    $CurrentBuild = $theModel->processBuild();
    $price = 0;
    if (count($CurrentBuild) > 2) {
?>

<h3>Build complete</h3>
<p>
    The following results are what we recommend for your system:
</p>

<?php }else{ ?>

<img class="attentionimage" src="../Images/Attention.png" alt="">
<h3>Sorry about this</h3>
<p>
    <?php echo $CurrentBuild ?>
</p>

<?php } ?>

<div class="pure-g">
    <?php
    foreach ($CurrentBuild as $key => $BuildItem) {
        if ($key != "ComponentBudget" && isset($BuildItem['CompName'])) {?>
            <div class="result-container">
                <div class="result-item">
                    <div class="result-head">
                        <p>
                            <a href="<?php echo $BuildItem["CompLink"]?>" target="_blank">
                                <h3><?php echo "<br />" . $key . ": <br />";?></h3>
                                <img class="detail-image" src="../Images/<?php echo $key; ?>.png" alt="">
                            </a>
                            <a href="<?php echo $BuildItem["CompLink"]?>" target="_blank">
                                <?php
                                echo  "<br /> <b><u>" . preg_replace('#\s*\(.+\)\s*#U', '', $BuildItem['CompName'] ) . "</u></b>";
                                echo  "<br /> $" . $BuildItem['CompPrice'];
                                $price = $price + $BuildItem['CompPrice'];
                                ?>
                            </a>
                        </p>
                        <div class="header_background">
                            <div class="sub_header">
                                <p><img class="expand_icon" alt="" src="../Images/plus.png">Details</p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown"  style="display:none">
                        <div class="result-details">
                            <b>Component Specs:</b><br />
                            Full specs are listed bellow
                            <div class="line">
                            </div>
                            <p class="item-details">
                                <?php foreach($BuildItem['ComponentDetail'] as $detail){
                                    if(strpos($detail['DetailValue'], 'http') === 0){
                                        echo "<b>" . $detail['DetailTitle'] . ": </b> <u><a href=" . $detail['DetailValue'] . " target='_blank'>Click to Vist</a></u><br />";
                                    }elseif (!preg_match("/Comp/",$detail['DetailTitle'])) {
                                        echo "<b>" . $detail['DetailTitle'] . ": </b> " . $detail['DetailValue'] . "<br />";
                                    }
                                }?>
                            </p>
                        </div>

                        <div class="learn-details">
                            <b>Other opitions:</b><br />
                            Core differences are listed bellow
                            <p class="item-details">
                            <?php
                            foreach ($BuildItem['ComponentDetailAlts'] as $AltArray) {
                                if ($BuildItem['CompName'] != $AltArray['CompName']) {
                                    echo "<div class='alts'>";
                                    echo "<b>Name:</b> <u><a href=https://pricespy.co.nz/product.php?j=" . $BuildItem['CompID'] . "," . $AltArray['CompID'] . " target='_blank'>" . $AltArray['CompName'] . "</a></u><br />";
                                    echo "<b>Price:</b> $" . $AltArray['CompPrice'] . "<br />";
                                    foreach ($AltArray['AltDetails'] as $key => $value) {
                                        if(strpos($value['DetailValue'], 'http') === 0){
                                            echo "<b>" . $value['DetailTitle'] . ": </b> <u><a href=" . $value['DetailValue'] . " target='_blank'>Click to Vist</a></u><br />";
                                        }elseif ($value['DetailValue'] != "NULL" && !preg_match("/Comp/",$value['DetailTitle']) && $BuildItem['ComponentDetail'][$value['DetailTitle']]['DetailValue'] != $value['DetailValue'] ) {
                                            echo "<b>" . $value['DetailTitle'] . ": </b> " . $value['DetailValue'] . "<br />";
                                        }
                                    }
                                    echo "<br /></div>";
                                }
                            }
                            ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</div>
