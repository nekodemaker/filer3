  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Projet Filer-Login</title>
    <link rel="stylesheet" href="assets/style2.css">
  </head>

  <body>
    <div id="container">
      <div id="header-website">
        <a href="index.php">
          <img src="assets/logo.jpg" alt="">
        </a>
        <div class="menu">
          <div class="submenu purple">
            <a href="index.php?action=login">Sign in</a>
          </div>
          <div class="submenu blue">
            <a href="index.php?action=register">Register</a>
          </div>
        </div>
      </div>
      <div id="body-website">
        <p>Please Log in
          <p>
            <div id="message">
              <?php if(!empty($error)){echo $error;}  ?>
            </div>

            <form method='post' action='?action=login'>
              <fieldset>
                <legend>Log in</legend>
                <label for='username'>Username: </label>
                <input type='text' name='username' id='username' required>
                <br/>
                <label for='passw'>Password: </label>
                <input type='password' name='passw' id='passw' required>
                <br/>
                <input type='submit' value='log in'>
              </fieldset>
            </form>
      </div>
    </div>
  </body>

  </html>