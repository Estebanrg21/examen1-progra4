<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <h6 class="font-weight-bolder mb-0"><?php echo (isset($navTitle) ? $navTitle : "") ?></h6>
    </nav>
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
      <div class="ms-md-auto pe-md-3 d-flex align-items-center">

      </div>
      <ul class="navbar-nav  justify-content-end">
        <?php if (isset($linksNav)) : ?>
          <?php 
            foreach ($linksNav as $linkNav) {
              echo "
              <li class=\"nav-item d-flex align-items-center me-2\">
                <a href=\"$linkNav[0]\" class=\"nav-link text-body font-weight-bold px-0 \">
                  <i class=\"fa me-sm-1 ".$linkNav[2]."\"></i>
                  <span class=\"d-sm-inline d-none text-decoration-underline\">$linkNav[1]</span>
                </a>
            </li>
              ";
            }  
          ?>
        <?php endif; ?>
        <li class="nav-item d-flex align-items-center">
          <a href="<?php echo (isset($_SESSION['user']) ?"/dashboard.php" : "/login.php") ?>" class="nav-link text-body font-weight-bold px-0">
            <i class="fa <?php echo (isset($_SESSION['user']) ?"fa-user" : "fa-solid fa-lock") ?> me-sm-1"></i>
            <span class="d-sm-inline d-none"><?php echo (isset($_SESSION['user']) ? $_SESSION['user'] : "Login") ?></span>
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