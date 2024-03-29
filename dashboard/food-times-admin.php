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
$sessionCondition =  (!$_SESSION['isAdmin'] && !$_SESSION['isSuper']);
$headerLoc = "/dashboard.php";
require_once(__DIR__ . "/../templates/sessionValidation.php");
require_once(__DIR__ . "/../models/FoodTime.php");
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../util.php");
[$db, $connection] = Database::getConnection();
$classModal = "";
if (areSubmitted(FoodTime::$INSERT_REQUIRED_FIELDS)) {
  if (checkInput(FoodTime::$INSERT_REQUIRED_FIELDS)) {
    $foodTime = new FoodTime(
      (isset($_POST['name']) ? $_POST['name'] : null),
      (isset($_POST['description']) ? $_POST['description'] : null),
      (isset($_POST['id']) ? $_POST['id'] : null)
    );
    $foodTime->connection = $connection;
    $result = $foodTime->save();
    if ($result == 500 || $result == 400) {
      if ($result == 500)
        $errorMessage = "Hubo un error en el servidor";
      if ($result == 400)
        $errorMessage = "Campos en formato erróneo";
      $popErrorModal = true;
      $classModal = "danger";
    }
    if ($result == 200 || $result == 201 || $result == 205) {
      if ($result == 200)
        $successMessage = "Tiempo de comida actualizado correctamente!";
      if ($result == 201)
        $successMessage = "Tiempo de comida creado correctamente!";
      if ($result == 205)
        $successMessage = "Tiempo de comida no necesita actualizarse";
      $popSuccessModal = true;
      $classModal = "success";
    }
  } else {
    $errorSubmission = "Los campos no pueden estar vacíos";
  }
}

if (isset($_GET['id']) && isset($_GET['m'])) {
  $foodTime = FoodTime::getFoodTime($connection, $_GET['id'], $_GET['m'] == 'd');
  if ($foodTime) {
    if ($_GET['m'] != 'd') {
      $id = $foodTime['id'];
      $blockIdInput = true;
      $name = $foodTime['name'];
      $description =  $foodTime['description'];
      $formText = "Actualizar tiempo de comida";
      $formButtonText = "Actualizar";
    } else {
      $result = FoodTime::removeFoodTime($connection, $_GET['id']);
      if ($result = 204) {
        $successMessage = "Tiempo de comida eliminado correctamente";
        $popSuccessModal = true;
        $classModal = "success";
      } else {
        if ($result == 500)
          $errorMessage = "Hubo un error en el servidor";
        $popErrorModal = true;
        $classModal = "danger";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php $hdTitle = "SCOT: Tiempos";
require_once(__DIR__ . '../../templates/header.php') ?>

<body class="g-sidenav-show ">
  <!-- Modal -->
  <?php require_once(__DIR__ . '../../templates/modal.php') ?>
  <!-- End Modal -->
  <!-- Aside -->
  <?php $option = 3;
  require_once(__DIR__ . '../../templates/aside.php') ?>
  <!-- End Aside -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php $navTitle = "Administración de tiempos de comida";
    require_once(__DIR__ . '../../templates/navbar.php') ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row mt-4">
        <!-- Form -->
        <div class="col-12 col-xl-4">
          <div class="card h-100">
            <div class="card-header pb-0 p-3 border-0 d-flex align-items-center">
              <h6 class="mb-0" id="mainFormTitle"><?php echo (isset($formText) ? $formText : "Crear tiempo de comida") ?></h6>
              <p class="btn btn-link pe-3 ps-0 mb-0 ms-auto" id="clearMainForm">Limpiar</p>
            </div>
            <div class="card-body p-3">
              <form role="form" method="POST" action="#" id="mainForm">
                <?php if (isset($errorSubmission)) : ?>
                  <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageMainForm"><?php echo $errorSubmission; ?></p>
                <?php endif; ?>

                <?php if (isset($blockIdInput)) : ?>
                  <div class="mb-3" id="mainField">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Identificador de tiempo</h6>
                    <div>
                      <input type="hidden" name="id" value="<?php echo (isset($id) ? $id : "")  ?>">
                      <input type="text" class="form-control" id="formId" aria-label="id" aria-describedby="food-time-addon" value="<?php echo (isset($id) ? $id : "")  ?>" <?php echo (isset($blockIdInput) ? "disabled" : "")  ?>>
                    </div>
                  </div>
                <?php endif; ?>

                <div class="mb-3">
                  <h6 class="text-uppercase text-body text-xs font-weight-bolder">Nombre</h6>
                  <div>
                    <input type="text" class="form-control" id="mainFormName" placeholder="Nombre" name="name" aria-label="Nombre" aria-describedby="text-addon" value="<?php echo (isset($name) ? $name : "")  ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <h6 class="text-uppercase text-body text-xs font-weight-bolder">Descripción</h6>
                  <div>
                    <textarea class="form-control" id="formDescription" name="description" rows="3"><?php echo (isset($description) ? $description : "")  ?></textarea>
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
        <!-- Show Food Times -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card mb-4">
              <div class="card-header pb-0">
                <h6>Tiempos de comida</h6>
              </div>
              <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                  <table class="table align-items-center mb-0">
                    <thead>
                      <tr>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Identificador</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Nombre</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Descripción</th>
                        <th class="text-secondary opacity-7"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $foodTimes = FoodTime::getAllFoodTimes($connection);
                      if ($foodTimes) {
                        while ($row = $foodTimes->fetch_array(MYSQLI_ASSOC)) {
                          echo "
                            <tr>
                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\" value=\"" . $row['id'] . "\" food-time-id />
                                <p class=\"text-xs font-weight-bold mb-0 \">" . $row["id"] . "</p>
                              </td>
                              
                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\"  value=\"" . $row['name'] . "\" food-time-name />
                                <p class=\"text-xs font-weight-bold mb-0\">" . $row["name"] . "</p>
                              </td>

                              <td class=\"align-middle text-center text-sm text-wrap\">
                                <input type=\"hidden\"  value=\"" . $row['description'] . "\" food-time-description />
                                <p class=\"text-xs font-weight-bold mb-0 text-wrap\">" . $row["description"] . "</p>
                              </td>";
                          echo "<td><div class=\"d-flex justify-content-center align-items-center\">";
                          echo "
                                <form action=\"#\" method=\"get\" class=\"m-0 p-0\">
                                  <input type=\"hidden\" value=\"" . $row['id'] . "\" name=\"id\" />
                                  <input type=\"hidden\" value=\"u\" name=\"m\" />
                                  <button type=\"submit\" class=\"btn btn-link text-dark px-3 mb-0 \" >
                                    <i class=\"fas fa-pencil-alt text-dark me-2\" aria-hidden=\"true\"></i>Actualizar
                                  </button>
                                </form>
                                ";
                          echo "
                                <form action=\"#\" method=\"get\" class=\"m-0 p-0\">
                                  <input type=\"hidden\" value=\"" . $row['id'] . "\" name=\"id\" />
                                  <input type=\"hidden\" value=\"d\" name=\"m\" />
                                  <button class=\"btn btn-link text-danger px-3 mb-0 \" delete-item>
                                    <i class=\"far fa-trash-alt me-2\" aria-hidden=\"true\"></i>Eliminar
                                  </button>
                                </form>
                                ";
                          echo "</div></td>";
                        }
                      }
                      ?>
                      <script>
                        let deleteButtons = Array.prototype.slice.call(document.querySelectorAll('button[delete-item]'));
                        if (deleteButtons) {
                          deleteButtons.forEach((element) => {
                            element.addEventListener('click', (e) => {
                              e.preventDefault();
                              if (confirm('¿Desea eliminar el tiempo de comida?')) {
                                e.target.form.submit();
                              }
                            })
                          });
                        }
                      </script>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End Show Food Times -->
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
      document.getElementById("mainFormTitle").textContent = "Crear tiempo";
      let mainField = document.getElementById("mainField");
      if (mainField) mainField.remove();
      document.getElementById("mainFormName").value = "";
      document.getElementById("formDescription").value = "";
      document.getElementById("mainFormButton").textContent = "Crear";
      let formMsg = document.getElementById("errorMessageMainForm");
      if (formMsg) formMsg.remove();
    });
  </script>
</body>

</html>