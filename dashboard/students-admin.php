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
<?php
$sessionCondition = (!$_SESSION['isAdmin'] && !$_SESSION['isSuper']);
$headerLoc = "/dashboard.php";
require_once(__DIR__."/../templates/sessionValidation.php");
require_once(__DIR__ . "/../models/Student.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../util.php");
[$db, $connection] = Database::getConnection();
$classModal = "";
if (areSubmitted(Student::$INSERT_REQUIRED_FIELDS)) {
  if (checkInput(Student::$INSERT_REQUIRED_FIELDS)) {

    $student = new Student(
      $_POST['id'],
      (isset($_POST['name']) ? $_POST['name'] : null),
      (isset($_POST['lastNames']) ? $_POST['lastNames'] : null),
      (isset($_POST['sectionId']) ? $_POST['sectionId'] : null),

    );
    $student->connection = $connection;
    $result = $student->save();
    if ($result == 500 || $result == 400 || $result == 404 || $result == 403) {
      if ($result == 500)
        $errorMessage = "Hubo un error en el servidor";
      if ($result == 400)
        $errorMessage = "Campos en formato erróneo";
      if ($result == 403)
        $errorMessage = "Sección no existe";
      if ($result == 404)
        $errorMessage = "Estudiante no existe";
      $popErrorModal = true;
      $classModal = "danger";
    }
    if ($result == 200 || $result == 201 || $result == 205) {
      if ($result == 200)
        $successMessage = "Estudiante actualizado correctamente!";
      if ($result == 201)
        $successMessage = "Estudiante creado correctamente!";
      if ($result == 205)
        $successMessage = "Estudiante no necesita actualizarse";
      $popSuccessModal = true;
      $classModal = "success";
    }
  } else {
    $errorSubmission = "Los campos no pueden estar vacíos";
  }
}
if (isset($_GET['id']) && isset($_GET['m'])) {
  if (empty($_GET['id'])) {
    $searchError = "Campo no puede estar en blanco";
  } else {
    $student = Student::getStudent($connection, $_GET['id'], $_GET['m'] == 'd');
    if ($student) {
      if ($_GET['m'] != 'd') {
        $id = $student['id'];
        $blockIdInput = true;
        $name = $student['name'];
        $lastNames = $student['lastnames'];
        $idSection = $student['id_section'];
        $formText = "Actualizar estudiante";
        $formButtonText = "Actualizar";
      } else {
        $result = Student::removeStudent($connection, $_GET['id']);
        if ($result = 204) {
          $successMessage = "Estudiante eliminado correctamente";
          $popSuccessModal = true;
          $classModal = "success";
        } else {
          if ($result == 500)
            $errorMessage = "Hubo un error en el servidor";
          $popErrorModal = true;
          $classModal = "danger";
        }
      }
    } else {
      $searchInfo = "Estudiante no encontrado";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<?php $hdTitle = "SCOT: Estudiantes";
require_once(__DIR__ . '../../templates/header.php') ?>

<body class="g-sidenav-show  bg-gray-100">
  <!-- Modal -->
  <?php require_once(__DIR__ . '../../templates/modal.php') ?>
  <!-- End Modal -->
  <!-- Aside -->
  <?php $option = 4;
  require_once(__DIR__ . '../../templates/aside.php') ?>
  <!-- End Aside -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php $navTitle = "Administración de estudiantes";
    require_once(__DIR__ . '../../templates/navbar.php') ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row mt-4">
        <!-- Form -->
        <div class="col-12 col-xl-4">
          <div class="card h-100">
            <div class="card-header pb-0 p-3 border-0 d-flex align-items-center">
              <h6 class="mb-0" id="mainFormTitle"><?php echo (isset($formText) ? $formText : "Crear estudiante") ?></h6>
              <?php if (isset($id)) : ?>
                <form action="" method="get" class="ms-auto" id="formDelete">
                  <input type="hidden" value="<?php echo $id; ?>" name="id" />
                  <input type="hidden" value="d" name="m" />
                  <button class="btn btn-link text-danger px-3 mb-0 ms-auto" delete-item>
                    <i class="far fa-trash-alt me-2" aria-hidden="true"></i>Eliminar
                  </button>
                </form>
                <script>
                  let deleteButton = document.querySelector('button[delete-item]');
                  if (deleteButton) {
                    deleteButton.addEventListener('click', (e) => {
                      e.preventDefault();
                      if (confirm('¿Desea eliminar la sección?')) {
                        e.target.form.submit();
                      }
                    });
                  }
                </script>
              <?php endif; ?>
              <p class="btn btn-link pe-3 ps-0 mb-0 ms-auto" id="clearMainForm">Limpiar</p>
            </div>

            <div class="card-body p-3">
              <form role="form" method="POST" action="#" id="mainForm">
                <?php if (isset($errorSubmission)) : ?>
                  <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageMainForm"><?php echo $errorSubmission; ?></p>
                <?php endif; ?>

                <div class="mb-3" id="mainField">
                  <h6 class="text-uppercase text-body text-xs font-weight-bolder">Cédula de estudiante</h6>
                  <div>
                    <input type="hidden" name="id" value="<?php echo (isset($id) ? $id : $_POST['id'])  ?>">
                    <input type="text" name="id" placeholder="Cédula" class="form-control" id="formId" aria-label="id" aria-describedby="food-time-addon" value="<?php echo (isset($id) ? $id : ((isset($_POST['id'])) ? $_POST['id'] : "")) ?>" <?php echo (isset($blockIdInput) ? "disabled" : "")  ?>>
                  </div>
                </div>

                <div class="mb-3">
                  <h6 class="text-uppercase text-body text-xs font-weight-bolder">Nombre</h6>
                  <div>
                    <input type="text" class="form-control" id="mainFormName" placeholder="Nombre" name="name" aria-label="Nombre" aria-describedby="text-addon" value="<?php echo (isset($name) ? $name : ((isset($_POST['name'])) ? $_POST['name'] : "")) ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <h6 class="text-uppercase text-body text-xs font-weight-bolder">Apellidos</h6>
                  <div>
                    <input type="text" class="form-control" id="mainFormLastNames" placeholder="Apellidos" name="lastNames" aria-label="Apellidos" aria-describedby="text-addon" value="<?php echo (isset($lastNames) ? $lastNames : ((isset($_POST['lastNames'])) ? $_POST['lastNames'] : "")) ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <h6 class="text-uppercase text-body text-xs font-weight-bolder">Sección</h6>
                  <div>
                    <input type="text" class="form-control" id="mainFormSectionId" placeholder="Sección" name="sectionId" aria-label="Sección" aria-describedby="text-addon" value="<?php echo (isset($idSection) ? $idSection : ((isset($_POST['sectionId'])) ? $_POST['sectionId'] : "")) ?>">
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" id="mainFormButton" class="btn bg-gradient-info w-100 mt-4 mb-0"><?php echo (isset($formButtonText) ? $formButtonText : "Crear") ?></button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- End Form -->
        <!-- Search Student -->
        <div class="col-12 col-xl-4 mt-4 mt-lg-0">
          <div class="card  d-flex">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Buscar estudiante</h6>
            </div>
            <div class="card-body p-3">
              <?php if (isset($searchInfo)) : ?>
                <p class="text-info text-xs font-weight-bolder mb-3" id="infoMessageSearch"><?php echo $searchInfo; ?></p>
              <?php endif; ?>
              <?php if (isset($searchError)) : ?>
                <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageSearch"><?php echo $searchError; ?></p>
              <?php endif; ?>
              <form action="#" method="get">
                <div class="align-self-center  d-flex flex-wrap">
                  <div class="input-group flex-md-fill" style="z-index:98;">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" name="id" placeholder="Cédula de estudiante">
                  </div>
                  <input type="hidden" value="b" name="m" />
                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Buscar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Search Student -->

        <footer class="footer pt-3  ">
          <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
              <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start">
                  © <script>
                    document.write(new Date().getFullYear())
                  </script>,
                  made with <i class="fa fa-heart"></i> by
                  <a href="https://estebanramirez.xyz" class="font-weight-bold" target="_blank">Esteban Ramírez</a>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
  </main>
  <!-- Scripts -->
  <?php require_once(__DIR__ . '../../templates/scripts.php') ?>
  <!-- End Scripts -->
  <script>
    document.getElementById("clearMainForm").addEventListener("click", (e) => {
      window.history.replaceState({}, document.title, `${window.location.pathname}`);
      document.getElementById("mainFormTitle").textContent = "Crear estudiante";
      let mainField = document.getElementById("formId");
      mainField.value = "";
      mainField.removeAttribute('disabled');
      document.getElementById("mainFormName").value = "";
      document.getElementById("mainFormLastNames").value = "";
      document.getElementById("mainFormSectionId").value = "";
      document.getElementById("mainFormButton").textContent = "Crear";
      let formMsg = document.getElementById("errorMessageMainForm");
      if (formMsg) formMsg.remove();
      let deleteButton = document.getElementById('formDelete');
      if (deleteButton) deleteButton.remove();
    });
  </script>

</body>

</html>