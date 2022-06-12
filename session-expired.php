<?php
session_start();
if (!isset($_SESSION['wasRedirected']))
  header("Location: /index.php");
else
  session_destroy();
?>


<!DOCTYPE html>
<html lang="en">

<?php $hdTitle = "SCOT: Sesión expirada";
require_once(__DIR__ . '/templates/header.php') ?>

<body class="">
  <style>
    #loginFooter footer {
      width: 100%;
    }

    #loginFooter footer div.row {
      justify-content: center !important;
    }

    footer .copyright {
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
              <h1>Sesión expirada!</h1>
              <a href="/login.php" style="color:white">Iniciar sesión</a>
            </div>

          </div>
        </div>
      </div>
    </section>

  </main>
  <div class="d-flex justify-content-center" id="loginFooter">
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