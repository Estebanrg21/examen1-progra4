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
if(!$_SESSION['verification']){
  header("Location: /index.php");
}else if(!$_SESSION['verification'] || (!$_SESSION['isAdmin'] && !$_SESSION['isSuper'])){
  header("Location: /dashboard.php");
}

$now = time();

if($now > $_SESSION['expire']) {
  session_destroy();
  session_start();
  $_SESSION['wasRedirected']=true;
  header("Location: /session-expired.php");
}

require_once(__DIR__."/../models/Section.php");
require_once(__DIR__."/../database/database.php");
require_once(__DIR__."/../util.php");
[$db,$connection] = Database::getConnection();
$classModal = "";
if(areSubmitted(Section::$INSERT_REQUIRED_FIELDS ) || areSubmitted(Section::$UPDATE_REQUIRED_FIELDS )){
  if (checkInput(Section::$INSERT_REQUIRED_FIELDS) || checkInput(Section::$UPDATE_REQUIRED_FIELDS)) {
    $sectionResult = new Section($_POST['section'],(isset($_POST['description'])?$_POST['description']:null));
    $sectionResult->connection = $connection;
    $result = $sectionResult->save();
    if($result == 500 || $result == 400){
      if($result==500)
        $errorMessage = "Hubo un error en el servidor";
      if($result==400)
        $errorMessage = "Campos en formato erróneo";
      $popErrorModal = true;
      $classModal = "danger";
    }
    if($result == 200 || $result == 201 || $result == 205 ){
      if($result==200)
        $successMessage = "Sección actualizada correctamente!";
      if($result==201)
        $successMessage = "Sección creada correctamente!";
      if($result==205)
        $successMessage = "La sección no necesita actualizarse";
      $popSuccessModal =true;  
      $classModal = "success";
    }   
  }else{
    $errorSubmission = "Los campos no pueden estar vacíos";
  }
  
}

if (isset($_GET['id']) && isset($_GET['m'])) {
  $sectionResult = Section::getSection($connection,$_GET['id'],$_GET['m']=='d');
  if($sectionResult){
    if($_GET['m']!='d'){
      $section = $sectionResult['id'];
      $blockIdInput =true;
      $description =  $sectionResult['description'];
      $formText = "Actualizar sección";
      $formButtonText = "Actualizar";
    }else{
      $result = Section::removeSection($connection,$_GET['id']);
      if($result = 204){
        $successMessage = "Sección eliminada correctamente";
        $popSuccessModal =true;  
        $classModal = "success";
      }else{
        if($result==500)
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

<?php  $hdTitle="SCOT: Secciones"; require_once(__DIR__ . '../../templates/header.php') ?>
<body class="g-sidenav-show  bg-gray-100">
  <?php if(isset($popSuccessModal) || isset($popErrorModal)) : ?>
    <script>
      window.history.replaceState({}, document.title, `${window.location.pathname}`);
    </script>
    <div class="modal fade <?php echo $classModal?>-modal-container" id="<?php echo $classModal?>Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="<?php echo $classModal?>ModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down position-relative">
        <div class="px-3  modal-content <?php echo $classModal?>-modal d-flex flex-column justify-content-around" >
        <button type="button" class="btn-close position-absolute top-2 m-0 p-0 end-4" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="<?php echo $classModal?>-modal-animation">
            <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
            <?php if(isset($popSuccessModal)): ?>
              <lottie-player src="https://assets10.lottiefiles.com/packages/lf20_bkizmjpn.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"    autoplay></lottie-player>
            <?php else : ?>
              <lottie-player src="https://assets3.lottiefiles.com/packages/lf20_46u4ucum.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  autoplay></lottie-player>
            <?php endif; ?>
          </div>
          <h3 class="mt-4 text-center text-white text-break"> <?php echo (isset($popSuccessModal)?$successMessage:$errorMessage) ?></h3>
          
          <button type="button" data-bs-dismiss="modal" class="btn btn-outline-<?php echo $classModal?> w-50 center align-self-center modal-button-confirm">Entendido!</button>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <!-- Aside -->
    <?php  $option=2; require_once(__DIR__ . '../../templates/aside.php') ?>
  <!-- End Aside -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php $navTitle = "Administración de secciones"; require_once(__DIR__ . '../../templates/navbar.php') ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row mt-4">
      <!-- Form -->
      <div class="col-12 col-xl-4">
          <div class="card h-100">
            <div class="card-header pb-0 p-3 border-0 d-flex align-items-center">
              <h6 class="mb-0" id="mainFormTitle"><?php echo (isset($formText)?$formText:"Crear sección")?></h6>
              <p class="btn btn-link pe-3 ps-0 mb-0 ms-auto" id="clearMainForm">Limpiar</p>
            </div>
            <div class="card-body p-3">
              <form role="form" method="POST" action="#" id="mainForm">
              <?php if(isset($errorSubmission)) : ?>
                <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageMainForm"><?php echo $errorSubmission;?></p>
              <?php endif; ?>
                <div class="mb-3">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Identificador de sección</h6>
                      <div>
                        <input type="hidden" name="section" value="<?php echo (isset($section)?$section:"")  ?>">
                        <input type="text" class="form-control" id="formSection" placeholder="Sección" name="section" aria-label="Section" aria-describedby="section-addon" value="<?php echo (isset($section)?$section:"")  ?>" <?php echo (isset($blockIdInput)?"disabled":"")  ?>>
                      </div>
                </div>
                <div class="mb-3">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Descripción</h6>
                      <div>
                        <textarea class="form-control" id="formDescription" name="description" rows="3"><?php echo (isset($description)?$description:"")  ?></textarea>
                      </div>
                </div>
                <div class="text-center">
                  <button type="submit" id="mainFormButton" class="btn bg-gradient-info w-100 mt-4 mb-0"><?php echo (isset($formButtonText)?$formButtonText:"Crear")?></button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <!-- End Form -->
      <!-- Show sections -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Secciones</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Identificador</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Descripción</th>                    
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sections = Section::getAllSections($connection);
                      if($sections){
                        while($row = $sections->fetch_array(MYSQLI_ASSOC)){
                          echo "
                            <tr>
                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\" value=\"".$row['id']."\" section-id />
                                <p class=\"text-xs font-weight-bold mb-0 \">".$row["id"]."</p>
                              </td>
                              
                              <td class=\"align-middle text-center text-sm text-wrap\">
                                <input type=\"hidden\"  value=\"".$row['description']."\" section-description />
                                <p class=\"text-xs font-weight-bold mb-0 text-wrap\">".$row["description"]."</p>
                              </td>";
                              echo "<td><div class=\"d-flex justify-content-center align-items-center\">";
                              echo "
                                <form action=\"#\" method=\"get\" class=\"m-0 p-0\">
                                  <input type=\"hidden\" value=\"".$row['id']."\" name=\"id\" />
                                  <input type=\"hidden\" value=\"u\" name=\"m\" />
                                  <button type=\"submit\" class=\"btn btn-link text-dark px-3 mb-0 \" >
                                    <i class=\"fas fa-pencil-alt text-dark me-2\" aria-hidden=\"true\"></i>Actualizar
                                  </button>
                                </form>
                                ";  
                              echo "
                                <form action=\"#\" method=\"get\" class=\"m-0 p-0\">
                                  <input type=\"hidden\" value=\"".$row['id']."\" name=\"id\" />
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
                      if(deleteButtons){
                        deleteButtons.forEach((element)=>{
                          element.addEventListener('click',(e)=>{
                            e.preventDefault();
                            if(confirm('¿Desea eliminar la sección?')){
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
      <!-- End Show sections -->
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
  <script>
    document.getElementById("clearMainForm").addEventListener("click",(e)=>{
        window.history.replaceState({}, document.title, `${window.location.pathname}`);
        document.getElementById("mainFormTitle").textContent = "Crear usuario";
        let mainField = document.getElementById("formSection");
        mainField.value = "";
        mainField.removeAttribute('disabled');
        document.getElementById("formDescription").value = "";
        document.getElementById("mainFormButton").textContent = "Crear";
        let formMsg = document.getElementById("errorMessageMainForm");
        if(formMsg)formMsg.remove();
    });
  </script>
  <?php if(isset($popSuccessModal) || isset($popErrorModal)) : ?>
      <script type="text/javascript">
         let modal = new bootstrap.Modal(document.getElementById('<?php echo $classModal?>Modal'));
         modal.show();
    </script>
  <?php endif; ?>
</body>

</html>