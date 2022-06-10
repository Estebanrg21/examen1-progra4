<?php
session_start();
$sessionCondition =  (!$_SESSION['isAdmin'] && !$_SESSION['isSuper']);
$headerLoc = "/dashboard.php";
require_once(__DIR__ . "/../../templates/sessionValidation.php");
require_once(__DIR__ . "/../../util.php");
require_once(__DIR__ . "/../../database/database.php");
require_once(__DIR__ . "/../../models/Menu.php");
require_once(__DIR__ . "/../../models/DateMenu.php");
[$db, $connection] = Database::getConnection();
if (!isset($_SESSION["date"]) || !isset($_SESSION["date_start"])) {
    header("Location: /dashboard/menu-assignment.php");
}
if (isset($_GET["items"])) {
    try {
        Menu::getAllItemsHtml($connection, $_GET["items"]);
    } catch (\Throwable $th) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error', true, 500);
        echo 'No se pudo cargar el conjunto de datos';
    }
    die;
}
if (areSubmitted(DateMenu::$INSERT_REQUIRED_FIELDS)) {
    if (checkInput(DateMenu::$INSERT_REQUIRED_FIELDS)) {
        $dateMenu = new DateMenu(
            (isset($_POST['idFood']) ? $_POST['idFood'] : null),
            (isset($_POST['idMenu']) ? $_POST['idMenu'] : null),
            (isset($_POST['description']) ? $_POST['description'] : null),
        );
        $dateMenu->connection = $connection;
        $result = $dateMenu->save();
        [$text, $isOk] = DateMenu::$responseCodes[$result];
    } else {
        $errorSubmission = "Los campos no pueden estar vacíos";
    }
}
if (areSubmitted(DateMenu::$UPDATE_REQUIRED_FIELDS)) {
    if (checkInput(["id", "idFood", "idMenu", "description"])) {
        $dateMenu = new DateMenu(
            (isset($_POST['idFood']) ? $_POST['idFood'] : null),
            (isset($_POST['idMenu']) ? $_POST['idMenu'] : null),
            (isset($_POST['description']) ? $_POST['description'] : null),
            (isset($_POST['id']) ? $_POST['id'] : null)
        );
        $dateMenu->connection = $connection;
        $result = $dateMenu->save();
        [$text, $isOk] = DateMenu::$responseCodes[$result];
    } else {
        $errorSubmission = "Los campos no pueden estar vacíos";
    }
} else if (isset($_GET['id']) && isset($_GET['m'])) {
    $dateMenuResult = DateMenu::getDateMenu($connection, $_GET['id'], $_GET['m'] == 'd', $_GET['m'] != 'd');
    if ($dateMenuResult) {
        if ($_GET['m'] != 'd') {
            $id = $dateMenuResult['id'];
            $blockIdInput = true;
            $idFood = $dateMenuResult["idFood"];
            $idMenu = $dateMenuResult["idMenu"];
            $foodTime = $dateMenuResult["tname"];
            $menu = $dateMenuResult["mname"];
            $description =  $dateMenuResult['description'];
            $formText = "Actualizar sección";
            $formButtonText = "Actualizar";
        } else {
            $result = DateMenu::removeMenu($connection, $_GET['id']);
            [$text, $isOk] = DateMenu::$responseCodes[$result];
        }
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
<?php $hdTitle = "SCOT: Asignar menús";
require_once(__DIR__ . '/../../templates/header.php') ?>

<body class="g-sidenav-show  bg-gray-100">
    <!-- Modal -->
    <?php require_once(__DIR__ . '/../../templates/modal.php') ?>
    <!-- End Modal -->
    <!-- Aside -->
    <?php $option = 5;
    require_once(__DIR__ . '/../../templates/aside.php') ?>
    <!-- End Aside -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <?php $navTitle = "Asignación de menús a fecha: " . (new DateTime($_SESSION['date']))->format('d/m/Y') . " siendo las " . (new DateTime($_SESSION['date']))->format('H:i A');
        if (isset($blockIdInput)) $navTitle = "Asignación de menús: ".(new DateTime($_SESSION['date']))->format('d/m/Y') ;
        $linksNav = [["/dashboard/menu-assignment.php", "Asignar menús!", "fa-calendar-check"]];
        require_once(__DIR__ . '/../../templates/navbar.php') ?>
        <!-- End Navbar -->
        <div class="container-fluid py-4 row">
            <!-- Form -->
            <div class="col-12 col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3 border-0 d-flex align-items-center">
                        <h6 class="mb-0" id="mainFormTitle"><?php echo (isset($formText) ? $formText : "Asignar comida") ?></h6>
                        <p class="btn btn-link pe-3 ps-0 mb-0 ms-auto" id="clearMainForm">Limpiar</p>
                    </div>
                    <div class="card-body p-3">
                        <form role="form" method="POST" action="#" id="mainForm">
                            <?php if (isset($errorSubmission)) : ?>
                                <p class="text-danger text-xs font-weight-bolder mb-3" id="errorMessageMainForm"><?php echo $errorSubmission; ?></p>
                            <?php endif; ?>

                            <?php if (isset($blockIdInput)) : ?>
                                <div class="mb-3" id="mainField">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Identificador de asignación</h6>
                                    <div>
                                        <input type="hidden" name="id" value="<?php echo (isset($id) ? $id : "")  ?>">
                                        <input type="text" class="form-control me-4" id="formId" aria-label="id" aria-describedby="food-time-addon" value="<?php echo (isset($id) ? $id : "")  ?>" <?php echo (isset($blockIdInput) ? "disabled" : "")  ?>>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Tiempo de comida</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <input type="hidden" name="idFood" value="<?php echo (isset($idFood) ? $idFood : "")  ?>" />
                                    <input disabled type="text" class="form-control me-4" style="padding-left:20px !important;" aria-label="Tiempo" aria-describedby="text-addon" value="<?php echo (isset($foodTime) ? $foodTime : "")  ?>">
                                </div>
                                <button type="button" class="btn bg-gradient-dark m-0" item-type="tiempo" sel-items items="Tiempos de comida">Seleccionar</button>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Comida</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <input type="hidden" name="idMenu" value="<?php echo (isset($idMenu) ? $idMenu : "")  ?>" />
                                    <input disabled type="text" class="form-control me-4" style="padding-left:20px !important;" aria-label="Comida" aria-describedby="text-addon" value="<?php echo (isset($menu) ? $menu : "")  ?>">
                                </div>
                                <button type="button" class="btn bg-gradient-dark m-0" item-type="comida" sel-items items="Comidas">Seleccionar</button>
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
            <!-- Show Items -->
            <div class="col-12 col-xl-4 overflow-auto" id="tableDepView" style="display:none;max-height: 500px;">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6 id="itemsDepTitle"></h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2" id="tableDepContainer"> </div>
                </div>
            </div>
            <!-- End Show Items -->
        </div>
        <!-- Show Show Date Menus -->
        <div class="row mt-4 mw-100">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Menús</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Identificador</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Tiempo de comida</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Menu</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Hora</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 ">Descripción</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $items = DateMenu::getAllDateMenus($connection, (new DateTime($_SESSION["date_start"]))->format('Y-m-d'));
                                    if ($items) {
                                        while ($row = $items->fetch_array(MYSQLI_ASSOC)) {
                                            echo "
                                            <tr>
                                                <td class=\"align-middle text-center text-sm\">
                                                    <p class=\"text-xs font-weight-bold mb-0 \">" . $row["id"] . "</p>
                                                </td>
                              
                                                <td class=\"align-middle text-center text-sm\">
                                                    <p class=\"text-xs font-weight-bold mb-0\">" . $row["tname"] . "</p>
                                                </td>

                                                <td class=\"align-middle text-center text-sm\">
                                                    <p class=\"text-xs font-weight-bold mb-0\">" . $row["mname"] . "</p>
                                                </td>

                                                <td class=\"align-middle text-center text-sm\">
                                                    <p class=\"text-xs font-weight-bold mb-0\">" .(new DateTime($row["start"]))->format('H:i') . "</p>
                                                </td>

                                                <td class=\"align-middle text-center text-sm text-wrap\">
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
                                                    if (confirm('¿Desea eliminar la asignación?')) {
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
        <!-- End Show Date Menus -->
    </main>
    <!-- Scripts -->
    <?php require_once(__DIR__ . '/../../templates/scripts.php') ?>
    <!-- End Scripts -->
    <script>
        let rowSelected = undefined;
        let selects = document.querySelectorAll("button[sel-items]");
        if (selects.length > 0) {
            selects.forEach((el) => {
                el.addEventListener("click", (e) => {
                    const element = e.target;
                    fetch('?items=' + element.getAttribute("item-type"))
                        .then(result => {
                            if (result.status != 500)
                                return result.text();
                            throw new Error('No se logró cargar los datos');
                        })
                        .then((response) => {
                            if (response) {
                                document.getElementById("tableDepView").style.display = "block";
                                let container = document.getElementById("tableDepContainer");
                                container.innerHTML = response;
                                container.scrollIntoView({
                                    behavior: 'smooth'
                                });
                                let rows = document.querySelectorAll("tr[item-row]");
                                if (rows) {
                                    css = 'tr[item-row]:hover{ opacity:0.8; }';
                                    let = style = document.createElement('style');
                                    if (style.styleSheet) {
                                        style.styleSheet.cssText = css;
                                    } else {
                                        style.appendChild(document.createTextNode(css));
                                    }
                                    document.getElementsByTagName('head')[0].appendChild(style);
                                    rows.forEach((r) => {
                                        r.style.cursor = "pointer";
                                        r.addEventListener("click", (e) => {
                                            let sourceElement = e.target;

                                            function getTr() {
                                                if (sourceElement.tagName !== "TR") {
                                                    sourceElement = sourceElement.parentNode;
                                                    getTr();
                                                }
                                            }
                                            getTr();
                                            if (rowSelected) {
                                                rowSelected.style.background = "transparent";
                                                rowSelected.style.color = "#67748e";
                                            }
                                            rowSelected = sourceElement;
                                            sourceElement.style.background = "#2B3843";
                                            sourceElement.style.color = "#fff";
                                            element.parentNode.querySelector("input[type='hidden']").value = sourceElement.querySelector("input").value;
                                            element.parentNode.querySelector("input[type='text']").value = sourceElement.querySelector("p").textContent;
                                        });
                                    });
                                    document.getElementById("itemsDepTitle").textContent = element.getAttribute("items");
                                }
                            }
                        }).catch((error) => {
                            alert(error);
                        });
                });
            });
        }
    </script>
</body>