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
session_start();
$sessionCondition = (!$_SESSION['isAdmin'] && !$_SESSION['isSuper']);
$headerLoc = "/dashboard.php";
require_once(__DIR__ . "/../templates/sessionValidation.php");
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
    [$text, $isOk] = Student::$responseCodes[$result];
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
        [$text, $isOk] = Student::$responseCodes[$result];
      }
    } else {
      $searchInfo = "Estudiante no encontrado";
    }
  }
}

if (isset($_FILES["csvFile"])) {
  try {
    #para warning sobre limite de tamaño excedido ver:
    #https://stackoverflow.com/a/21715692/11449132
    if (!empty($_FILES['csvFile']['tmp_name'])) {
      $results = Student::readCsvStudents($connection, file_get_contents($_FILES['csvFile']['tmp_name']));
      header("Location: /dashboard/student-admin.php");
      exit;
    } else {
      $csvError = "El campo no puede ir vacío";
    }
  } catch (\Throwable $th) {
    $csvError = $th->getMessage();
  }
}

if (isset($isOk)) {
  if (!$isOk) {
    $errorMessage = $text;
    $popErrorModal = true;
    $classModal = "danger";
  } else {
    $successMessage = $text;
    $popSuccessModal = true;
    $classModal = "success";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<?php $hdTitle = "SCOT: Estudiantes";
require_once(__DIR__ . '../../templates/header.php') ?>

<body class="g-sidenav-show  ">
  <div class="modal fade" id="Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down position-relative">
      <div class="px-3  modal-content  bg-modal-menu">
        <div class="modal-header bg-modal-menu">
          <h5 class="modal-title" id="exampleModalToggleLabel2">Información de subida del CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body " id="modalBody" style="-webkit-overflow-scrolling: touch !important;overflow-y: auto !important;">
          <h5>Puntos a tomar en cuenta:</h5>
          <ul>
            <li>
              <p>Antes de ingresar los estudiantes, deberán de existir las secciones, de lo contrario no se podrán guardar los estudiantes con secciones inexistentes. Si desea agregar secciones, diríjase a la pantalla de administración de secciones</p>
            </li>
            <li>
              <p>El único formato de archivo que se acepta es CSV delimitado por comas</p>
            </li>
            <li>
              <p>Debe obedecer el formato establecido en la plantilla, de lo contrario no se podrán procesar los datos</p>
            </li>
            <li>
              <p>Los campos deben de tener una longitud igual o menor a la siguiente:</p>
              <ul>
                <li>
                  <p><b>id</b> : 12 caracteres</p>
                </li>
                <li>
                  <p><b>nombre</b> : 20 caracteres</p>
                </li>
                <li>
                  <p><b>apellidos</b> : 100 caracteres</p>
                </li>
                <li>
                  <p><b>sección</b> : 10 caracteres</p>
                </li>
              </ul>
            </li>
            <li><p><b>Tenga cuidado con los espacios en blanco</b>. El sistema los puede tolerar, sin embargo, para evitar resultados no deseados, por favor verifique las entradas</p></li>
            <li>
              <p>Una vez que se hayan subido los datos, el sistema los procesará y si no existe ningún error, devolverá un reporte con el resultado para cada estudiante</p>
            </li>
            <li>
              <p>Para descargar la plantilla del formato del archivo, haga click <b><a href="/assets/plantillaEstudiantes.csv" download>aquí</a></b></p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
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
                      if (confirm('¿Desea eliminar el estudiante?')) {
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
        <div class="col-12 col-xl-4 mt-4 mt-lg-0">
          <!-- Search Student -->
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
          <!-- End Search Student -->
          <!-- Upload CSV -->
          <div class="card  d-flex mt-4">
            <div class="card-header pb-0 p-3 d-flex align-items-center">
              <h6 class="mb-0">Subir CSV con estudiantes</h6>

              <p class="btn btn-link pe-3 ps-0 mb-0 ms-auto">tamaño máximo: <?php echo convertBytesTo(file_upload_max_size()) . "Mb" ?></p>
              <span id="uploadHelp"><i class="fa-solid fa-question fa"></i></span>
            </div>
            <div class="card-body p-3">
              <?php if (isset($csvError)) : ?>
                <p class="text-danger text-xs font-weight-bolder mb-3" id="csvError"><?php echo $csvError; ?></p>
              <?php endif; ?>
              <form action="#" method="POST" enctype="multipart/form-data">
                <div class="align-self-center  d-flex flex-wrap">
                  <div class="input-group flex-md-fill" style="z-index:98;">
                    <input type="file" class="form-control" name="csvFile" placeholder="Estudiantes">
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Subir</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- End Upload CSV -->
        </div>


         <!-- Footer -->
         <?php require_once(__DIR__ . '../../templates/footer.php') ?>
        <!-- End Footer -->
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
  <script>
    let uploadHelp = document.getElementById("uploadHelp");
    if (uploadHelp) {
      uploadHelp.addEventListener("click", (e) => {
        openModal()
      });
    }

    function openModal() {
      let modal = new bootstrap.Modal(document.getElementById('Modal'));
      modal.show();
    }
  </script>
</body>

</html>