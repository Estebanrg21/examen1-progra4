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
}else if(!$_SESSION['isSuper']){
  header("Location: /index.php");
}
require_once(__DIR__."/../models/FoodTime.php");
require_once(__DIR__."/../database/database.php");
require_once(__DIR__."/../util.php");
[$db,$connection] = Database::getConnection();
$classModal = "";
if(areSubmitted(FoodTime::$INSERT_REQUIRED_FIELDS ) ){
  if (checkInput(FoodTime::$INSERT_REQUIRED_FIELDS) ) {
    $foodTime = new FoodTime(
      (isset($_POST['name'])?$_POST['name']:null),
      (isset($_POST['description'])?$_POST['description']:null),
      (isset($_POST['id'])?$_POST['id']:null)
    );
    $foodTime->connection = $connection;
    $result = $foodTime->save();
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
        $successMessage = "Tiempo de comida actualizado correctamente!";
      if($result==201)
        $successMessage = "Tiempo de comida creado correctamente!";
      if($result==205)
        $successMessage = "Tiempo de comida no necesita actualizarse";
      $popSuccessModal =true;  
      $classModal = "success";
    }   
  }else{
    $errorSubmission = "Los campos no pueden estar vacíos";
  }
  
}

if (isset($_GET['id']) && isset($_GET['m'])) {
  $foodTime = FoodTime::getFoodTime($connection,$_GET['id'],$_GET['m']=='d');
  if($foodTime){
    if($_GET['m']!='d'){
      $id = $foodTime['id'];
      $blockIdInput =true;
      $name=$foodTime['name'];
      $description =  $foodTime['description'];
      $formText = "Actualizar tiempo de comida";
      $formButtonText = "Actualizar";
    }else{
      $result = FoodTime::removeFoodTime($connection,$_GET['id']);
      if($result = 204){
        $successMessage = "Tiempo de comida eliminado correctamente";
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

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
  SCOT: Administración de tiempos
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/styles.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.5" rel="stylesheet" />
</head>

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
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main" style="z-index:99;">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="/">
        <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">SCOT</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="/pages/dashboard.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg width="12px" height="12px" viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>shop </title>
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <g transform="translate(1716.000000, 291.000000)">
                      <g transform="translate(0.000000, 148.000000)">
                        <path class="color-background opacity-6" d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"></path>
                        <path class="color-background" d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z"></path>
                      </g>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Inicio</span>
          </a>
        </li>

        
        <?php if(isset($_SESSION['isSuper'])) : ?>
          <?php if($_SESSION['isSuper']) : ?>
            <li class="nav-item">
              <a class="nav-link  " href="../pages/users-admin.php">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve" width="12px" height="12px">
                  <path d="M55.014,45.389l-9.553-4.776C44.56,40.162,44,39.256,44,38.248v-3.381c0.229-0.28,0.47-0.599,0.719-0.951  c1.239-1.75,2.232-3.698,2.954-5.799C49.084,27.47,50,26.075,50,24.5v-4c0-0.963-0.36-1.896-1-2.625v-5.319  c0.056-0.55,0.276-3.824-2.092-6.525C44.854,3.688,41.521,2.5,37,2.5s-7.854,1.188-9.908,3.53c-1.435,1.637-1.918,3.481-2.064,4.805  C23.314,9.949,21.294,9.5,19,9.5c-10.389,0-10.994,8.855-11,9v4.579c-0.648,0.706-1,1.521-1,2.33v3.454  c0,1.079,0.483,2.085,1.311,2.765c0.825,3.11,2.854,5.46,3.644,6.285v2.743c0,0.787-0.428,1.509-1.171,1.915l-6.653,4.173  C1.583,48.134,0,50.801,0,53.703V57.5h14h2h44v-4.043C60,50.019,58.089,46.927,55.014,45.389z M14,53.262V55.5H2v-1.797  c0-2.17,1.184-4.164,3.141-5.233l6.652-4.173c1.333-0.727,2.161-2.121,2.161-3.641v-3.591l-0.318-0.297  c-0.026-0.024-2.683-2.534-3.468-5.955l-0.091-0.396l-0.342-0.22C9.275,29.899,9,29.4,9,28.863v-3.454  c0-0.36,0.245-0.788,0.671-1.174L10,23.938l-0.002-5.38C10.016,18.271,10.537,11.5,19,11.5c2.393,0,4.408,0.553,6,1.644v4.731  c-0.64,0.729-1,1.662-1,2.625v4c0,0.304,0.035,0.603,0.101,0.893c0.027,0.116,0.081,0.222,0.118,0.334  c0.055,0.168,0.099,0.341,0.176,0.5c0.001,0.002,0.002,0.003,0.003,0.005c0.256,0.528,0.629,1,1.099,1.377  c0.005,0.019,0.011,0.036,0.016,0.054c0.06,0.229,0.123,0.457,0.191,0.68l0.081,0.261c0.014,0.046,0.031,0.093,0.046,0.139  c0.035,0.108,0.069,0.215,0.105,0.321c0.06,0.175,0.123,0.356,0.196,0.553c0.031,0.082,0.065,0.156,0.097,0.237  c0.082,0.209,0.164,0.411,0.25,0.611c0.021,0.048,0.039,0.1,0.06,0.147l0.056,0.126c0.026,0.058,0.053,0.11,0.079,0.167  c0.098,0.214,0.194,0.421,0.294,0.621c0.016,0.032,0.031,0.067,0.047,0.099c0.063,0.125,0.126,0.243,0.189,0.363  c0.108,0.206,0.214,0.4,0.32,0.588c0.052,0.092,0.103,0.182,0.154,0.269c0.144,0.246,0.281,0.472,0.414,0.682  c0.029,0.045,0.057,0.092,0.085,0.135c0.242,0.375,0.452,0.679,0.626,0.916c0.046,0.063,0.086,0.117,0.125,0.17  c0.022,0.029,0.052,0.071,0.071,0.097v3.309c0,0.968-0.528,1.856-1.377,2.32l-2.646,1.443l-0.461-0.041l-0.188,0.395l-5.626,3.069  C15.801,46.924,14,49.958,14,53.262z M58,55.5H16v-2.238c0-2.571,1.402-4.934,3.659-6.164l8.921-4.866  C30.073,41.417,31,39.854,31,38.155v-4.018v-0.001l-0.194-0.232l-0.038-0.045c-0.002-0.003-0.064-0.078-0.165-0.21  c-0.006-0.008-0.012-0.016-0.019-0.024c-0.053-0.069-0.115-0.152-0.186-0.251c-0.001-0.002-0.002-0.003-0.003-0.005  c-0.149-0.207-0.336-0.476-0.544-0.8c-0.005-0.007-0.009-0.015-0.014-0.022c-0.098-0.153-0.202-0.32-0.308-0.497  c-0.008-0.013-0.016-0.026-0.024-0.04c-0.226-0.379-0.466-0.808-0.705-1.283c0,0-0.001-0.001-0.001-0.002  c-0.127-0.255-0.254-0.523-0.378-0.802l0,0c-0.017-0.039-0.035-0.077-0.052-0.116h0c-0.055-0.125-0.11-0.256-0.166-0.391  c-0.02-0.049-0.04-0.1-0.06-0.15c-0.052-0.131-0.105-0.263-0.161-0.414c-0.102-0.272-0.198-0.556-0.29-0.849l-0.055-0.178  c-0.006-0.02-0.013-0.04-0.019-0.061c-0.094-0.316-0.184-0.639-0.26-0.971l-0.091-0.396l-0.341-0.22  C26.346,25.803,26,25.176,26,24.5v-4c0-0.561,0.238-1.084,0.67-1.475L27,18.728V12.5v-0.354l-0.027-0.021  c-0.034-0.722,0.009-2.935,1.623-4.776C30.253,5.458,33.081,4.5,37,4.5c3.905,0,6.727,0.951,8.386,2.828  c1.947,2.201,1.625,5.017,1.623,5.041L47,18.728l0.33,0.298C47.762,19.416,48,19.939,48,20.5v4c0,0.873-0.572,1.637-1.422,1.899  l-0.498,0.153l-0.16,0.495c-0.669,2.081-1.622,4.003-2.834,5.713c-0.297,0.421-0.586,0.794-0.837,1.079L42,34.123v4.125  c0,1.77,0.983,3.361,2.566,4.153l9.553,4.776C56.513,48.374,58,50.78,58,53.457V55.5z"/>
                  <g>
                  </g>
                  
                </svg>
                </div>
                <span class="nav-link-text ms-1">Administrar Usuarios</span>
              </a>
            </li>
          <?php endif; ?>
        <?php endif; ?>
          
        <li class="nav-item">
          <a class="nav-link  " href="../pages/sections-admin.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg  width="14px" height="14px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                <g>
                  <g>
                    <g>
                      <polygon points="31.715,111.763 480.285,111.763 480.285,360.062 495.991,360.062 495.991,96.057 16.009,96.057 16.009,360.062 
                        31.715,360.062 			"/>
                      <path d="M431.953,368.218v-24.014H320.191v24.014H0v47.725h512v-47.725H431.953z M335.896,359.91h80.352v8.308h-80.352V359.91z
                        M496.294,400.237H15.706v-16.314h480.589V400.237z"/>
                      <polygon points="416.095,320.191 383.925,320.191 383.925,304.029 368.219,304.029 368.219,320.191 336.047,320.191 
                        336.047,335.896 416.095,335.896 			"/>
                      <polygon points="116.497,222.5 127.925,211.073 139.352,222.5 150.458,211.394 139.031,199.967 150.458,188.539 139.352,177.434 
                        127.925,188.861 116.497,177.434 105.39,188.539 116.819,199.967 105.39,211.394 			"/>
                      <polygon points="298.475,218.428 287.048,229.856 275.62,218.428 264.514,229.534 275.942,240.961 264.514,252.39 
                        275.62,263.495 287.048,252.067 298.475,263.495 309.582,252.39 298.154,240.961 309.582,229.534 			"/>
                      <polygon points="410.542,218.428 399.114,229.856 387.688,218.428 376.581,229.534 388.009,240.961 376.581,252.39 
                        387.688,263.495 399.114,252.067 410.542,263.495 421.648,252.39 410.221,240.961 421.648,229.534 			"/>
                      <polygon points="139.352,256.51 127.925,267.937 116.497,256.51 105.39,267.615 116.819,279.043 105.39,290.47 116.497,301.576 
                        127.925,290.149 139.352,301.576 150.458,290.47 139.031,279.043 150.458,267.615 			"/>
                      <polygon points="223.829,264.005 223.829,247.844 239.991,247.844 239.991,232.138 223.829,232.138 223.829,215.976 
                        208.123,215.976 208.123,232.138 191.962,232.138 191.962,247.844 208.123,247.844 208.123,264.005 			"/>
                      <rect x="79.899" y="232.138" width="96.055" height="15.706"/>
                      <rect x="328.046" y="224.128" width="32.018" height="15.706"/>
                      <rect x="328.046" y="248.147" width="32.018" height="15.706"/>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1 text-wrap">Administración de secciones</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link  active" href="../pages/food-times-admin.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <svg width="14px" height="14px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 600.801 600.801" style="enable-background:new 0 0 600.801 600.801;" xml:space="preserve">
                <g>
                  <g>
                    <path d="M542.166,229.165H58.635c-16.279,0-31.914,6.892-42.895,18.908C4.737,260.114-0.732,276.345,0.734,292.604
                      c4.154,46.036,22.405,90.355,52.781,128.167c25.49,31.73,59.006,58.253,97.644,77.397c4.708,27.366,28.61,48.254,57.299,48.254
                      h183.886c28.689,0,52.592-20.888,57.299-48.254c38.637-19.145,72.154-45.667,97.645-77.397
                      c30.375-37.812,48.627-82.131,52.779-128.167c1.467-16.259-4.002-32.49-15.006-44.531
                      C574.08,236.057,558.445,229.165,542.166,229.165z M407.645,480.168v8.115c0,8.449-6.85,15.3-15.301,15.3H208.458
                      c-8.45,0-15.3-6.851-15.3-15.3v-8.115c0-6.144-3.687-11.664-9.333-14.085c-53.153-22.79-95.683-60.68-119.677-106.976h472.506
                      c-23.996,46.296-66.525,84.186-119.678,106.976C411.33,468.504,407.645,474.024,407.645,480.168z M47.994,316.267
                      c-2.199-8.98-3.75-18.162-4.593-27.511c-0.811-8.986,6.21-16.751,15.234-16.751h483.533c9.023,0,16.045,7.765,15.232,16.751
                      c-0.842,9.349-2.393,18.53-4.592,27.511H47.994z"/>
                    <path d="M392.344,546.923H208.458c-28.55,0-52.795-20.349-57.748-48.419c-38.516-19.123-72.255-45.89-97.584-77.419
                      C22.688,383.196,4.399,338.784,0.236,292.649c-1.479-16.399,4.037-32.77,15.135-44.914c11.075-12.12,26.844-19.071,43.264-19.071
                      h483.531c16.42,0,32.188,6.951,43.264,19.071c11.098,12.144,16.614,28.514,15.135,44.914
                      c-4.161,46.133-22.449,90.545-52.888,128.436c-25.327,31.527-59.067,58.294-97.585,77.419
                      C445.14,526.574,420.895,546.923,392.344,546.923z M58.635,229.665c-16.139,0-31.639,6.833-42.525,18.746
                      C5.201,260.348-0.222,276.439,1.232,292.56c4.145,45.938,22.359,90.165,52.672,127.898c25.288,31.479,58.995,58.196,97.476,77.263
                      l0.228,0.113l0.043,0.25c4.769,27.72,28.659,47.839,56.806,47.839h183.886c28.147,0,52.038-20.119,56.806-47.839l0.043-0.25
                      l0.229-0.113c38.482-19.068,72.189-45.785,97.477-77.263c30.314-37.735,48.527-81.963,52.671-127.898
                      c1.454-16.12-3.968-32.212-14.877-44.149c-10.886-11.913-26.386-18.746-42.525-18.746H58.635z M392.344,504.083H208.458
                      c-8.712,0-15.8-7.088-15.8-15.8v-8.115c0-5.925-3.544-11.273-9.03-13.625c-53.362-22.88-95.952-60.953-119.924-107.205
                      l-0.378-0.73h474.151l-0.378,0.73c-23.974,46.252-66.564,84.325-119.925,107.205c-5.485,2.352-9.029,7.699-9.029,13.625v8.115
                      C408.145,496.995,401.057,504.083,392.344,504.083z M64.972,359.607c23.935,45.717,66.182,83.349,119.05,106.016
                      c5.854,2.51,9.636,8.219,9.636,14.545v8.115c0,8.161,6.639,14.8,14.8,14.8h183.886c8.161,0,14.801-6.639,14.801-14.8v-8.115
                      c0-6.326,3.782-12.035,9.635-14.545c52.867-22.667,95.114-60.299,119.051-106.016H64.972z M553.201,316.767H47.602l-0.094-0.381
                      c-2.224-9.081-3.773-18.362-4.605-27.585c-0.401-4.446,1.09-8.88,4.092-12.165c3.023-3.309,7.157-5.131,11.64-5.131h483.533
                      c4.483,0,8.617,1.822,11.641,5.131c3.001,3.285,4.492,7.719,4.09,12.165c-0.83,9.217-2.379,18.498-4.604,27.585L553.201,316.767z
                      M48.387,315.767h504.029c2.163-8.916,3.673-18.017,4.486-27.056c0.377-4.167-1.021-8.322-3.832-11.4
                      c-2.832-3.1-6.704-4.806-10.902-4.806H58.635c-4.198,0-8.07,1.707-10.902,4.806c-2.812,3.078-4.21,7.233-3.834,11.4
                      C44.715,297.755,46.225,306.855,48.387,315.767z"/>
                  </g>
                  <g>
                    <path d="M169.734,133.492c-7.951-8.275-22.371-18.139-45.914-18.139c-13.135,0-22.812-4.4-29.586-13.451
                      c-6.423-8.583-8.701-19.427-8.701-26.104c0-11.792-9.53-21.359-21.309-21.42c-11.956-0.061-21.626,10.078-21.529,22.033
                      c0.145,17.815,6.566,36.895,17.24,51.158c10.455,13.971,30.025,30.624,63.886,30.624c10.143,0,13.74,3.671,14.922,4.877
                      c2.687,2.741,4.757,7.714,6.195,12.594c1.904,6.464,7.9,10.859,14.64,10.859h12.664c9.923,0,17.244-9.303,14.864-18.937
                      C184.358,156.461,179.227,143.371,169.734,133.492z"/>
                    <path d="M172.242,187.023h-12.664c-6.957,0-13.174-4.613-15.119-11.218c-1.19-4.038-3.21-9.464-6.073-12.385
                      c-1.147-1.17-4.635-4.727-14.565-4.727c-34.069,0-53.764-16.763-64.287-30.824c-10.712-14.315-17.195-33.551-17.34-51.454
                      c-0.048-5.95,2.345-11.803,6.565-16.057c4.145-4.179,9.599-6.48,15.358-6.48l0.109,0c12.024,0.062,21.806,9.895,21.806,21.92
                      c0,6.602,2.253,17.321,8.602,25.805c6.673,8.916,16.22,13.25,29.186,13.25c23.721,0,38.257,9.947,46.274,18.292
                      c8.056,8.383,13.942,19.931,17.497,34.321c1.169,4.731,0.114,9.648-2.895,13.49C181.678,184.812,177.138,187.023,172.242,187.023z
                      M64.118,54.877c-5.49,0-10.692,2.197-14.648,6.185c-4.034,4.066-6.321,9.66-6.275,15.345
                      c0.144,17.698,6.551,36.712,17.141,50.862c10.386,13.879,29.831,30.424,63.486,30.424c10.35,0,14.06,3.783,15.278,5.026
                      c3.008,3.069,5.097,8.657,6.319,12.803c1.821,6.183,7.644,10.501,14.16,10.501h12.664c4.586,0,8.839-2.071,11.667-5.683
                      c2.817-3.599,3.806-8.203,2.711-12.633c-3.512-14.218-9.314-25.613-17.247-33.869c-7.883-8.205-22.185-17.985-45.554-17.985
                      c-13.303,0-23.111-4.465-29.987-13.651c-6.496-8.681-8.801-19.649-8.801-26.404c0-11.476-9.336-20.861-20.811-20.92L64.118,54.877
                      z"/>
                  </g>
                  <g>
                    <path d="M320.838,133.492c-7.951-8.275-22.371-18.139-45.914-18.139c-13.135,0-22.813-4.4-29.586-13.451
                      c-6.423-8.583-8.702-19.427-8.702-26.104c0-11.792-9.53-21.359-21.309-21.42c-11.956-0.061-21.625,10.078-21.529,22.033
                      c0.145,17.815,6.566,36.895,17.24,51.158c10.455,13.97,30.024,30.624,63.885,30.624c10.143,0,13.74,3.671,14.922,4.877
                      c2.687,2.741,4.757,7.714,6.195,12.594c1.905,6.464,7.902,10.859,14.64,10.859h12.664c9.922,0,17.244-9.303,14.863-18.937
                      C335.461,156.461,330.33,143.371,320.838,133.492z"/>
                    <path d="M323.346,187.023h-12.664c-6.956,0-13.173-4.613-15.12-11.218c-1.19-4.037-3.209-9.463-6.073-12.385
                      c-1.146-1.169-4.633-4.727-14.565-4.727c-34.066,0-53.762-16.763-64.286-30.824c-10.712-14.315-17.195-33.551-17.34-51.454
                      c-0.048-5.95,2.345-11.803,6.565-16.058c4.145-4.179,9.599-6.48,15.357-6.48l0.109,0c12.024,0.062,21.806,9.895,21.806,21.92
                      c0,6.601,2.253,17.32,8.602,25.805c6.673,8.916,16.22,13.25,29.186,13.25c23.72,0,38.257,9.947,46.274,18.292
                      c8.056,8.383,13.941,19.93,17.496,34.321c1.169,4.73,0.114,9.647-2.894,13.489C332.781,184.812,328.242,187.023,323.346,187.023z
                      M215.221,54.877c-5.49,0-10.692,2.196-14.647,6.185c-4.034,4.066-6.321,9.659-6.275,15.345
                      c0.144,17.698,6.551,36.712,17.141,50.862c10.387,13.879,29.832,30.424,63.485,30.424c10.352,0,14.061,3.784,15.279,5.027
                      c3.008,3.069,5.096,8.656,6.318,12.803c1.822,6.183,7.645,10.5,14.161,10.5h12.664c4.587,0,8.839-2.072,11.667-5.684
                      c2.817-3.598,3.806-8.203,2.711-12.632c-3.512-14.218-9.314-25.613-17.246-33.869c-7.884-8.205-22.186-17.985-45.554-17.985
                      c-13.303,0-23.112-4.465-29.987-13.651c-6.496-8.681-8.801-19.649-8.801-26.404c0-11.476-9.336-20.861-20.811-20.92
                      L215.221,54.877z"/>
                  </g>
                </g>

              </svg>
            </div>
            <span class="nav-link-text ms-1 text-wrap">Administración de tiempos de comida</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link " href="../pages/students-admin.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
              width="14px" height="14px" viewBox="0 0 29.254 29.254" style="enable-background:new 0 0 29.254 29.254;"
                xml:space="preserve">
                <g>
                  <g>
                    <path d="M13.694,7.487c2.065,0,3.743-1.676,3.743-3.743C17.437,1.676,15.759,0,13.694,0c-2.065,0-3.742,1.676-3.742,3.744
                      C9.952,5.811,11.628,7.487,13.694,7.487z"/>
                    <polygon points="16.572,16.042 16.156,16.918 16.156,16.918 14.032,21.377 14.032,21.377 13.752,21.961 9.974,21.961 
                      7.349,11.463 5.473,11.932 8.463,23.895 9.976,23.895 8.036,28.17 9.208,28.703 11.392,23.895 14.593,23.895 16.691,28.695 
                      17.873,28.18 16,23.895 17.361,23.895 17.361,21.961 14.997,21.961 15.276,21.377 15.276,21.377 16.102,19.641 17.281,17.167 
                      23.781,17.167 23.781,16.042 		"/>
                    <polygon points="15.167,8.65 10.911,8.65 10.279,21.377 12.43,21.377 13.317,21.377 15.99,15.766 16.156,15.418 16.156,15.341 
                      22.713,15.341 22.713,13.375 17.257,13.375 		"/>
                    <polygon points="19.344,18.757 17.238,18.757 15.99,21.377 16.156,21.377 19.344,21.377 19.344,29.254 22.713,29.254 
                      22.713,18.757 22.443,18.757 		"/>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1 text-wrap">Administración de estudiantes</span>
          </a>
        </li>

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link  " href="../pages/profile.html">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg width="12px" height="12px" viewBox="0 0 46 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>customer-support</title>
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g transform="translate(-1717.000000, -291.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <g transform="translate(1716.000000, 291.000000)">
                      <g transform="translate(1.000000, 0.000000)">
                        <path class="color-background opacity-6" d="M45,0 L26,0 C25.447,0 25,0.447 25,1 L25,20 C25,20.379 25.214,20.725 25.553,20.895 C25.694,20.965 25.848,21 26,21 C26.212,21 26.424,20.933 26.6,20.8 L34.333,15 L45,15 C45.553,15 46,14.553 46,14 L46,1 C46,0.447 45.553,0 45,0 Z"></path>
                        <path class="color-background" d="M22.883,32.86 C20.761,32.012 17.324,31 13,31 C8.676,31 5.239,32.012 3.116,32.86 C1.224,33.619 0,35.438 0,37.494 L0,41 C0,41.553 0.447,42 1,42 L25,42 C25.553,42 26,41.553 26,41 L26,37.494 C26,35.438 24.776,33.619 22.883,32.86 Z"></path>
                        <path class="color-background" d="M13,28 C17.432,28 21,22.529 21,18 C21,13.589 17.411,10 13,10 C8.589,10 5,13.589 5,18 C5,22.529 8.568,28 13,28 Z"></path>
                      </g>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link  " href="../logout.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg width="12px" height="20px" viewBox="0 0 40 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>spaceship</title>
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g transform="translate(-1720.000000, -592.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <g transform="translate(1716.000000, 291.000000)">
                      <g transform="translate(4.000000, 301.000000)">
                        <path class="color-background" d="M39.3,0.706666667 C38.9660984,0.370464027 38.5048767,0.192278529 38.0316667,0.216666667 C14.6516667,1.43666667 6.015,22.2633333 5.93166667,22.4733333 C5.68236407,23.0926189 5.82664679,23.8009159 6.29833333,24.2733333 L15.7266667,33.7016667 C16.2013871,34.1756798 16.9140329,34.3188658 17.535,34.065 C17.7433333,33.98 38.4583333,25.2466667 39.7816667,1.97666667 C39.8087196,1.50414529 39.6335979,1.04240574 39.3,0.706666667 Z M25.69,19.0233333 C24.7367525,19.9768687 23.3029475,20.2622391 22.0572426,19.7463614 C20.8115377,19.2304837 19.9992882,18.0149658 19.9992882,16.6666667 C19.9992882,15.3183676 20.8115377,14.1028496 22.0572426,13.5869719 C23.3029475,13.0710943 24.7367525,13.3564646 25.69,14.31 C26.9912731,15.6116662 26.9912731,17.7216672 25.69,19.0233333 L25.69,19.0233333 Z"></path>
                        <path class="color-background opacity-6" d="M1.855,31.4066667 C3.05106558,30.2024182 4.79973884,29.7296005 6.43969145,30.1670277 C8.07964407,30.6044549 9.36054508,31.8853559 9.7979723,33.5253085 C10.2353995,35.1652612 9.76258177,36.9139344 8.55833333,38.11 C6.70666667,39.9616667 0,40 0,40 C0,40 0,33.2566667 1.855,31.4066667 Z"></path>
                        <path class="color-background opacity-6" d="M17.2616667,3.90166667 C12.4943643,3.07192755 7.62174065,4.61673894 4.20333333,8.04166667 C3.31200265,8.94126033 2.53706177,9.94913142 1.89666667,11.0416667 C1.5109569,11.6966059 1.61721591,12.5295394 2.155,13.0666667 L5.47,16.3833333 C8.55036617,11.4946947 12.5559074,7.25476565 17.2616667,3.90166667 L17.2616667,3.90166667 Z"></path>
                        <path class="color-background opacity-6" d="M36.0983333,22.7383333 C36.9280725,27.5056357 35.3832611,32.3782594 31.9583333,35.7966667 C31.0587397,36.6879974 30.0508686,37.4629382 28.9583333,38.1033333 C28.3033941,38.4890431 27.4704606,38.3827841 26.9333333,37.845 L23.6166667,34.53 C28.5053053,31.4496338 32.7452344,27.4440926 36.0983333,22.7383333 L36.0983333,22.7383333 Z"></path>
                      </g>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Cerrar sesión</span>
          </a>
        </li>
      </ul>
    

  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <h6 class="font-weight-bolder mb-0">Administración de tiempos</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
                <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none"><?php echo (isset($_SESSION['user'])?$_SESSION['user']:"")?></span>
              </a>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
            
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row mt-4">
      <!-- Form -->
      <div class="col-12 col-xl-4">
          <div class="card h-100">
            <div class="card-header pb-0 p-3 border-0 d-flex align-items-center">
              <h6 class="mb-0" id="mainFormTitle"><?php echo (isset($formText)?$formText:"Crear tiempo de comida")?></h6>
              <p class="btn btn-link pe-3 ps-0 mb-0 ms-auto" id="clearMainForm">Limpiar</p>
            </div>
            <div class="card-body p-3">
              <form role="form" method="POST" action="#" id="mainForm">
                <?php if(isset($errorSubmission)) : ?>
                  <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageMainForm"><?php echo $errorSubmission;?></p>
                <?php endif; ?>

                <?php if(isset($blockIdInput)) : ?>
                  <div class="mb-3" id="mainField">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Identificador de tiempo</h6>
                      <div>
                        <input type="hidden" name="id" value="<?php echo (isset($id)?$id:"")  ?>">
                        <input type="text" class="form-control" id="formId" aria-label="id" aria-describedby="food-time-addon" value="<?php echo (isset($id)?$id:"")  ?>" <?php echo (isset($blockIdInput)?"disabled":"")  ?>>
                      </div>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Nombre</h6>
                      <div>
                        <input type="text" class="form-control" id="mainFormName" placeholder="Nombre" name="name" aria-label="Nombre" aria-describedby="text-addon" value="<?php echo (isset($name)?$name:"")  ?>">
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
                      if($foodTimes){
                        while($row = $foodTimes->fetch_array(MYSQLI_ASSOC)){
                          echo "
                            <tr>
                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\" value=\"".$row['id']."\" food-time-id />
                                <p class=\"text-xs font-weight-bold mb-0 \">".$row["id"]."</p>
                              </td>
                              
                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\"  value=\"".$row['name']."\" food-time-name />
                                <p class=\"text-xs font-weight-bold mb-0\">".$row["name"]."</p>
                              </td>

                              <td class=\"align-middle text-center text-sm text-wrap\">
                                <input type=\"hidden\"  value=\"".$row['description']."\" food-time-description />
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
                            if(confirm('¿Desea eliminar el tiempo de comida?')){
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
        document.getElementById("mainFormTitle").textContent = "Crear tiempo";
        let mainField = document.getElementById("mainField");
        if(mainField)mainField.remove();
        document.getElementById("mainFormName").value = "";
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