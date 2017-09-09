<?php
    $CurrentBuild = $theModel->processBuild();
    $price = 0;
?>

<h3>Build complete</h3>
<p>
    The following results are what we recommend for your system:
</p>

<div class="pure-g">
    <?php
    foreach ($CurrentBuild as $key => $item) {
        if ($key != "ComponentBudget") {?>
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
                                if($detail != "0" && $key == "ProductPage"){
                                    echo "<b>" . $key . ": </b> <u><a href=" . $detail . " target='_blank'>Click to Vist</a></u><br />";
                                }elseif ($detail != "0" && !preg_match("/Comp/",$key)) {
                                    echo "<b>" . preg_replace('/(?<!\ )[A-Z]{1}[a-z]/', ' $0', $key) . ": </b> " . $detail . "<br />";
                                }
                            }?>
                        </p>
                    </div>
                </div>
            </div>
    <?php
        }
    } ?>

    <div class="result-container">
        <div class="result-item">
            <div class="result-head">
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
            </div>
            <div class="result-details" style="display:none">
                <p class="item-details">
                    <?php
                    foreach ($CurrentBuild as $key => $item) {
                        if ($key != "ComponentBudget") {
                            echo  "<b>" . $key . ":</b> $" . $item['CompPrice'] . " out  of $" . $CurrentBuild['ComponentBudget'][$key] . "<br />";
                        }
                    } ?>
                </p>
            </div>
        </div>
    </div>
</div>
