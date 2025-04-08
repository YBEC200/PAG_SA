<?php
// Conexión a la base de datos
include_once '../../../config/database.php';

// Consulta para obtener el único usuario con rol 'admin'
$sqljeje = "SELECT dni, nombre, correo, telefono FROM usuario WHERE rol = 'admin' LIMIT 1";
$result = $conn->query($sqljeje);

// Verificar si se encontró un usuario
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc(); // Obtener los datos del usuario
} else {
    $admin = null; // No se encontró un usuario con rol 'admin'
}

// Manejo del formulario para actualizar los datos del usuario con rol 'admin'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formType']) && $_POST['formType'] === 'updateAdmin') {
    // Obtener los datos enviados desde el formulario
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    // Validar que los campos no estén vacíos
    if (!empty($nombre) && !empty($correo) && !empty($dni) && !empty($telefono)) {
        // Actualizar los datos del usuario con rol 'admin'
        $stmt = $conn->prepare("UPDATE usuario SET nombre = ?, correo = ?, dni = ?, telefono = ? WHERE rol = 'admin' LIMIT 1");
        $stmt->bind_param("ssss", $nombre, $correo, $dni, $telefono);

        if ($stmt->execute()) {
            // Redirigir después de actualizar
            header("Location: Perfil-Usuario.php");
            exit;
        } else {
            echo "<script>alert('Ocurrió un error al actualizar los datos.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Por favor, complete todos los campos.');</script>";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="assets/images/logotipo.png" type="image/png"/>
	<!--plugins-->
	<link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet"/>
	<!-- loader-->
	<link href="assets/css/pace.min.css" rel="stylesheet"/>
	<script src="assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/bootstrap-extended.css" rel="stylesheet">
  	<link href="https://fonts.googleapis.com/css2?family=Concert+One&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
	<link href="assets/css/icons.css" rel="stylesheet">
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.18/jspdf.plugin.autotable.min.js"></script>
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="assets/css/dark-theme.css"/>
	<link rel="stylesheet" href="assets/css/semi-dark.css"/>
	<link rel="stylesheet" href="assets/css/header-colors.css"/>

	<title>Administrador - Botica San Antonio</title>
</head>

<body>
	<div class="wrapper">
		<?php include_once '../../../config/sidebar.php'; ?>
	</div>
		<header>
			<?php include_once '../../../config/nav.php'; ?>
		</header>
		<!--end header -->
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Perfil de Usuario</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($admin['correo'] ?? ''); ?></li>
							</ol>
						</nav>
					</div>
				</div>
				<!--end breadcrumb-->
				<div class="container">
					<div class="main-body">
						<div class="row">
							<div class="col-lg-4">
								<div class="card">
									<div class="card-body">
										<div class="d-flex flex-column align-items-center text-center">
											<img src="assets/images/avatars/avatar-1.png" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
											<div class="mt-3">
												<h4><?php echo htmlspecialchars($admin['nombre'] ?? ''); ?></h4>
												<p class="text-secondary mb-1">Gerente - Propietario</p>
												<p class="text-muted font-size-sm">RUC:  10435605856</p>
											</div>
										</div>
										<hr class="my-4" />
										<ul class="list-group list-group-flush">
											<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
												<h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-globe me-2 icon-inline"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>Pag.Web</h6>
												<span class="text-secondary">www.boticasanantonio.shop</span>
											</li>
											<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
												<h6 class="mb-0"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
													width="22.5" height="22.5" viewBox="0 0 512 512"
													preserveAspectRatio="xMidYMid meet" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook me-2 icon-inline text-primary">
												   <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
												   fill="#000000" stroke="none">
												   <path d="M236 4355 c-33 -13 -68 -32 -78 -42 -17 -18 23 -57 1188 -1143 663
												   -619 1211 -1125 1218 -1125 13 0 2376 2273 2376 2286 0 4 -25 17 -55 28 l-56
												   21 -2267 0 -2267 0 -59 -25z"/>
												   <path d="M4340 3283 c-426 -411 -775 -750 -775 -754 0 -10 1545 -1452 1551
												   -1447 2 3 3 667 2 1476 l-3 1472 -775 -747z"/>
												   <path d="M1 2509 c1 -800 4 -1465 8 -1477 5 -21 84 51 776 717 424 408 771
												   745 773 750 2 5 -318 307 -710 672 -392 365 -743 692 -780 728 l-68 64 1
												   -1454z"/>
												   <path d="M2989 1979 c-178 -171 -338 -318 -356 -325 -41 -18 -95 -18 -136 0
												   -18 8 -177 150 -355 316 l-323 301 -33 -28 c-19 -15 -372 -354 -785 -753 -516
												   -498 -747 -728 -740 -735 7 -7 739 -10 2302 -10 2271 0 2292 0 2334 20 l43 20
												   -804 750 c-442 412 -808 751 -814 753 -5 1 -155 -137 -333 -309z"/>
												   </g>
												   </svg>Correo Laboral</h6>
												<span class="text-secondary"><?php echo htmlspecialchars($admin['correo'] ?? ''); ?></span>
											</li>
											<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
												<h6 class="mb-0"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
													width="19" height="19" viewBox="0 0 512.000000 512.000000"
													preserveAspectRatio="xMidYMid meet" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook me-2 icon-inline text-primary">
												   <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
												   fill="#000000" stroke="none">
												   <path d="M2600 4923 l0 -159 168 -12 c292 -21 538 -86 787 -207 428 -208 761
												   -542 977 -980 131 -266 195 -519 214 -838 l7 -117 156 2 156 3 -2 110 c-16
												   813 -463 1571 -1183 2005 -338 204 -732 321 -1162 345 l-118 6 0 -158z"/>
												   <path d="M952 5060 c-65 -14 -141 -60 -188 -115 -24 -27 -162 -196 -308 -375
												   -287 -351 -326 -411 -367 -565 -30 -113 -38 -362 -15 -506 41 -261 128 -515
												   276 -811 424 -843 1256 -1719 2111 -2221 129 -76 389 -202 524 -256 464 -182
												   887 -206 1166 -65 47 23 191 131 427 321 196 157 372 300 390 317 18 17 47 60
												   65 95 27 56 31 74 31 146 0 72 -4 90 -31 146 -18 35 -45 76 -60 90 -15 14
												   -255 208 -533 431 -306 244 -523 411 -550 422 -25 11 -79 21 -120 23 -61 4
												   -86 1 -135 -18 -52 -19 -80 -41 -206 -165 -134 -131 -150 -144 -204 -160 -176
												   -51 -477 54 -766 267 -108 80 -329 300 -409 409 -228 309 -335 652 -250 797
												   12 21 80 93 150 161 163 156 185 195 185 327 0 77 -4 98 -26 145 -16 33 -195
												   264 -445 575 -416 516 -419 520 -484 552 -72 36 -155 48 -228 33z m481 -795
												   c213 -266 387 -493 387 -503 0 -10 -57 -75 -133 -148 -105 -104 -139 -144
												   -164 -195 -66 -134 -85 -273 -58 -430 52 -315 233 -640 524 -941 496 -515
												   1115 -731 1457 -510 32 21 113 93 179 161 65 67 125 119 132 117 15 -6 975
												   -776 985 -790 4 -6 8 -14 8 -17 0 -9 -676 -549 -720 -576 -121 -73 -338 -88
												   -571 -40 -985 205 -2281 1331 -2839 2469 -261 532 -317 980 -152 1213 41 57
												   508 641 534 667 10 10 23 15 30 12 7 -3 187 -223 401 -489z"/>
												   <path d="M2583 4148 c4 -24 9 -95 13 -159 l6 -116 102 -6 c292 -18 556 -134
												   769 -336 229 -217 367 -513 384 -818 l6 -113 160 0 160 0 -6 110 c-35 610
												   -423 1151 -984 1370 -170 67 -385 110 -546 110 l-69 0 5 -42z"/>
												   </g>
												   </svg>
												   Nro.Contacto</h6>
												<span class="text-secondary"><?php echo htmlspecialchars($admin['telefono'] ?? ''); ?></span>
											</li>
											<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
												<h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook me-2 icon-inline text-primary"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>Facebook</h6>
												<span class="text-secondary">www.facebook.com/BOTICA-SANT</span>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-lg-8">
								<div class="card">
									<div class="card-body">
										<form action="" method="POST">
											<input type="hidden" name="formType" value="updateAdmin">
											<div class="row mb-3">
												<div class="col-sm-3">
													<h6 class="mb-0">Nombre</h6>
												</div>
												<div class="col-sm-9 text-secondary">
													<input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($admin['nombre'] ?? ''); ?>" required />
												</div>
											</div>
											<div class="row mb-3">
												<div class="col-sm-3">
													<h6 class="mb-0">Correo Laboral</h6>
												</div>
												<div class="col-sm-9 text-secondary">
													<input type="email" class="form-control" name="correo" value="<?php echo htmlspecialchars($admin['correo'] ?? ''); ?>" required />
												</div>
											</div>
											<div class="row mb-3">
												<div class="col-sm-3">
													<h6 class="mb-0">DNI</h6>
												</div>
												<div class="col-sm-9 text-secondary">
													<input type="text" class="form-control" name="dni" value="<?php echo htmlspecialchars($admin['dni'] ?? ''); ?>" required />
												</div>
											</div>
											<div class="row mb-3">
												<div class="col-sm-3">
													<h6 class="mb-0">Teléfono</h6>
												</div>
												<div class="col-sm-9 text-secondary">
													<input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($admin['telefono'] ?? ''); ?>" required />
												</div>
											</div>
											<div class="row">
												<div class="col-sm-3"></div>
												<div class="col-sm-9 text-secondary">
													<button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end page wrapper -->
		<!--start overlay-->
		<div class="overlay toggle-icon"></div>
		<!--end overlay-->
		<!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->
		<footer class="page-footer">
			<p class="mb-0">Copyright © 2023. All right reserved.</p>
		</footer>
	</div>
	<!--end wrapper-->



	<!--start switcher-->
	<div class="switcher-wrapper">
		<div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
		</div>
		<div class="switcher-body">
			<div class="d-flex align-items-center">
				<h5 class="mb-0 text-uppercase">Theme Customizer</h5>
				<button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
			</div>
			<hr/>
			<h6 class="mb-0">Theme Styles</h6>
			<hr/>
			<div class="d-flex align-items-center justify-content-between">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
					<label class="form-check-label" for="lightmode">Light</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
					<label class="form-check-label" for="darkmode">Dark</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
					<label class="form-check-label" for="semidark">Semi Dark</label>
				</div>
			</div>
			<hr/>
			<div class="form-check">
				<input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
				<label class="form-check-label" for="minimaltheme">Minimal Theme</label>
			</div>
			<hr/>
			<h6 class="mb-0">Header Colors</h6>
			<hr/>
			<div class="header-colors-indigators">
				<div class="row row-cols-auto g-3">
					<div class="col">
						<div class="indigator headercolor1" id="headercolor1"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor2" id="headercolor2"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor3" id="headercolor3"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor4" id="headercolor4"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor5" id="headercolor5"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor6" id="headercolor6"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor7" id="headercolor7"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor8" id="headercolor8"></div>
					</div>
				</div>
			</div>
			<hr/>
			<h6 class="mb-0">Sidebar Colors</h6>
			<hr/>
			<div class="header-colors-indigators">
				<div class="row row-cols-auto g-3">
					<div class="col">
						<div class="indigator sidebarcolor1" id="sidebarcolor1"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor2" id="sidebarcolor2"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor3" id="sidebarcolor3"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor4" id="sidebarcolor4"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor5" id="sidebarcolor5"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor6" id="sidebarcolor6"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor7" id="sidebarcolor7"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor8" id="sidebarcolor8"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--end switcher-->
	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<!--app JS-->
	<script src="assets/js/app.js"></script>
</body>

</html>