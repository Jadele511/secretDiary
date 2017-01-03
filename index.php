<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

  $error = "";

  if (array_key_exists("logout", $_GET)) {

    unset($_SESSION);

    setcookie("id", "", time() - 60*60);

    $_COOKIE["id"] = "";

  } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR ( array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])){

    header("Location:loggedinpage.php");

  }



  if (array_key_exists("submit",$_POST)) {

    include("connection.php");



    if (!$_POST['email']) {

      $error .="An email address is required.<br>";

    }

    if (!$_POST['password']) {

      $error .="A password is required.<br>";

    }

    if ($error != "" ) {

      $error = "<p>There were error(s) in you form:</p>".$error;

    } else {

      if ($_POST['signup']== '1') {

        $query = "SELECT `id` from `secretdi` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

        $result = mysqli_query($link, $query);

        if (mysqli_num_rows($result) > 0) {

          $error = "That email address is taken";

        } else {

          $query = "INSERT INTO `secretdi` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";

          if (!mysqli_query($link, $query)) {

            $error = "<p>Could not sign up. Please try later</p>";

          } else {

            $query = "UPDATE `secretdi` SET password='".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id=".mysqli_insert_id($link)." LIMIT 1 ";

            $last_rowid = mysqli_insert_id($link);

            mysqli_query($link, $query);

            $_SESSION['id'] = $last_rowid;

            if ($_POST['stayLoggedIn'] == '1') {

              setcookie("id", $last_rowid, time()+ 60*60*24*365);

            }

              header("Location: loggedinpage.php");
          }

        }

      } else {

          $query = "SELECT * from `secretdi` WHERE email='".mysqli_real_escape_string($link, $_POST['email'])."'";

          $result = mysqli_query($link, $query);

          $row = mysqli_fetch_array($result);

          if (isset($row)) {

              $hashedPassword = md5(md5($row['id']).$_POST['password']);

              if ($hashedPassword == $row['password']) {

                $_SESSION['id'] = $row['id'];

                if ($_POST['stayLoggedIn'] == '1') {

                  setcookie("id",$row['id'], time()+ 60*60*24*365);

                }

                header("Location: loggedinpage.php");


              } else {

                $error = "<p>That email/password combination could not be found </p>";

              }

          } else {

            $error = "<p>That email/password combination could not be found </p>";

          }
      }
  }
}


?>

<?php include("header.php") ?>
    <div class="container" id="#homePageContainer">

      <h1>Secret Diary</h1>
      <p><strong>Store your thought permanently and securely</strong></p>

      <form method="post" id="signUpForm">

        <div id="error"><?php if ($error!= "") {

          echo '<div class="alert alert-danger" role="alert">
  <strong>'.$error.'</strong>
</div>';
        } ?></div>

        <p>Interested? Sign up now!</p>

        <div class="form-group">

            <input name="email" type="text" class="form-control" placeholder ="Your Email" >

          </div>

          <div class="form-group">

            <input name="password" type="text" class="form-control"placeholder ="Password" >

          </div>

            <div class="form-check">

              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="stayLoggedIn" value=1 > Stay Logged In
              </label>

          </div>

            <input name="signup" type="hidden" value= 1 >

            <button type="submit" name="submit" class="btn btn-success">Sign Up</button>

            <p><a class="toggleForms">Log In </a></p>
      </form>


      <form method="post" id="logInForm">

        <p> Log in using your username and password</p>

        <div class="form-group">

            <input name="email" type="text" class="form-control" placeholder ="Your Email" >

          </div>

          <div class="form-group">

            <input name="password" type="text" class="form-control"placeholder ="Password" >

          </div>

            <div class="form-check">

              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="stayLoggedIn" value=1 > Stay Logged In
              </label>

          </div>

            <input name="signup" type="hidden" value= 0 >

            <button type="submit" name="submit" class="btn btn-success">Sign In </button>

            <p><a class="toggleForms">Sign Up </a></p>

      </form>


      </form>


</div>
<?php include("footer.php") ?>
