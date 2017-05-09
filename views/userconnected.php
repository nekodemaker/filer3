<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Projet Filer-User Connected</title>
  <link rel="stylesheet" href="assets/style2.css">
  <script src="assets/script4.js"></script>
</head>

<body>
  <div id="container">
    <div id="header-website">
      <a href="index.php">
        <img src="assets/logo.jpg" alt="">
      </a>
      <div class="menu">
        <div class="submenu purple">
          <a href="index.php?action=logout">log out</a>
        </div>
      </div>
    </div>
    <div id="body-website">
      <p class="welcome-para">
        Welcome
        <?php  print_r($_SESSION['username']); ?>
      </p>
      <div id="warning-block">
        <?php errors_file(); ?>
      </div>
      <div id="message">
        <?php good_messages(); ?>
      </div>
      <form id="form-upload" method="post" enctype="multipart/form-data" action="?action=upload">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000000" /> Choose a file to upload, you can upload a same file name with a different extension
        <input type="file" id="myFile" name="monfichier" />
        <input type="submit" class="bof" value="upload" />
      </form>
      <div id="edit-block">
        <?php edit_appear();?>
          <?php visualize_appear();?>
      </div>
      <div id="user-files-list">
        <?php display_all_user_files();?>
      </div>
      <form method="post" action="?action=createNewFolder">
        <label for="new-d">Create a new directory</label>
        <input type="text" id="new-d" name="new_directory" />
        <input type="submit" class="bof" value="Create new directory" />
      </form>
      <div id="user-directories-list">
        <?php display_all_user_directories();?>
      </div>
    </div>
  </div>
</body>

</html>