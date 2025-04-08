<?php
include_once '../../../config/database.php';
// Manejo de la eliminación del lote
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
		$id = intval($_POST['id']);

		// Llamar al procedimiento almacenado para eliminar el lote
		$stmt = $conn->prepare("CALL eliminar_lote(?)");
		$stmt->bind_param("i", $id);

		if ($stmt->execute()) {
			// Redirigir después de eliminar
			header("Location: Gestion-Productos.php");
			exit;
		} else {
			echo "<script>alert('Error al eliminar el lote.');</script>";
		}

		$stmt->close();
		exit;
	}

	// Manejo de la actualización del lote
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
		$id = intval($_POST['edit_id']);
		$id_producto = intval($_POST['id_producto']);
		$numero_lote = $_POST['numero_lote'];
		$fecha_entrada = $_POST['fecha_entrada'];
		$fecha_vencimiento = $_POST['fecha_vencimiento'];
		$cantidad = intval($_POST['cantidad']);

		// Llamar al procedimiento almacenado para actualizar el lote
		$stmt = $conn->prepare("CALL actualizar_lote(?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iisssi", $id, $id_producto, $numero_lote, $fecha_entrada, $fecha_vencimiento, $cantidad);

		if ($stmt->execute()) {
			echo "<script>alert('Lote actualizado exitosamente.');</script>";
		} else {
			echo "<script>alert('Error al actualizar el lote.');</script>";
		}

		$stmt->close();
		header("Location: Gestion-Productos.php");
		exit;
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

		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Productos</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
								<li class="breadcrumb-item active" aria-current="page">Gestión de productos</li>
							</ol>
						</nav>
					</div>
				</div>
			  
				<div class="card">
					<div class="card-body">
						<div class="d-lg-flex justify-content-between align-items-center mb-4 gap-3">
							<!-- Sección izquierda: Mostrar productos y búsqueda -->
							<div class="d-flex align-items-center gap-3 flex-grow-1 flex-wrap">
								<!--<div class="d-flex align-items-center flex-shrink-0">
									<label for="productsPerPageSelect" class="me-2 fw-bold" >Mostrar</label>
									<select class="form-select w-auto" id="productsPerPageSelect" style="cursor: pointer;">
										<option value="5" selected>5</option>
										<option value="10">10</option>
										<option value="25">25</option>
										<option value="50">50</option>
										<option value="100">100</option>
									</select>
								</div>-->
						
								<div class="position-relative flex-grow-1">
									<input type="search" class="form-control ps-5 radius-30" placeholder="Buscar producto por nombres" id="searchInput">
									<span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
								</div>
							</div>
						
							<!-- Sección derecha: Filtro, exportar e imprimir -->
							<div class="d-flex align-items-center gap-2 flex-wrap justify-content-end flex-shrink-0">

									<label for="categoryFilter">Categoría:</label>
										<select class="form-select w-auto" id="categoryFilter" onchange="filterTable()">
											<option value="">Todas</option>
											<?php
											// Obtener categorías de la base de datos
											$categorySql = "SELECT DISTINCT nombre FROM categoria";
											$categoryResult = $conn->query($categorySql);
											while ($category = $categoryResult->fetch_assoc()) {
												echo "<option value='" . htmlspecialchars($category['nombre']) . "'>" . htmlspecialchars($category['nombre']) . "</option>";
											}
											?>
										</select>

									<label for="filterSelect">Presentación:</label>
										<select class="form-select w-auto" id="filterSelect" onchange="filterTable()">
											<option value="">Todas</option>
											<?php
											// Obtener presentaciones de la base de datos
											$presentationSql = "SELECT DISTINCT nombre FROM presentacion";
											$presentationResult = $conn->query($presentationSql);
											while ($presentation = $presentationResult->fetch_assoc()) {
												echo "<option value='" . htmlspecialchars($presentation['nombre']) . "'>" . htmlspecialchars($presentation['nombre']) . "</option>";
											}
											?>
										</select>
								
								<button class="btn-export-excel d-flex align-items-center" id="exportExcelBtn">
									<i class='bx bx-file'></i>
									<span class="ms-1">Excel</span>
								</button>
								
						
								<button class="btn-export-pdf d-flex align-items-center" id="exportPdfBtn">
									<i class='bx bxs-file-pdf'></i>
									<span class="ms-1">PDF</span>
								</button>
								
						
								<!-- Botón agregar producto -->
								<a href="Agregar-Nuevo-Productos.php" class="btn btn-primary radius-30 ms-5 d-inline-flex align-items-center">
									<i class="bx bxs-plus-square"></i> Agregar nuevo producto
								</a>
							</div>
						</div>
						
						
						<style>
							.form-control:hover{
								box-shadow: 0 2px 4px #fc9090;
							}

							.form-control:focus{
								outline: none !important ;
								box-shadow: none;
								box-shadow: 0 0 20px #ffa4a4;
							}

							.btn-export-excel, .btn-export-pdf {
								font-size: 13px;
								width: 100px;
								padding: 6px 10px;
								font-weight: 600;
								border-radius: 8px;
								border: none;
								justify-content: center;
							}
						
							.btn-export-excel {
								background-color: #28a745;
								color: white;
							}

							.btn-export-excel:hover {
								background-color: #277639;
							}
						
							.btn-export-pdf {
								background-color: #dc3545;
								color: white;
							}

							.btn-export-pdf:hover {
								background-color: #b12e3b;
							}

							.btn-export-excel i, .btn-export-pdf i{
								font-size: 18px;
							}
						
							

							@media (max-width: 995px) {
								.d-lg-flex {
									flex-wrap: wrap !important;
								}

								.d-lg-flex > div {
									width: 100%;
									margin-bottom: 10px; 
								}

								.btn-export-excel, .btn-export-pdf, .btn-primary {
									width: 100%;
									text-align: center;
								}

								.gap-2 {
									gap: 5px;
								}
							}


						</style>
						
						
						
						<div class="table-responsive">
						<table class="table mb-0">
							<thead class="table-light">
								<tr>
									<th>ID</th>
									<th>Nombre</th>
									<th>Precio</th>
									<th>Stock</th>
									<th>Lote</th>
									<th>Categoría</th>
									<th>Presentación</th>
									<th>Fecha Registro</th>
									<th>Fecha de Vencimiento</th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody id="productTable">
								<?php
								$sql = "SELECT 
											lote.id AS ID, 
											producto.nombre AS NOMBRE, 
											producto.precio AS PRECIO, 
											lote.cantidad AS STOCK,
											lote.numero_lote AS NUMERO_LOTE,
											categoria.nombre AS CATEGORIA, 
											presentacion.nombre AS PRESENTACION, 
											lote.fecha_entrada AS FECHA_REGISTRO, 
											lote.fecha_vencimiento AS FECHA_VENCIMIENTO
										FROM lote
										INNER JOIN producto ON producto.id = lote.id_producto
										INNER JOIN categoria ON producto.id_categoria = categoria.id
										INNER JOIN presentacion ON producto.id_presentacion = presentacion.id
										ORDER BY lote.id ASC"; // Ordenar por ID del lote en orden ascendente
								$result = $conn->query($sql);

								// Verificar si la consulta fue exitosa
								if ($result === false) {
									echo "<tr><td colspan='9' class='text-center'>Error en la consulta: " . $conn->error . "</td></tr>";
								} else {
									if ($result->num_rows > 0) {
										while ($row = $result->fetch_assoc()) {
											echo "<tr>";
											echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
											echo "<td>" . htmlspecialchars($row['NOMBRE']) . "</td>";
											echo "<td>S/" . number_format($row['PRECIO'], 2) . "</td>";
											echo "<td>" . htmlspecialchars($row['STOCK']) . "</td>";
											echo "<td>" . htmlspecialchars($row['NUMERO_LOTE']) . "</td>";
											echo "<td>" . htmlspecialchars($row['CATEGORIA']) . "</td>";
											echo "<td>" . htmlspecialchars($row['PRESENTACION']) . "</td>";
											echo "<td>" . date("d-m-Y", strtotime($row['FECHA_REGISTRO'])) . "</td>";
											echo "<td>" . date("d-m-Y", strtotime($row['FECHA_VENCIMIENTO'])) . "</td>";
											echo "<td>
												    <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal' data-id='" . htmlspecialchars($row['ID']) . "' data-producto='" . htmlspecialchars($row['NOMBRE']) . "' data-lote='" . htmlspecialchars($row['STOCK']) . "' data-entrada='" . htmlspecialchars($row['FECHA_REGISTRO']) . "' data-vencimiento='" . htmlspecialchars($row['FECHA_VENCIMIENTO']) . "'>Editar</button>
													<button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#confirmDeleteModal' data-id='" . htmlspecialchars($row['ID']) . "'>Eliminar</button>
												</td>";
											echo "</tr>";
										}
									} else {
										echo "<tr><td colspan='9' class='text-center'>No se encontraron productos</td></tr>";
									}
								}
								?>
							</tbody>
							<!-- Modal de Confirmación -->
							<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
										</div>
										<div class="modal-body">
											¿Estás seguro de que deseas eliminar este lote?
										</div>
										<div class="modal-footer">
											<form action="" method="POST">
												<input type="hidden" name="id" id="deleteId">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
												<button type="submit" class="btn btn-danger">Eliminar</button>
											</form>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="editModalLabel">Editar Lote</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
										</div>
										<form action="" method="POST">
											<div class="modal-body">
												<input type="hidden" name="edit_id" id="editId">
												<div class="mb-3">
													<label for="id_producto" class="form-label">Producto</label>
													<select class="form-select" id="id_producto" name="id_producto" required>

														<?php
														$productosSql = "SELECT id, nombre FROM producto";
														$productosResult = $conn->query($productosSql);
														while ($producto = $productosResult->fetch_assoc()) {
															echo "<option value='" . htmlspecialchars($producto['id']) . "'>" . htmlspecialchars($producto['nombre']) . "</option>";
														}
														?>
													</select>
												</div>
												<div class="mb-3">
													<label for="numero_lote" class="form-label">Número de Lote</label>
													<input type="text" class="form-control" id="numero_lote" name="numero_lote" placeholder="Número de Lote" required>
												</div>
												<div class="mb-3">
													<label for="fecha_entrada" class="form-label">Fecha de Entrada</label>
													<input type="date" class="form-control" id="fecha_entrada" name="fecha_entrada" placeholder="Fecha de Entrada" required>
												</div>
												<div class="mb-3">
													<label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
													<input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" placeholder="Fecha de Vencimiento" required>
												</div>
												<div class="mb-3">
													<label for="cantidad" class="form-label">Cantidad</label>
													<input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="Cantidad" required>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
												<button type="submit" class="btn btn-primary">Guardar Cambios</button>
											</div>
										</form>
									</div>
								</div>
							</div>

							

						</table>

							
						</div>
					</div>
				</div>

			</div>
		</div>

		

		

		<div class="overlay toggle-icon"></div>
	</div>




	<style>
		.btn-success, .btn-warning {
			background-color: #3ea556d3;
			border-color: #28a745;
			font-size: 12px; 
			color: #fff; 
			font-weight: 400;
			line-height: 10px;
		}
		.btn-warning {
			background-color: #ffd149;
			border-color: #ffc107;
		}
		.table th, .table td {
			text-align: center ;
			border: 1px solid #ddd; 
			padding: 9px 15px; 
		}

		.table tbody tr {
			border-bottom: 1px solid #ddd;
		}
		

		.table td, .table th {
			vertical-align: middle;
		}
		.table th {
			padding: 15px 5px;
		}
		.product-show {
			position: absolute;
			top: 50%;
			left: 5px;
			transform: translateY(-50%);
		}
		.table img {
			width: 70px !important;
			height: 70px !important;
			object-fit: cover;
		}
		.border-bottom {
			border-bottom: 1px solid #ddd;
		}
		.form-select {
			width: auto;
			margin-right: 10px;
		}

		.order-actions {
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.order-actions .bxs-edit {
			color: #f2f2f2;
			font-weight: 500;
			
		}
		.order-actions .bxs-trash {
			color: #f2f2f2;
			font-weight: 500;
		}

		



	</style>
	





	  
	  
	
	<!-- Incluye la biblioteca XLSX -->
	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
	
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.28"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

		
		
	<script>
								const editModal = document.getElementById('editModal');
								editModal.addEventListener('show.bs.modal', function (event) {
									const button = event.relatedTarget; // Botón que activó el modal
									const id = button.getAttribute('data-id'); // ID del lote

									// Realizar una solicitud AJAX para obtener los datos del lote
									fetch(`get_lote.php?id=${id}`)
										.then(response => response.json())
										.then(data => {
											if (data.error) {
												alert(data.error);
											} else {
												// Asignar los valores como placeholder en los campos del modal
												document.getElementById('editId').value = id;
												document.getElementById('id_producto').value = data.ID_PRODUCTO;
												document.getElementById('numero_lote').placeholder = data.NUMERO_LOTE;
												document.getElementById('fecha_entrada').placeholder = data.FECHA_ENTRADA;
												document.getElementById('fecha_vencimiento').placeholder = data.FECHA_VENCIMIENTO;
												document.getElementById('cantidad').placeholder = data.CANTIDAD;
											}
										})
										.catch(error => console.error('Error al obtener los datos del lote:', error));
								});
								const confirmDeleteModal = document.getElementById('confirmDeleteModal');
								confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
									const button = event.relatedTarget; // Botón que activó el modal
									const id = button.getAttribute('data-id'); // Obtener el ID del lote
									const deleteIdInput = document.getElementById('deleteId'); // Campo oculto en el formulario
									deleteIdInput.value = id; // Asignar el ID al campo oculto
								});
	</script>
	<script>
								// Aquí va tu script
								// Obtener los elementos del DOM
								const categoryFilter = document.getElementById('categoryFilter');
								const filterSelect = document.getElementById('filterSelect');
								const productTable = document.getElementById('productTable');
								const searchInput = document.getElementById('searchInput');

								// Función para filtrar la tabla
								function filterTable() {
									const categoryValue = categoryFilter.value.toLowerCase(); // Valor del filtro de categoría
									const presentationValue = filterSelect.value.toLowerCase(); // Valor del filtro de presentación
									const searchValue = searchInput.value.toLowerCase(); // Valor de la barra de búsqueda
									const rows = productTable.getElementsByTagName('tr'); // Todas las filas de la tabla

									for (let i = 0; i < rows.length; i++) {
										const cells = rows[i].getElementsByTagName('td');
										if (cells.length > 0) {
											const productName = cells[1].textContent.toLowerCase(); // Columna "Nombre" (2ª columna)
											const categoryName = cells[5].textContent.toLowerCase(); // Columna "Categoría" (6ª columna)
											const presentationName = cells[6].textContent.toLowerCase(); // Columna "Presentación" (7ª columna)

											// Verificar si la fila coincide con los filtros
											const matchesSearch = productName.includes(searchValue);
											const matchesCategory = categoryValue === "" || categoryName === categoryValue;
											const matchesPresentation = presentationValue === "" || presentationName === presentationValue;

											if (matchesSearch && matchesCategory && matchesPresentation) {
												rows[i].style.display = ''; // Mostrar la fila
											} else {
												rows[i].style.display = 'none'; // Ocultar la fila
											}
										}
									}
								}

								// Agregar eventos a los filtros
								categoryFilter.addEventListener('change', filterTable);
								filterSelect.addEventListener('change', filterTable);
								searchInput.addEventListener('input', filterTable);
	</script>
	<!-- Luego, tu código que usa jsPDF -->
	<script>
		// Función para exportar a Excel
		document.getElementById('exportExcelBtn').addEventListener('click', function () {
			const tableData = [];
			const tableHeaders = [
				"ID", "Nombre", "Precio", "Stock", "Categoría", "Presentación", "Fecha Registro", "Fecha de Vencimiento"
			];

			// Recorremos las filas visibles de la tabla para extraer los datos
			document.querySelectorAll('#productTable tr').forEach(row => {
				if (row.style.display !== 'none') { // Solo incluir filas visibles
					const cells = row.querySelectorAll('td');
					const rowData = Array.from(cells).map(cell => cell.textContent.trim());
					tableData.push(rowData);
				}
			});

			// Crear libro y hoja de Excel
			const workbook = XLSX.utils.book_new();
			const dataWithHeader = [tableHeaders, ...tableData];
			const worksheet = XLSX.utils.aoa_to_sheet(dataWithHeader);

			// Exportar el archivo
			XLSX.utils.book_append_sheet(workbook, worksheet, 'Productos');
			XLSX.writeFile(workbook, 'Gestion_Productos.xlsx');
		});

		// Función para exportar a PDF
		document.getElementById('exportPdfBtn').addEventListener('click', function () {
			const { jsPDF } = window.jspdf;
			const doc = new jsPDF();

			// Agregar el título centrado
			doc.setFontSize(16);
			doc.setTextColor(40);
			doc.setFont("helvetica", "bold");
			doc.text("Gestión de Productos", doc.internal.pageSize.getWidth() / 2, 15, { align: "center" });

			// Encabezados de la tabla
			const headers = [["ID", "Nombre", "Precio", "Stock", "Categoría", "Presentación", "Fecha Registro", "Fecha de Vencimiento"]];
			const rows = [];

			// Recorre las filas visibles de la tabla y extrae los datos
			document.querySelectorAll('#productTable tr').forEach(row => {
				if (row.style.display !== 'none') { // Solo incluir filas visibles
					const cells = row.querySelectorAll('td');
					if (cells.length > 0) {
						rows.push([
							cells[0].textContent.trim(), // ID
							cells[1].textContent.trim(), // Nombre
							cells[2].textContent.trim(), // Precio
							cells[3].textContent.trim(), // Stock
							cells[4].textContent.trim(), // Categoría
							cells[5].textContent.trim(), // Presentación
							cells[6].textContent.trim(), // Fecha Registro
							cells[7].textContent.trim()  // Fecha de Vencimiento
						]);
					}
				}
			});

			// Generar la tabla en el PDF
			doc.autoTable({
				head: headers,
				body: rows,
				startY: 30,
				theme: 'grid',
				headStyles: {
					fillColor: [40, 167, 69], // Color verde
					textColor: [255, 255, 255],
					halign: 'center'
				},
				bodyStyles: {
					fontSize: 10,
					halign: 'center'
				}
			});

			// Guardar el PDF
			doc.save('Gestion_Productos.pdf');
		});
	</script>
	
  




	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="assets/plugins/Drag-And-Drop/dist/imageuploadify.min.js"></script>
	<script>
		$(document).ready(function () {
			$('#image-uploadify').imageuploadify();
		})
	</script>
	<!--app JS-->
	<script src="assets/js/app.js"></script>
</body>

</html>