<?php
session_start();
require_once(__DIR__ . "/util.php");
require_once(__DIR__ . "/database/database.php");
require_once(__DIR__ . "/models/DateMenu.php");
[$db, $connection] = Database::getConnection();
if (areSubmitted(["id"])) {
    if (checkInput(["id"])) {
        $menu = DateMenu::getDateMenu($connection, $_POST["id"], false, true);
        if ($menu) {
            $tname = $menu["tname"];
            $mname = $menu["mname"];
            $start = $menu["start"];
            $end = $menu["end"];
            $description = $menu["description"];
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
    <style>
        .fc-event {
            height: 100px;
            background: #a31e32;
            color: white;
            cursor: pointer;
        }

        body {
            background: #2B3843;

        }

        body * {
            color: white;
        }

        .shadow-blur h6,
        .shadow-blur span,
        .shadow-blur i {
            color: #2B3843 !important;
        }

        .fc .fc-daygrid-day.fc-day-today {
            background: rgba(23, 30, 36, 0.5);
        }

        .fc-daygrid-dot-event:hover {
            background: white;

        }

        .fc-daygrid-dot-event:hover * {
            color: #2B3843;
        }
    </style>
    <script>
        function formDate(inputsValues) {
            let form = document.createElement("form");
            form.method = 'POST';
            form.action = '#';
            document.body.appendChild(form);
            for (let i = 0; i < inputsValues.length; i++) {
                const structure = inputsValues[i];
                let input = document.createElement("input");
                input.type = "hidden";
                input.name = structure.name;
                input.value = structure.value;
                form.appendChild(input);
            }
            form.submit();
        }
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridWeek',
                headerToolbar: {
                    end: ''
                },
                locale: 'es',
                hiddenDays: [0, 6],
                editable: false,
                selectable: true,
                events: {
                    height: "100%",
                    url: '/dashboard/menu-assignment.php',
                    failure: function() {
                        alert("No se logró cargar el calendario");
                    }
                },
                eventClick: function(info) {
                    info = info.event;
                    formDate([{
                        name: "id",
                        value: info.extendedProps.identificator
                    }]);

                }
            });
            calendar.render();
        });
    </script>
</head>

<body>
    <div class="modal fade" id="Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down position-relative">
            <div class="px-3  modal-content d-flex flex-column justify-content-around bg-modal-menu">
                <div class="modal-header bg-modal-menu">
                    <h5 class="modal-title" id="exampleModalToggleLabel2">Información del menú</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column justify-content-evenly" id="modalBody">
                    <div>
                        <h6>Tiempo de comida:</h6>
                        <p><?php echo (isset($tname) ? $tname : "") ?></p>
                    </div>
                    <div>
                        <h6>Tiempo:</h6>
                        <p><?php echo (isset($start) ? (new DateTime($start))->format('H:i A') : "") ?> - <?php echo (isset($end) ? (new DateTime($end))->format('H:i A') : "") ?></p>
                    </div>
                    <div>
                        <h6>Menú:</h6>
                        <p><?php echo (isset($mname) ? $mname : "") ?></p>
                    </div>
                    <div>
                        <h6>Descripción:</h6>
                        <p><?php echo (isset($description) ? $description : "") ?></p>
                    </div>
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
        <?php if (isset($menu)) : ?>
            openModal();
        <?php endif; ?>
    </script>
    <script>
        const targetNode = document.body;
        const config = {
            attributes: true,
            childList: true,
            subtree: true
        };
        const callback = function(mutationList, observer) {
            for (const mutation of mutationList) {
                if (mutation.type === 'childList') {
                    let tableContainer = document.querySelector("div[class='fc-daygrid fc-dayGridWeek-view fc-view'");
                    if (tableContainer) {
                        let tableContParent = tableContainer.parentNode;
                        tableContParent.style.overflowX = "auto";
                        tableContainer.style.minWidth = "500px";
                        observer.disconnect();
                    }
                }
            }
        };
        const observer = new MutationObserver(callback);
        observer.observe(targetNode, config);
    </script>
</body>

</html>