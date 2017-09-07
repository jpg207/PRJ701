<?php

error_reporting(E_ALL);
class PageView {
    public function Render($theModel) {
        $CurrentContent = $theModel->getPageContent();
        $Content = 1;
    ?>

  <!doctype html>
  <html lang="en">
    <body>
      <?php include ('../Includes/Head.php'); ?>
      <?php include ('../Includes/TopNav.php'); ?>
      <?php include ($CurrentContent);?>

      <div class="footer l-box is-center">
          Lets get building
      </div>
    </body>
  </html>
  <?php
  }
}

if(!isset($_REQUEST['page'])) {
  $_REQUEST['page'] = 'Home';
}
?>
