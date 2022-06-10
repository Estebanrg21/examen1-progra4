<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        SCOT
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/styles.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css" rel="stylesheet" />
    <!-- FullCalendar dependencies -->
    <link rel="stylesheet" href="/assets/fullcalendar-5.11.0/lib/main.min.css" />
    <script src="/assets/fullcalendar-5.11.0/lib/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridWeek',
                headerToolbar: {
                    end: ''
                },
                hiddenDays: [0, 6],
                editable: true,
                selectable: true,
                events: {
                    height: "100%",
                    url: '/dashboard/menu-assignment.php',
                    failure: function() {
                        alert("No se logró cargar el calendario");
                    }
                },
                eventClick: function(info) {
                    console.log(info);
                }
            });
            calendar.render();
        });
    </script>
</head>

<body>
    <div class="modal fade" id="Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down position-relative">
            <div class="px-3  modal-content d-flex flex-column justify-content-around bg-cbdark">
                <div class="modal-header bg-cbdark">
                    <h5 class="modal-title" id="exampleModalToggleLabel2">Información del menú</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
            </div>
        </div>
    </div>
    <main class="main-content position-relative vh-100 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Menú de la semana</h6>
                </nav>
                <div class="navbar-nav mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <ul class="navbar-nav  justify-content-end">
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?php echo (isset($_SESSION['user']) ? "/dashboard.php" : "/login.php") ?>" class="nav-link text-body font-weight-bold px-0">
                                <i class="fa <?php echo (isset($_SESSION['user']) ? "fa-user" : "fa-solid fa-lock") ?> me-sm-1"></i>
                                <span class="d-sm-inline"><?php echo (isset($_SESSION['user']) ? $_SESSION['user'] : "Login") ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="d-flex justify-content-center align-items-center ">
            <div class="container-fluid py-4 m-0 row bg-red col-12 col-md-9 ">
                <div id='calendar'></div>
            </div>
        </div>
    </main>
    <!-- Scripts -->
    <?php require_once(__DIR__ . '/templates/scripts.php') ?>
    <!-- End Scripts -->
    <script type="text/javascript">
        function openModal() {
            let modal = new bootstrap.Modal(document.getElementById('Modal'));
            modal.show();
        }
    </script>
</body>

</html>