<?php
require_once(__DIR__ . "/../util.php");
require_once(__DIR__ . "/../models/DateMenu.php");
require_once(__DIR__ . "/../database/database.php");
[$db, $connection] = Database::getConnection();
if (isset($_GET["start"]) && isset($_GET["end"])) {
    if (isDateValid($_GET["start"]) && isDateValid($_GET["end"])) {
        $start = (new DateTime($_GET["start"]))->format('Y-m-d H:i:s');
        $end = (new DateTime($_GET["end"]))->format('Y-m-d H:i:s');
        DateMenu::getAllDateMenusWithRange($connection, $start, $end);
        exit;
    }
}
session_start();
$sessionCondition =  (!$_SESSION['isAdmin'] && !$_SESSION['isSuper']);
$headerLoc = "/dashboard.php";
require_once(__DIR__ . "/../templates/sessionValidation.php");
if (areSubmitted(["date"])) {
    if (isDateValid($_POST["date"])) {
        $_SESSION["date"] =  $_POST["date"];
        if (isset($_POST["id"]))
            header("Location: /dashboard/menu-assignment/edit-date.php?id=" . $_POST['id'] . "&m=u");
        else
            header("Location: /dashboard/menu-assignment/edit-date.php");
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
        SCOT: Asignar menús
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css" rel="stylesheet" />
    <!-- FullCalendar dependencies -->
    <link rel="stylesheet" href="/assets/fullcalendar-5.11.0/lib/main.min.css" />
    <script src="/assets/fullcalendar-5.11.0/lib/main.min.js"></script>
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
                initialView: 'dayGridMonth',
                headerToolbar: {
                    right: 'timeGridWeek dayGridMonth prev,next'
                },
                scrollTime: '07:00:00',
                locale: 'es',
                hiddenDays: [0, 6],
                allDaySlot: false,
                eventMinHeight: 50,
                navLinks: true,
                buttonText: {
                    month: 'Mes',
                    week: 'Semana',
                },
                slotDuration: '01:00',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                },
                dayMaxEventRows: true, // for all non-TimeGrid views
                views: {
                    dayGridMonth: {
                        dayMaxEventRows: 3 // adjust to 6 only for timeGridWeek/timeGridDay
                    }
                },
                expandRows: true,
                selectable: true,
                displayEventEnd: true,
                eventMaxStack: 1,
                dateClick: function(info) {
                    formDate([{
                        name: "date",
                        value: info.dateStr
                    }]);
                },
                events: {
                    url: '/dashboard/menu-assignment.php',
                    failure: function() {
                        alert("No se logró cargar el calendario");
                    }
                },
                eventClick: function(info) {
                    formDate([{
                            name: "date",
                            value: info.event.extendedProps.dbStart
                        },
                        {
                            name: "id",
                            value: info.event.extendedProps.identificator
                        }
                    ]);
                },
            });
            calendar.render();
        });
    </script>
    <style>
        .fc-timegrid-slot,
        .fc-timegrid-slot-label {
            height: 100px !important;
        }
    </style>
    <link href="/assets/css/styles.css" rel="stylesheet" />
</head>

<body class="g-sidenav-show ">
    <!-- Aside -->
    <?php $option = 6;
    require_once(__DIR__ . '../../templates/aside.php') ?>
    <!-- End Aside -->

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <?php $navTitle = "Asignación de menús";

        require_once(__DIR__ . '../../templates/navbar.php') ?>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div id='calendar'></div>
        </div>
        <!-- Footer -->
        <?php require_once(__DIR__ . '../../templates/footer.php') ?>
        <!-- End Footer -->
    </main>
    <!-- Scripts -->
    <?php require_once(__DIR__ . '../../templates/scripts.php') ?>
    <!-- End Scripts -->
</body>