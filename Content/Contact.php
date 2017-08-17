<div class="content-wrapper">
  <div class="content">
    <h2 class="content-head is-center">Contact us</h2>
    <div class="pure-g">
      <div class="pure-u-1-3"></div>
      <div class="pure-u-1-3">
        <form class="pure-form pure-form-stacked" action="../phpScripts/mail.php" method="POST">
          <fieldset>
            <legend>Got a problem, suggestion or just want to contact us? Hit us up bellow</legend>
            <label for="name">Name</label>
            <input required type="text" name="name" placeholder="Email">

            <label for="email">Email</label>
            <input required id="email" type="email" placeholder="Email">

            <label for="message">Message</label>
            <textarea name="message" rows="6"></textarea>
            <br />
            <input type="submit" value="Send" class="pure-button pure-button-primary"><input type="reset" value="Clear">
          </fieldset>
        </form>
      </div>
      <div class="pure-u-1-3"></div>
    </div>
  </div>
</div>
