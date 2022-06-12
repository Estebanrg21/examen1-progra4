<?php
session_start();
if (isset($_SESSION['verification'])) {
  header("Location: dashboard.php");
}
require_once "database/database.php";
require_once "models/User.php";
[$db, $connection] = Database::getConnection();
if (isset($_POST['email']) && isset($_POST['password'])) {
  $user = new User($_POST['email'], $_POST['password']);
  $user->connection = $connection;
  if ($user->login()) {
    session_start();
    $_SESSION['user'] = $user->email;
    $_SESSION['isSuper'] = $user->isSu;
    $_SESSION['isAdmin'] = $user->isAdmin;
    $_SESSION['verification'] = true;
    $_SESSION['start'] = time();
    if ($user->isSu || $user->isAdmin) {
      $_SESSION['LAST_ACTIVITY'] = time();
    } else {
      $_SESSION['expire'] = $_SESSION['start'] + (60 * 60);
    }
    header("Location: /dashboard.php");
  } else {
    $loginError = "Datos incorrectos";
  }
}
?>

<!--
=========================================================
* Soft UI Dashboard - v1.0.5
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<?php $hdTitle = "SCOT: Login";
require_once(__DIR__ . '/templates/header.php') ?>

<body class="">
  <style>
    #loginFooter footer{
      width: 100%;
    }
    #loginFooter footer div.row{
      justify-content: center !important;
    }
    footer .copyright{
      text-align: center !important;
    }
  </style>
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-75">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
              <div class="card card-plain mt-8">
                <div class="card-header pb-0 text-left bg-transparent">
                  <h3 class="font-weight-bolder text-info " style="color:white !important;">Login</h3>
                </div>
                <div class="card-body">
                  <form role="form" action="#" method="POST">
                    <?php if (isset($loginError)) : ?>
                      <p class="text-danger text-xs font-weight-bolder p-0 mb-3" id="errorMessageValidate"><?php echo $loginError; ?></p>
                    <?php endif; ?>
                    <label style="color:white">Email</label>
                    <div class="mb-3">
                      <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="email-addon">
                    </div>
                    <label style="color:white">Contraseña</label>
                    <div class="mb-3">
                      <input type="password" name="password" class="form-control" placeholder="Contraseña" aria-label="Password" aria-describedby="password-addon">
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">Iniciar sesión</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>
  <div class="d-flex justify-content-center" id="loginFooter" >
    <!-- Footer -->
    <?php require_once(__DIR__ . '/templates/footer.php') ?>
    <!-- End Footer -->
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.5"></script>
</body>

</html>