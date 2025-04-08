<?php
include_once '../../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $userId = intval($_POST['delete_user_id']);

    // Llamar al procedimiento almacenado
    $sql = "CALL eliminar_usuario(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        // Redirigir a la misma página después de eliminar
        header("Location: Gestion-clientes.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar el usuario: " . $stmt->error . "</div>";
    }

    $stmt->close();
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


		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">

                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Comercio Electrónico</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Gestión de clientes</li>
							</ol>
						</nav>
					</div>
				</div>
                
                

				<div class="card radius-10">
                    <div class="card-header">
                      <div class="d-flex align-items-center">
                        <div>
                          <h6 class="mb-0">Gestión de Clientes</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-horizontal-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:exportTableToExcel('tableData', 'Pedidos')">Exportar a Excel</a></li>
                                <li><a class="dropdown-item" href="javascript:exportStyledTableToPDF()">Exportar a PDF</a></li>
                            </ul>
                        </div>
                        
                      </div>
                    </div>

				<div class="card-body">
						<div class="table-responsive">
							<div class="d-flex justify-content-between mb-3">
								<div class="position-relative">
									<input type="search" class="form-control ps-5 radius-30" placeholder="Buscar cliente">
									<span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
								</div>
								<select id="filterStatus" class="form-select w-25">
									<option value="">Rol</option>
									<option value="admin">Admin</option>
									<option value="cliente">Cliente</option>
								</select>
							</div>
							<!-- Modal de Confirmación -->
							<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header bg-danger text-white">
											<h5 class="modal-title" id="deleteConfirmationLabel">Confirmar Eliminación</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<div class="modal-body">
											<p>¿Estás seguro de que deseas eliminar este usuario?</p>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
											<form id="deleteForm" method="POST" action="Gestion-clientes.php">
												<input type="hidden" name="delete_user_id" id="deleteUserId">
												<button type="submit" class="btn btn-danger">Eliminar</button>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- Modal para Detalles del Pedido -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Pedidos del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>Lista de Pedidos</h5>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Total</th>
                            <th>Fecha del Pedido</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php
						if (isset($_GET['user_id'])) {
							$userId = intval($_GET['user_id']);

							// Consulta para obtener los pedidos del usuario
							$sql = "SELECT id, total, fecha_pedido, estado FROM pedido WHERE id_usuario = ?";
							$stmt = $conn->prepare($sql);
							$stmt->bind_param("i", $userId);
							$stmt->execute();
							$result = $stmt->get_result();

							if ($result->num_rows > 0) {
								while ($order = $result->fetch_assoc()) {
									echo "<tr>";
									echo "<td>" . htmlspecialchars($order['id']) . "</td>";
									echo "<td>S/" . htmlspecialchars($order['total']) . "</td>";
									echo "<td>" . htmlspecialchars($order['fecha_pedido']) . "</td>";
									echo "<td>" . ucfirst(htmlspecialchars($order['estado'])) . "</td>";
									echo "</tr>";
								}
							} else {
								echo "<tr><td colspan='4' class='text-center'>No se encontraron pedidos.</td></tr>";
							}
						} else {
							echo "<tr><td colspan='4' class='text-center'>Seleccione un usuario para ver los pedidos.</td></tr>";
						}
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>			
							<table class="table align-middle mb-0" id="example2">
								<thead class="table-light">
									<tr>
										<th>ID Cliente</th>
										<th>Nombre</th>
										<th>Correo</th>
										<th>Fecha de Registro</th>
										<th>Rol</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php
									// Consulta para obtener los datos de la tabla usuario
									$sql = "SELECT id, dni, nombre, correo, rol, fecha_registro FROM usuario";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										while ($row = $result->fetch_assoc()) {
											echo "<tr class='client-row'>";
											echo "<td class='client-id'>" . str_pad($row['id'], 3, '0', STR_PAD_LEFT) . "</td>";
											echo "<td class='client-name'><span class='badge cliente-nombre'>" . htmlspecialchars($row['nombre']) . "</span></td>";
											echo "<td class='client-email'>" . htmlspecialchars($row['correo']) . "</td>";
											echo "<td class='client-registration-date'>" . htmlspecialchars(date('d M Y', strtotime($row['fecha_registro']))) . "</td>";
											echo "<td class='client-role'>" . ucfirst($row['rol']) . "</td>";
											echo "<td>
													<div class='d-flex justify-content-center gap-2'>
														<button class='btn-action-details' data-bs-toggle='modal' data-bs-target='#orderDetailsModal' onclick='loadModalContent(" . $row["id"] . ")'>
															<i class='bx bx-file'></i>
														</button>
														<button class='btn-action-delete' data-bs-toggle='tooltip' data-bs-placement='top' title='Eliminar' onclick='showDeleteModal(" . $row['id'] . ")'>
															<i class='bx bx-trash'></i>
														</button>
													</div>
												</td>";
											echo "</tr>";
										}
									} else {
										echo "<tr><td colspan='6' class='text-center'>No se encontraron usuarios.</td></tr>";
									}
									?>
								</tbody>

								
							</table>
						</div>
						<script>
									function loadModalContent(userId) {
										const url = new URL(window.location.href);
										url.searchParams.set('user_id', userId);
										window.history.pushState({}, '', url);
									}
									function showDeleteModal(userId) {
										// Establecer el ID del usuario en el campo oculto del formulario
										document.getElementById('deleteUserId').value = userId;

										// Mostrar el modal
										const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
										deleteModal.show();
									}
								</script>



					</div>


					<style>
						.badge {
							display: inline-block;
							padding: 0.5rem 1rem;
							font-size: 0.8rem;
							font-weight: 600;
							border-radius: 20px; 
							text-align: center;
						}
					
						.badge-activo {
							background-color: rgb(23 160 14 / 0.1); 
							color: #17a64a; /* Verde */
						}
					
						.badge-inactivo {
							background-color: rgb(244 17 39 / 0.1); 
							color: #dc3545; /* Rojo */
						}
						
					</style>
					

                      
                    
                </div>
                  
			</div>
		</div>

		 <div class="overlay toggle-icon"></div>
	
		  <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
	</div>

    <style>
		/* Estilo general para las celdas de la tabla */
		.table td, .table th {
			vertical-align: middle;
			text-align: center;
			border: 1px solid #ddd;
			padding: 10px 15px;
		}
	
		/* Estilo de los encabezados de la tabla */
		.modal-header {
			border-bottom: 2px solid #ddd;
		}
	
		.table-bordered {
			border: 1px solid #ddd;
		}
	
		.table-hover tbody tr:hover {
			background-color: #f9f9f9;
		}
	
		.table td {
			text-align: center !important;
			vertical-align: middle;
			line-height: 50px;
		}
	
		.table td span{
			text-align: center;
			vertical-align: middle;
			line-height: 12px;
		}
	
		.table td .badge-entregado {
			box-shadow: 0 2px 4px #b2dea9;
		}
	
		.table td .badge-pendiente {
			box-shadow: 0 2px 4px #d4e395;
		}
	
		.table td .badge-cancelado {
			box-shadow: 0 2px 4px #f0bcbc;
		}
	
		/* Estilo para los nombres de los clientes */
		.cliente-nombre {
			background-color: #FF6F61;
			color: white;
			padding: 5px 10px;
			border-radius: 20px;
			font-weight: 500;
			text-align: center;
			box-shadow: 0 2px 4px #a46f3a;
		}
	
		/* Estilos generales para los botones de acción */
		.btn-action-details, .btn-action-update, .btn-action-delete {
			display: flex;
			justify-content: center;
			align-items: center;
			color: #f2f2f2;
			padding: 10px;
			border-radius: 20%;
			width: 35px;
			height: 35px;
			border: none;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}
	
		/* Estilos para el icono dentro del botón */
		.btn-action-details .bx, .btn-action-update .bx, .btn-action-delete .bx {
			font-weight: 400 !important;
			font-size: 18px;
		}
	
		/* Efecto hover para los botones */
		.btn-action-details:hover, .btn-action-update:hover, .btn-action-delete:hover {
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		}
	
		/* Botón de detalles (color de fondo) */
		.btn-action-details {
			background-color: #17a2b8;
		}
	
		.btn-action-details:hover {
			background-color: #138496;
		}
	
		/* Botón de Editar (color de fondo) */
		.btn-action-update {
			background-color: #29b466;
		}
	
		.btn-action-update:hover {
			background-color: #30955c;
		}
	
		/* Botón de eliminar (color de fondo y estilo) */
		.btn-action-delete {
			background-color: #dc3545; /* Rojo */
		}
	
		.btn-action-delete:hover {
			background-color: #c82333; /* Rojo más oscuro */
		}
	</style>


	<script>
									document.querySelector('.form-control.ps-5').addEventListener('input', function () {
										const searchValue = this.value.toLowerCase();
										const rows = document.querySelectorAll('.client-row');

										rows.forEach(row => {
											const name = row.querySelector('.client-name').innerText.toLowerCase();
											if (name.includes(searchValue)) {
												row.style.display = '';
											} else {
												row.style.display = 'none';
											}
										});
									});

									document.getElementById('filterStatus').addEventListener('change', function () {
										const selectedValue = this.value.toLowerCase();
										const rows = document.querySelectorAll('.client-row');

										rows.forEach(row => {
											const role = row.querySelector('.client-role').innerText.toLowerCase();
											if (selectedValue === '' || role.includes(selectedValue)) {
												row.style.display = '';
											} else {
												row.style.display = 'none';
											}
										});
									});
								</script>
								

	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/plugins/chartjs/js/chart.js"></script>
    <script src="assets/plugins/sparkline-charts/jquery.sparkline.min.js"></script>
	<script src="assets/js/index.js"></script>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

	<!--app JS-->
	<script src="assets/js/app.js"></script>
	<script>
		new PerfectScrollbar(".app-container")
	</script>

	<script>
		$(document).ready(function() {
			$('#example').DataTable();
		} );
	</script>
	<script>
		$(document).ready(function() {
			var table = $('#example2').DataTable( {
				lengthChange: false,
				buttons: [ 'copy', 'excel', 'pdf', 'print']
			} );
		
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
		} );
	</script>

</body>

</html>