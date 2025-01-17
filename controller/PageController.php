<?php
//Si no tiene nada, le asigna un valor por defecto

require_once __DIR__ . '/../includes/auth.php';
$_GET['opt'] = $_GET['opt'] ?? '';

switch ($_GET['opt']) {
	case '':
	case 'login':
		$auth = checkAuth();
		if ($auth === 'error_db') {
			$error = 'db';
			require_once 'view/error.php';
			break;
		} elseif ($auth === true) {
			header('Location: index.php?opt=home');
			break;
		} else {
			require_once 'view/login.php';
			break;
		}

	case 'error_db':
		$error = 'db';
		require_once 'view/error.php';
		break;

	case 'test':
		require_once 'controller/testController.php';
		require_once 'view/test.php';
		echo "Testeando conexión MariaDB";
		break;

	//Todos estos casos necesita que esté iniciada la sesión
	case 'database':
	case 'granjas':
	case 'galpones':
	case 'mantenimientos':
	case 'vacunas':
	case 'home':
	case 'mantenimientosGalpones':
	case 'usuarios':
	case 'lotesAves':
	case 'cargarMortandad':
	case 'cargarPesaje':
	case 'aplicarVacunas':
	case 'moverGalpon':
	case 'bajaLote':
	case 'compuestos':
	case 'lotesVacunas':
		$auth = checkAuth();
		if ($auth === 'error_db') {
			$error = 'db';
			require_once 'view/error.php';
			break;
		} elseif ($auth === false) {
			header('Location: index.php?opt=login');
			break;
		}
		switch ($_GET['opt']) {
			case 'granjas':
				require_once 'controller/abmGranjasController.php';
				if ( $_SESSION['tipoUsuario'] === 'Propietario' )
				{
					require_once 'view/abmGranjas.php';
				}else{
					$error = '403';
					require_once 'view/error.php';
				}
				break;
			case 'galpones':
				require_once 'controller/abmGalponesController.php';
				if ( $_SESSION['tipoUsuario'] === 'Propietario' )
				{
					require_once 'view/abmGalpones.php';
				}else{
					$error = '403';
					require_once 'view/error.php';
				}
				break;
			case 'database':
				require_once 'view/database.php';
				break;
			case 'mantenimientos':
				require_once 'controller/mantenimientosController.php';
				require_once 'view/abmMantenimientosGranjas.php';
				break;
			case 'mantenimientosGalpones':
				require_once 'controller/mantenimientosController.php';
				require_once 'view/abmMantenimientosGalpones.php';
				break;
			case 'vacunas':
				require_once 'controller/vacunasController.php';
				require_once 'view/abmVacunas.php';
				break;
			case 'lotesVacunas':
				require_once 'controller/vacunasController.php';
				require_once 'view/abmLotesVacunas.php';
				break;
			case 'lotesAves':
				require_once 'controller/abmLotesAvesController.php';
				require_once 'view/abmLotesAves.php';
				break;
			case 'cargarMortandad':
				require_once 'controller/abmLotesAvesController.php';
				require_once 'view/abmAvesMortandad.php';
				break;
			case 'cargarPesaje':
				require_once 'controller/abmLotesAvesController.php';
				require_once 'view/abmAvesPesajes.php';
				break;
			case 'moverGalpon':
				require_once 'controller/abmLotesAvesController.php';
				require_once 'view/abmAvesGalpones.php';
				break;
			case 'bajaLote':
				require_once 'controller/abmLotesAvesController.php';
				require_once 'view/abmAvesBaja.php';
				break;
			case 'aplicarVacunas':
				require_once 'controller/abmLotesAvesController.php';
				require_once 'controller/vacunasController.php';
				require_once 'view/abmAvesVacunas.php';
				break;
			case 'compuestos':
				require_once 'controller/compuestosController.php';
				require_once 'view/abmCompuestos.php';
				break;
			case 'home':
				require_once 'controller/homeController.php';
				break;
			case 'usuarios':
				require_once 'controller/userController.php';
				if ($_SESSION['tipoUsuario'] === 'Propietario' )
				{
					require_once 'view/abmUsuarios.php';
				}else{
					$error = '403';
					require_once 'view/error.php';
				}
				break;
			case 'database':
				require_once 'view/database.php';
				break;
		}
		break;

	default:
		$error = '404';
		require_once 'view/error.php';
		break;
}

//Este es un caso especial que toma el botón del formulario iniciar sesión que no va por Get.
if (!empty($_POST['btLogin'])) {
	require_once 'controller/userController.php';
}
