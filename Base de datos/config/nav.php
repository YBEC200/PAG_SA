<?php
// Consulta para obtener el único usuario con rol 'admin'
$sqljeje = "SELECT dni, nombre, correo, telefono FROM usuario WHERE rol = 'admin' LIMIT 1";
$result = $conn->query($sqljeje);

// Verificar si se encontró un usuario
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc(); // Obtener los datos del usuario
} else {
    $admin = null; // No se encontró un usuario con rol 'admin'
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
</head>
<body>
<header>
			<div class="topbar d-flex align-items-center">
				<nav class="navbar navbar-expand gap-3">
					<div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
					</div>
					  <div class="top-menu ms-auto">
						<ul class="navbar-nav align-items-center gap-1">
							<div class="app-container p-2 my-2"> </div>
							<li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
								<a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
								</a>
							</li>
							<li class="nav-item dark-mode d-none d-sm-flex">
								<a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
								</a>
							</li>
							<li class="nav-item dropdown dropdown-app">
								<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown" href="javascript:;"><i class='bx bx-grid-alt'></i></a>
								<div class="dropdown-menu dropdown-menu-end p-0">
									<div class="app-container p-2 my-2">
									<div class="row gx-0 gy-2 row-cols-3 justify-content-center p-2">
											<div class="col">
												<a href="https://slack.com" target="_blank">
													<div class="app-box text-center">
														<div class="app-icon">
															<img src="assets/images/app/slack.png" width="30" alt="">
														</div>
														<div class="app-name">
															<p class="mb-0 mt-1">Slack</p>
														</div>
													</div>
												</a>
											</div>
											<div class="col">
												<a href="https://www.behance.net" target="_blank">
													<div class="app-box text-center">
														<div class="app-icon">
															<img src="assets/images/app/behance.png" width="30" alt="">
														</div>
														<div class="app-name">
															<p class="mb-0 mt-1">Behance</p>
														</div>
													</div>
												</a>
											</div>
											<div class="col">
												<a href="https://www.google.com/drive/" target="_blank">
													<div class="app-box text-center">
														<div class="app-icon">
															<img src="assets/images/app/google-drive.png" width="30" alt="">
														</div>
														<div class="app-name">
															<p class="mb-0 mt-1">Google Drive</p>
														</div>
													</div>
												</a>
											</div>
											<div class="col">
												<a href="https://outlook.live.com" target="_blank">
													<div class="app-box text-center">
														<div class="app-icon">
															<img src="assets/images/app/outlook.png" width="30" alt="">
														</div>
														<div class="app-name">
															<p class="mb-0 mt-1">Outlook</p>
														</div>
													</div>
												</a>
											</div>
											<div class="col">
												<a href="https://github.com" target="_blank">
													<div class="app-box text-center">
														<div class="app-icon">
															<img src="assets/images/app/github.png" width="30" alt="">
														</div>
														<div class="app-name">
															<p class="mb-0 mt-1">GitHub</p>
														</div>
													</div>
												</a>
											</div>
											<div class="col">
												<a href="https://stackoverflow.com" target="_blank">
													<div class="app-box text-center">
														<div class="app-icon">
															<img src="assets/images/app/stack-overflow.png" width="30" alt="">
														</div>
														<div class="app-name">
															<p class="mb-0 mt-1">Stack Overflow</p>
														</div>
													</div>
												</a>
											</div>
											<!-- Agrega más enlaces según sea necesario -->
										</div>
									</div>
								</div>
							</li>
							<li class="nav-item dropdown dropdown-large">
								<a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" data-bs-toggle="dropdown"><span class="alert-count">7</span>
									<i class='bx bx-bell'></i>
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									<a href="javascript:;">
										<div class="msg-header">
											<p class="msg-header-title">Notificaciones</p>
											<p class="msg-header-badge">7 Nuevos</p>
										</div>
									</a>
									<div class="header-notifications-list">
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-1.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Daisy Anderson<span class="msg-time float-end">5 sec
												ago</span></h6>
													<p class="msg-info">The standard chunk of lorem</p>
												</div>
											</div>
										</a>
										
									</div>
									<a href="Notificaciones-Alertas.html">
										<div class="text-center msg-footer">
											<button class="btn btn-primary w-100">View All Notifications</button>
										</div>
									</a>
								</div>
							</li>
							<li class="nav-item dropdown dropdown-large">
								<div class="dropdown-menu dropdown-menu-end">
									<div class="header-message-list">	
									</div>
								</div>
							</li>
						</ul>
					</div>


					<div class="user-box dropdown px-3">
						<a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<img src="assets/images/avatars/avatar-1.png" class="user-img" alt="user avatar">
							<div class="user-info">
								<p class="user-name mb-0"><?php echo htmlspecialchars($admin['nombre'] ?? ''); ?></p>
								<p class="designattion mb-0">Propietario</p>
							</div>
						</a>
						<ul class="dropdown-menu dropdown-menu-end">
							<li>
								<a class="dropdown-item d-flex align-items-center" href="Perfil-Usuario.php"><i class="bx bx-user fs-5"></i><span>Perfil</span></a>
							</li>
							<li>
								<div class="dropdown-divider mb-0" ></div>
							</li>
							<li><a class="dropdown-item d-flex align-items-center" href="../../../public/index.html"><i class="bx bx-log-out-circle"></i><span>Cerrar Sesión</span></a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
		</header>
</body>
</html>