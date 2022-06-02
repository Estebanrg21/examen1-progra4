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
require_once(__DIR__."/templates/sessionValidation.php");
require_once(__DIR__."/models/FoodTime.php");
require_once(__DIR__."/models/Food.php");
require_once(__DIR__."/database/database.php");
require_once(__DIR__."/util.php");
[$db,$connection] = Database::getConnection();
$classModal="";
if (areSubmitted(['getByDate1','getByDate2','fId'])) {
  try {
    $date1 =(new DateTime($_POST['getByDate1']));
    $date2 =(new DateTime($_POST['getByDate2']));
    if($date1->diff($date2)->days <= 30){
      if(!Food::genReport($date1->format('Y-m-d'),$date2->format('Y-m-d'),((!empty($_POST['fId'])?$_POST['fId']:null)))) {
        $getByDateError="No se encontraron registros";  
      }
    }else{
      $getByDateError="Rango de fechas inválido";  
    }
  } catch (\Throwable $th) {
    $getByDateError="Fecha inválida";
  }
}
if (areSubmitted(['id','foodTime'])) {
  if(checkInput(['id','foodTime'])){
    try {
      $food = new Food($_POST['id'], $_POST['foodTime']);
      $food->connection = $connection;
      $result = $food->save();
      if($result!=201){
        $popDangerModal = true;
        $errorMessage = "Hubo un error";
        $classModal = "danger";
      }else{
        $popSuccessModal = true;
        $successMessage= "Tiempo canjeado!";
        $classModal = "success";
      }
    } catch (\Throwable $th) {
      $popDangerModal = true;
        $errorMessage = "Hubo un error";
        $classModal = "danger";
    }   
  }
}

if (isset($_GET['id']) && isset($_GET['fId'])) {
  if(empty($_GET['id']) || empty($_GET['fId'])){
    $searchError="Campos no pueden estar en blanco";
  }else{
    $food = new Food($_GET['id'], $_GET['fId']);
    $food->connection = $connection;
    try {
      $studentStatus = $food->canStudentExchange();
      if ($studentStatus) {
        $askToInsert = true;
        $popSuccessModal = true;
        $successMessage= "Estudiante cuenta con el tiempo disponible";
        $question = " ¿Desea marcarlo como canjeado?";
        $id = $food->student;
        $foodTime = $food->foodTime;
        $classModal = "success";
      }
      if($studentStatus == false){
        $popDangerModal = true;
        $errorMessage = "Estudiante ya canjeó el tiempo";
        $classModal = "danger";
      }

    } catch (\Throwable $th) {
      $searchError="Hubo un error";
    }    
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php  $hdTitle="SCOT: Panel"; require_once(__DIR__ . '/templates/header.php') ?>

<body class="g-sidenav-show  bg-gray-100">
  <?php if(isset($popSuccessModal) || isset($popDangerModal)) : ?>
    <script>
      window.history.replaceState({}, document.title, `${window.location.pathname}`);
    </script>
    <div class="modal fade <?php echo $classModal?>-modal-container" id="<?php echo $classModal?>Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="<?php echo $classModal?>ModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down position-relative">
        <div class="px-3  modal-content <?php echo $classModal?>-modal d-flex flex-column justify-content-around" >
        <button type="button" class="btn-close position-absolute top-2 m-0 p-0 end-4" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="<?php echo $classModal?>-modal-animation">
            <script src="/assets/js/lottie-player.js"></script>
            <?php if(isset($popSuccessModal)): ?>
              <lottie-player src="/assets/lottie-animations/lf20_bkizmjpn.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"    autoplay></lottie-player>
            <?php else : ?>
              <lottie-player src="/assets/lottie-animations/lf20_46u4ucum.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  autoplay></lottie-player>
            <?php endif; ?>
          </div>
          <h3 class="mt-4 text-center text-white text-break"> <?php echo (isset($popSuccessModal)?$successMessage:$errorMessage) ?></h3>
          <?php if(isset($question)): ?>
            <h4 class="mt-4 text-center text-white text-break"> <?php echo $question ?></h4>
            <div class="w-100 d-flex justify-content-between">
              <form action="#" method="post" class="w-40"> 
                <input type="hidden" name="id" value="<?php echo (isset($id)?$id:"") ?>">
                <input type="hidden" name="foodTime" value="<?php echo (isset($foodTime)?$foodTime:"") ?>">
                <button type="submit" data-bs-dismiss="modal" class="btn btn-outline-<?php echo $classModal?> w-100 center align-self-center modal-button-confirm">Si</button>
              </form>
            <button type="button" data-bs-dismiss="modal" class="btn btn-danger w-40 center align-self-center">No</button>
            </div>
          <?php else : ?>
            <button type="button" data-bs-dismiss="modal" class="btn btn-outline-<?php echo $classModal?> w-50 center align-self-center modal-button-confirm">Entendido!</button>
          <?php endif; ?>
          </div>
      </div>
    </div>
  <?php endif; ?>
  <!-- Aside -->
  <?php  $option=0; require_once(__DIR__ . '/templates/aside.php') ?>
  <!-- End Aside -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php $navTitle = "Panel de control"; require_once(__DIR__ . '/templates/navbar.php') ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row mt-4">
      <div class="col-lg-5" style="cursor:pointer;">
          <div class="card h-100 p-3">
            <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('../assets/img/illustrations/pizza.svg');background-position: center;">
              <span class="mask " style="background-image: linear-gradient(to right top, #bc5900, #d07300, #e28e00, #f2aa00, #ffc700);"></span>
              <div class="card-body position-relative z-index-1 d-flex flex-column h-100 ">
                <h5 class="text-white font-weight-bolder mb-4 pt-2">Validar Estudiantes!</h5>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  
                  <?php if(isset($searchError)) : ?>
                    <p class="text-danger text-xs font-weight-bolder p-0 m-0" id="errorMessageValidate"><?php echo $searchError;?></p>
                  <?php endif; ?>
                  <p class="btn btn-link pe-3 p-0 m-0 text-xs" id="clearValidate">Limpiar</p>
                </div>
              <form action="#" method="get">
              <div class="align-self-center  d-flex flex-wrap">
                <div class="input-group flex-md-fill mb-5" style="z-index:99;"> 
                    <select class="form-select" name="fId" id="validateSelect" aria-label="Default select example">
                      <option selected value="">Tiempo de comida</option>
                      <?php

                        $foodTimes = FoodTime::getAllFoodTimes($connection);
                        if($foodTimes){
                          while($row = $foodTimes->fetch_array(MYSQLI_ASSOC)){
                            echo "<option  value=\"".$row["id"]."\">".$row["name"]."</option>";
                          }
                        }

                      ?>
                  </select>
                </div>
                <div class="input-group flex-md-fill" style="z-index:99;">
                  <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                  <input type="text" class="form-control" name="id" id="validateStudent" placeholder="Cédula de estudiante">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Buscar</button>
                  </div>
              </div>
              </form>
              </div>
            </div>
          </div>
        </div>  
        <div class="col-lg-7 mb-lg-0 mb-4">
          <div class="">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-6">
                  <div class="d-flex flex-column h-100">
                    <h2 class="font-weight-bolder">Sistema de Control de Tiempos de Comida </h2>
                    <p class="mb-2">
                      Bienvenida(o) al panel de control donde podrá hacer las siguietes operaciones:
                    </p>
                    <h6>Estudiantes</h6>
                    <ul class="mb-2">
                      <?php if($_SESSION['isAdmin'] || $_SESSION['isSuper']) : ?>  
                        <li>
                          Agregar
                          </li>
                          <li>
                          Actualizar
                          </li>
                          <li>
                          Eliminar
                          </li>
                        <?php endif; ?>
                      <li>Validar estudiante</li>
                    </ul>
                    <?php  
                      if(isset($_SESSION['isSuper'])){
                        if($_SESSION['isSuper']){
                          echo "Además, podrá agregar nuevos usuarios administradores del sistema.";
                        }
                      }
                      ?>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
        <?php if($_SESSION['isAdmin'] || $_SESSION['isSuper']) : ?>  
          <div class="col-12 col-xl-4 mt-4">
            <div class="card  d-flex">
              <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Generar reporte </h6>
              </div>
              
                <div class="card-body p-3">
                  <form action="#" method="post" >
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <?php if(isset($getByDateError)) : ?>
                      <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageReport"><?php echo $getByDateError;?></p>
                    <?php endif; ?>
                    <p class="btn btn-link pe-3 p-0 m-0 text-xs" id="clearReport">Limpiar</p>
                  </div>
                  <div class=" mb-3" style="z-index:99;"> 
                  <h6 class="text-uppercase text-body text-xs font-weight-bolder">Tiempo de alimentación a consultar</h6>
                        <select class="form-select" name="fId" id="reportSelect" aria-label="Default select example">
                          <option selected value="">Tiempo de comida</option>
                          <?php

                            $foodTimes = FoodTime::getAllFoodTimes($connection);
                            if($foodTimes){
                              while($row = $foodTimes->fetch_array(MYSQLI_ASSOC)){
                                echo "<option  value=\"".$row["id"]."\">".$row["name"]."</option>";
                              }
                            }

                          ?>
                      </select>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Fecha de inicio a consultar</h6>
                          <div>
                            <input type="date" class="form-control" id="dateInput1" name="getByDate1" aria-label="Fecha" aria-describedby="text-addon" value="">
                          </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Fecha de terminación a consultar</h6>
                          <div>
                            <input type="date" class="form-control" id="dateInput2" name="getByDate2" aria-label="Fecha" aria-describedby="text-addon" value="">
                          </div>
                    </div>
                    <div class="text-center">
                          <button type="submit" class="btn bg-gradient-dark w-40 my-4 mb-2">Generar</button>
                        </div>
                  </form>
                </div>
              
            </div>
          </div>         
        <?php endif; ?>
      </div>
     
      
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
<?php  require_once(__DIR__ . '/templates/scripts.php') ?>
<!-- End Scripts -->
  <script>
     document.getElementById("clearValidate").addEventListener("click",(e)=>{
        window.history.replaceState({}, document.title, `${window.location.pathname}`);
        document.getElementById("validateStudent").value = "";
        document.getElementById("validateSelect").selectedIndex =0;
        let errorDialog = document.getElementById('errorMessageValidate');
        if(errorDialog)errorDialog.remove();
    });
    document.getElementById("clearReport").addEventListener("click",(e)=>{
        window.history.replaceState({}, document.title, `${window.location.pathname}`);
        document.getElementById("dateInput1").value = "";
        document.getElementById("dateInput2").value = "";
        document.getElementById("reportSelect").selectedIndex =0;
        let errorDialog = document.getElementById('errorMessageReport');
        if(errorDialog)errorDialog.remove();
    });
  </script>
  
</body>

</html>