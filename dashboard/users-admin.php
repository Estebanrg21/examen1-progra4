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
$sessionCondition = (!$_SESSION['isSuper']);
$headerLoc = "/dashboard.php";
require_once(__DIR__."/../templates/sessionValidation.php");
require_once(__DIR__."/../models/User.php");
require_once(__DIR__."/../database/database.php");
require_once(__DIR__."/../util.php");
[$db,$connection] = Database::getConnection();
$classModal = "";
if(areSubmitted(User::$INSERT_REQUIRED_FIELDS ) || areSubmitted(User::$UPDATE_REQUIRED_FIELDS )){
  if (checkInput(User::$INSERT_REQUIRED_FIELDS) || checkInput(User::$UPDATE_REQUIRED_FIELDS)) {
    $user = new User(
        $_POST['email'],
        (isset($_POST['password'])?$_POST['password']:null),
        (isset($_POST['name'])?$_POST['name']:null),
        (isset($_POST['isAdmin'])?$_POST['isAdmin']:null),
      );
    $user->connection = $connection;
    $result = $user->save($connection);
    if($result == 500 || $result == 400){
      if($result==500)
        $errorMessage = "Hubo un error en el servidor";
      if($result==400)
        $errorMessage = "Campos en formato erróneo";
      $popErrorModal = true;
      $classModal = "danger";
    }
    if($result == 200 || $result == 201 || $result == 204 ){
      if($result==200)
        $successMessage = "Usuario actualizado correctamente!";
      if($result==201)
        $successMessage = "Usuario creado correctamente!";
      if($result==205)
        $successMessage = "El usuario no necesita actualizarse";
      $popSuccessModal =true;  
      $classModal = "success";
    }   
  }else{
    $errorSubmission = "Los campos no pueden estar vacíos";
  }
  
}

if (isset($_GET['id']) && isset($_GET['m'])) {
  $user = User::getUser($connection,$_GET['id'],$_GET['m']=='d');
  if($user){
    if($_GET['m']!='d'){
      $email = $user['email'];
      $name =  $user['name'];
      $isAdmin = $user['is_admin'];
      $formText = "Actualizar usuario";
      $formButtonText = "Actualizar";
    }else{
      $result = User::removeUser($connection,$_GET['id']);
      if($result = 204){
        $successMessage = "Usuario Eliminado correctamente";
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

<?php  $hdTitle="SCOT: Usuarios"; require_once(__DIR__ . '../../templates/header.php') ?>

<body class="g-sidenav-show  bg-gray-100">
  <!-- Modal -->
  <?php require_once(__DIR__ . '../../templates/modal.php') ?>
  <!-- End Modal -->
  <!-- Aside -->
    <?php  $option=1; require_once(__DIR__ . '../../templates/aside.php') ?>
  <!-- End Aside -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php $navTitle = "Administración de usuarios"; require_once(__DIR__ . '../../templates/navbar.php') ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row mt-4">
      <!-- Form -->
      <div class="col-12 col-xl-4">
          <div class="card h-100">
            <div class="card-header pb-0 p-3 border-0 d-flex align-items-center">
              <h6 class="mb-0" id="formUserTitle"><?php echo (isset($formText)?$formText:"Crear usuario")?></h6>
              <p class="btn btn-link pe-3 ps-0 mb-0 ms-auto" id="clearUserForm">Limpiar</p>
            </div>
            <div class="card-body p-3">
              <form role="form" method="POST" action="#" id="formUser">
              <?php if(isset($errorSubmission)) : ?>
                <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageUserForm"><?php echo $errorSubmission;?></p>
              <?php endif; ?>
                <div class="mb-3">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Email</h6>
                      <div>
                        <input type="email" class="form-control" id="formUserEmail" placeholder="Email" name="email" aria-label="Email" aria-describedby="email-addon" value="<?php echo (isset($email)?$email:"")  ?>">
                      </div>
                </div>
                <div class="mb-3">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Nombre</h6>
                      <div>
                        <input type="text" class="form-control" id="formUserName" placeholder="Nombre" name="name" aria-label="Nombre" aria-describedby="text-addon" value="<?php echo (isset($name)?$name:"")  ?>">
                      </div>
                </div>
                <div class="mb-3">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Contraseña</h6>
                    <div>
                      <input type="password" class="form-control" name="password" id="formUserPassword" placeholder="Password" aria-label="Password" aria-describedby="password-addon">
                    </div>
                </div>
                <?php
                  if(isset($isAdmin)){
                    echo "
                    <div class=\"border-0 px-0\" id=\"formUserCheckBox\">
                      <div class=\"form-check form-switch ps-0\">
                        <input type=\"hidden\" name=\"isAdmin\">
                        <input class=\"form-check-input ms-auto\" type=\"checkbox\"  id=\"isAdmin\"".(($isAdmin)?"checked":"").">
                        <label class=\"form-check-label text-body ms-3 text-truncate w-80 mb-0\" for=\"flexSwitchCheckDefault\">Usuario Administrativo</label>
                      </div>
                    </div>

                    <script>
                    let checkbox =document.getElementById(\"isAdmin\");
                    if (checkbox) {
                      let checkboxHidden = document.getElementsByName(\"isAdmin\")[0];
                      checkboxHidden.value=(checkbox.checked)?1:0;
                      checkbox.addEventListener(\"change\",(e)=>{
                          checkboxHidden.value=(e.target.checked)?1:0;
                      })  
                    }
                    </script>

                    ";
                  }
                
                ?>
                

                <div class="text-center">
                  <button type="submit" id="formUserButton" class="btn bg-gradient-info w-100 mt-4 mb-0"><?php echo (isset($formButtonText)?$formButtonText:"Crear")?></button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <!-- End Form -->
      <!-- Show users -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Usuarios</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Email</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Nombre del usuario</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Es super usuario</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Es administrador</th>                      
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $users = User::getAllUsers($connection);
                      if($users){
                        while($row = $users->fetch_array(MYSQLI_ASSOC)){
                          echo "
                            <tr>
                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\" value=\"".$row['email']."\" user-email />
                                <p class=\"text-xs font-weight-bold mb-0 \">".$row["email"]."</p>
                              </td>
                              
                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\"  value=\"".$row['name']."\" user-name />
                                <p class=\"text-xs font-weight-bold mb-0\">".$row["name"]."</p>
                              </td>
                              
                              <td class=\"align-middle text-center text-sm\">
                                <p class=\"m-0 badge badge-sm bg-gradient-".(($row["is_su"])?"success":"danger")."\">".(($row["is_su"])?"Si":"No")."</p>
                              </td>

                              <td class=\"align-middle text-center text-sm\">
                                <input type=\"hidden\" value=\"".$row['is_admin']."\" is-admin>
                                <p class=\"m-0 badge badge-sm bg-gradient-".(($row["is_admin"])?"success":"danger")."\">".(($row["is_admin"])?"Si":"No")."</p>
                              </td>
                            </td>";
                            if(!$row["is_su"]){
                              echo "<td><div class=\"d-flex justify-content-center align-items-center\">";
                              echo "
                                <form action=\"#\" method=\"get\" class=\"m-0 p-0\">
                                  <input type=\"hidden\" value=\"".$row['email']."\" name=\"id\" />
                                  <input type=\"hidden\" value=\"u\" name=\"m\" />
                                  <button type=\"submit\" class=\"btn btn-link text-dark px-3 mb-0 edit-user\" >
                                    <i class=\"fas fa-pencil-alt text-dark me-2\" aria-hidden=\"true\"></i>Actualizar
                                  </button>
                                </form>
                                ";  
                              echo "
                                <form action=\"#\" method=\"get\" class=\"m-0 p-0\">
                                  <input type=\"hidden\" value=\"".$row['email']."\" name=\"id\" />
                                  <input type=\"hidden\" value=\"d\" name=\"m\" />
                                  <button class=\"btn btn-link text-danger px-3 mb-0 \" delete-user>
                                    <i class=\"far fa-trash-alt me-2\" aria-hidden=\"true\"></i>Eliminar
                                  </button>
                                </form>
                                ";
                              echo "</div></td>";
                            }
                        }
                      }
                    ?>
                    <script>
                      let deleteUserButtons = Array.prototype.slice.call(document.querySelectorAll('button[delete-user]'));
                      if(deleteUserButtons){
                        deleteUserButtons.forEach((element)=>{
                          element.addEventListener('click',(e)=>{
                            e.preventDefault();
                            if(confirm('¿Desea eliminar el usuario?')){
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
      <!-- End Show users -->
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
  <?php  require_once(__DIR__ . '../../templates/scripts.php') ?>
  <!-- End Scripts -->
  <script>
    document.getElementById("clearUserForm").addEventListener("click",(e)=>{
        window.history.replaceState({}, document.title, `${window.location.pathname}`);
        document.getElementById("formUserTitle").textContent = "Crear usuario";
        document.getElementById("formUserEmail").value = "";
        document.getElementById("formUserName").value = "";
        document.getElementById("formUserPassword").value = "";
        document.getElementById("formUserButton").textContent = "Crear";
        let formMsg = document.getElementById("errorMessageUserForm");
        if(formMsg)formMsg.remove();
        let checkbox = document.getElementById("formUserCheckBox");
        if(checkbox)checkbox.remove();
    });
  </script>
</body>

</html>