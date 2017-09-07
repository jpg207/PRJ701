<?php
    $CurrentQuestion = $theModel->getQuestionContent();
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <h2 class="content-head is-center">Lets build you a pc</h2>
        <div class="pure-g">
            <div class="pure-u-1-1 pure-u-xl-1-4"></div>
            <div class="pure-u-1-1 pure-u-xl-1-2 decision-content">
                <div class="decision-content-inner">
                    <form class="pure-form" action="controller.php?page=Generate" method="post">
                        <fieldset>
                                <?php
                                include ($CurrentQuestion);
                                ?>
                        </fieldset>
                    </form>
                    <br />
                </div>
            </div>
            <div class="side-menu">
                <h3>
                    Your Choices
                </h3>
                <?php
                if(sizeof($_SESSION['UserAnswers']) == 0){
                ?>
                    <div class="decision-history">
                        <p>
                            Your decitions will show here. <br>Click one to go back
                        </p>
                    </div>
                <?php
                }
                foreach ($_SESSION['UserAnswers'] as $key => $value) {?>
                    <div class="decision-history">
                        <a href="controller.php?page=Generate&forceQuestion=<?php echo $key ?>"><p>
                            <?php echo $key . ": " . $value; ?>
                        </p></a>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
