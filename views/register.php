<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Projet Filer-register</title>
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
      <p>You can register here to upload files</p>
            <p><?php if(!empty($error)){echo $error;}  ?></p>
      <form method='post' action='?action=register'>
        <fieldset>
          <legend>Register</legend>
          <label for='username'>Username: </label>
          <input type='text' name='username' id='username' required>
          <br/>
          <label for='firstname'>Firstname: </label>
          <input type='text' name='firstname' id='firstname' required>
          <br/>
          <label for='lastname'>Lastname: </label>
          <input type='text' name='lastname' id='lastname' required>
          <br/>
          <label for='passw'>Password: </label>
          <input type='password' name='passw' id='passw' required>
          <br/>
          <label for='mail'>Mail: </label>
          <input type='email' name='mail' id='mail' required>
          <br/>
          <input type='submit' value='Register'>
        </fieldset>
      </form>
    </div>
  </div>
</body>

</html>