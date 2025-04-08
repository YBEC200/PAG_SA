<?php
// Conexión a la base de datos
include_once '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar qué formulario fue enviado
    if (isset($_POST['formType'])) {
        $formType = $_POST['formType'];

        if ($formType === 'category' && isset($_POST['categoryName'], $_POST['categoryDescription'])) {
            // Manejo del formulario de categorías
            $nombreCategoria = $_POST['categoryName'];
            $descripcionCategoria = $_POST['categoryDescription'];

            if (!empty($nombreCategoria) && !empty($descripcionCategoria)) {
                $stmt = $conn->prepare("CALL insertar_categoria(?, ?)");
                $stmt->bind_param("ss", $nombreCategoria, $descripcionCategoria);

                if ($stmt->execute()) {
                    // Redirigir después de procesar el formulario
                    header("Location: Gestion-Categorias-Presentaciones.php?success=category");
                    exit;
                } else {
                    echo "<script>alert('Ocurrió un error al registrar la categoría.');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('Por favor, complete todos los campos de la categoría.');</script>";
            }
        } elseif ($formType === 'presentation' && isset($_POST['presentationName'], $_POST['presentationDescription'])) {
            // Manejo del formulario de presentaciones
            $nombrePresentacion = $_POST['presentationName'];
            $descripcionPresentacion = $_POST['presentationDescription'];

            if (!empty($nombrePresentacion) && !empty($descripcionPresentacion)) {
                $stmt = $conn->prepare("CALL insertar_presentacion(?, ?)");
                $stmt->bind_param("ss", $nombrePresentacion, $descripcionPresentacion);

                if ($stmt->execute()) {
                    // Redirigir después de procesar el formulario
                    header("Location: Gestion-Categorias-Presentaciones.php?success=presentation");
                    exit;
                } else {
                    echo "<script>alert('Ocurrió un error al registrar la presentación.');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('Por favor, complete todos los campos de la presentación.');</script>";
            }
        } elseif ($formType === 'deletePresentation' && isset($_POST['presentationId'])) {
            // Manejo del formulario de eliminación de presentaciones
            $idPresentacion = intval($_POST['presentationId']);

            if (!empty($idPresentacion)) {
                // Llamar al procedimiento almacenado para eliminar la presentación
                $stmt = $conn->prepare("CALL eliminar_presentacion(?)");
                $stmt->bind_param("i", $idPresentacion);

                if ($stmt->execute()) {
                    // Redirigir después de eliminar
                    header("Location: Gestion-Categorias-Presentaciones.php?success=delete");
                    exit;
                } else {
                    echo "<script>alert('Ocurrió un error al eliminar la presentación.');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('ID de presentación no válido.');</script>";
            }
        } elseif ($formType === 'deleteCategory' && isset($_POST['categoryId'])) {
			// Manejo del formulario de eliminación de categorías
			$idCategoria = intval($_POST['categoryId']);

			if (!empty($idCategoria)) {
				// Llamar al procedimiento almacenado para eliminar la categoría
				$stmt = $conn->prepare("CALL eliminar_categoria(?)");
				$stmt->bind_param("i", $idCategoria);

				if ($stmt->execute()) {
					// Redirigir después de eliminar
					header("Location: Gestion-Categorias-Presentaciones.php?success=deleteCategory");
					exit;
				} else {
					echo "<script>alert('Ocurrió un error al eliminar la categoría.');</script>";
				}

				$stmt->close();
			} else {
				echo "<script>alert('ID de categoría no válido.');</script>";
			}
		} elseif ($formType === 'editCategory' && isset($_POST['categoryId'], $_POST['categoryName'], $_POST['categoryDescription'])) {
			// Manejo del formulario de edición de categorías
			$idCategoria = intval($_POST['categoryId']);
        $nombreCategoria = $_POST['categoryName'];
        $descripcionCategoria = $_POST['categoryDescription'];

			if (!empty($idCategoria) && !empty($nombreCategoria) && !empty($descripcionCategoria)) {
				// Llamar al procedimiento almacenado para actualizar la categoría
				$stmt = $conn->prepare("CALL actualizar_categoria(?, ?, ?)");
				$stmt->bind_param("iss", $idCategoria, $nombreCategoria, $descripcionCategoria);

				if ($stmt->execute()) {
					// Redirigir después de actualizar
					header("Location: Gestion-Categorias-Presentaciones.php?success=editCategory");
					exit;
				} else {
					echo "<script>alert('Ocurrió un error al actualizar la categoría.');</script>";
				}

				$stmt->close();
			} else {
				echo "<script>alert('Por favor, complete todos los campos para editar la categoría.');</script>";
			}
    	} elseif ($formType === 'editPresentation' && isset($_POST['presentationId'], $_POST['presentationName'], $_POST['presentationDescription'])) {
			// Manejo del formulario de edición de presentaciones
			$idPresentacion = intval($_POST['presentationId']);
        $nombrePresentacion = $_POST['presentationName'];
        $descripcionPresentacion = $_POST['presentationDescription'];

        if (!empty($idPresentacion) && !empty($nombrePresentacion) && !empty($descripcionPresentacion)) {
            // Llamar al procedimiento almacenado para actualizar la presentación
            $stmt = $conn->prepare("CALL actualizar_presentacion(?, ?, ?)");
            $stmt->bind_param("iss", $idPresentacion, $nombrePresentacion, $descripcionPresentacion);

            if ($stmt->execute()) {
                // Redirigir después de actualizar
                header("Location: Gestion-Categorias-Presentaciones.php?success=editPresentation");
                exit;
            } else {
                echo "<script>alert('Ocurrió un error al actualizar la presentación.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Por favor, complete todos los campos para editar la presentación.');</script>";
        }
	}
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



		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Tipos de Productos</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
								<li class="breadcrumb-item active" aria-current="page">Gestión de Categorías y Presentaciones</li>
							</ol>
						</nav>
					</div>
				</div>
		
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between mb-4 flex-wrap">
							<!-- Sección Categorías -->
							<div class="flex-fill me-3 mb-3">
								<div class="d-flex justify-content-between mb-3">
									<div>
										<label for="categorySearch" class="fw-bold">Buscar Categoría</label>
										<input type="search" class="form-control" id="categorySearch" placeholder="Buscar categoría">
									</div>
									<button class="btn btn-primary" id="addCategoryBtn" data-bs-toggle="modal" data-bs-target="#categoryModal">Agregar Nueva Categoría</button>
									<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="categoryModalLabel">Registrar Nueva Categoría</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
											<form id="categoryForm" action="" method="POST">
												<input type="hidden" name="formType" value="category">
												<div class="mb-3">
													<label for="categoryName" class="form-label">Nombre de la Categoría</label>
													<input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Ingrese el nombre de la categoría" required>
												</div>
												<div class="mb-3">
													<label for="categoryDescription" class="form-label">Descripción</label>
													<textarea class="form-control" id="categoryDescription" name="categoryDescription" rows="3" placeholder="Ingrese una descripción" required></textarea>
												</div>
												<button type="submit" class="btn btn-primary">Guardar</button>
											</form>
											</div>
										</div>
									</div>
									</div>
								</div>
								<div class="table-responsive mb-3">
									<table class="table mb-0">
										<thead class="table-light">
											<tr>
												<th>ID</th>
												<th>Nombre</th>
												<th>Acciones</th>
											</tr>
										</thead>
										<tbody id="categoryTable">
										<?php
											// Consulta para obtener las categorías
											$sql = "SELECT id, nombre, descripcion FROM categoria";
											$result = $conn->query($sql);

											// Verificar si hay resultados
											if ($result->num_rows > 0) {
												// Recorrer los resultados y generar las filas de la tabla
												while ($row = $result->fetch_assoc()) {
													echo "<tr class='category-row'>";
													echo "<td>" . htmlspecialchars($row['id']) . "</td>";
													echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
													echo "<td>
															<button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editCategoryModal' onclick='populateEditCategoryModal(" . htmlspecialchars($row['id']) . ", \"" . htmlspecialchars($row['nombre']) . "\", \"" . htmlspecialchars($row['descripcion']) . "\")'>Editar</button>
															<button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteCategoryModal' onclick='setDeleteCategoryId(" . htmlspecialchars($row['id']) . ")'>Eliminar</button>
														</td>";
													echo "</tr>";
												}
											} else {
												// Mostrar un mensaje si no hay categorías
												echo "<tr><td colspan='4' class='text-center'>No se encontraron categorías</td></tr>";
											}
											?>
										</tbody>
									</table>
									<!-- Modal para Confirmar Eliminación de Categoría -->
									<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="deleteCategoryModalLabel">Eliminar Categoría</h5>
													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
												</div>
												<div class="modal-body">
													<p>¿Estás seguro de que deseas eliminar esta categoría?</p>
													<form id="deleteCategoryForm" action="" method="POST">
														<input type="hidden" name="formType" value="deleteCategory">
														<input type="hidden" id="deleteCategoryId" name="categoryId">
														<button type="submit" class="btn btn-danger">Eliminar</button>
														<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
													</form>
												</div>
											</div>
										</div>
									</div>
									<!-- Modal para Editar Categoría -->
									<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="editCategoryModalLabel">Editar Categoría</h5>
													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
												</div>
												<div class="modal-body">
													<form id="editCategoryForm" action="" method="POST">
														<input type="hidden" name="formType" value="editCategory">
														<input type="hidden" id="editCategoryId" name="categoryId">
														<div class="mb-3">
															<label for="editCategoryName" class="form-label">Nombre de la Categoría</label>
															<input type="text" class="form-control" id="editCategoryName" name="categoryName" required>
														</div>
														<div class="mb-3">
															<label for="editCategoryDescription" class="form-label">Descripción</label>
															<textarea class="form-control" id="editCategoryDescription" name="categoryDescription" rows="3" placeholder="Ingrese una descripción aquí..." required></textarea>
														</div>
														<button type="submit" class="btn btn-primary">Guardar Cambios</button>
													</form>
												</div>
											</div>
										</div>
									</div>
									<script>
										function populateEditCategoryModal(id, name, description) {
											document.getElementById('editCategoryId').value = id;
											document.getElementById('editCategoryName').value = name;
											document.getElementById('editCategoryDescription').value = description;
										}
									</script>
									<script>
										function setDeleteCategoryId(id) {
											document.getElementById('deleteCategoryId').value = id;
										}
									</script>
								</div>
							</div>
		
							<div class="custom-divider"></div>		
							<!-- Sección Presentaciones -->
							<div class="flex-fill ms-3 mb-3">
								<div class="d-flex justify-content-between mb-3">
									<div>
										<label for="presentationSearch" class="fw-bold">Buscar Presentación</label>
										<input type="search" class="form-control" id="presentationSearch" placeholder="Buscar presentación">
									</div>
									<button class="btn btn-primary" id="addPresentationBtn" data-bs-toggle="modal" data-bs-target="#presentationModal">Agregar Nueva Presentación</button>
									<!-- Modal para Registrar Nueva Presentación -->
									<div class="modal fade" id="presentationModal" tabindex="-1" aria-labelledby="presentationModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="presentationModalLabel">Registrar Nueva Presentación</h5>
													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
												</div>
												<div class="modal-body">
												<form id="presentationForm" action="" method="POST">
													<input type="hidden" name="formType" value="presentation">
													<div class="mb-3">
														<label for="presentationName" class="form-label">Nombre de la Presentación</label>
														<input type="text" class="form-control" id="presentationName" name="presentationName" placeholder="Ingrese el nombre de la presentación" required>
													</div>
													<div class="mb-3">
														<label for="presentationDescription" class="form-label">Descripción</label>
														<textarea class="form-control" id="presentationDescription" name="presentationDescription" rows="3" placeholder="Ingrese una descripción" required></textarea>
													</div>
													<button type="submit" class="btn btn-primary">Guardar</button>
												</form>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="table-responsive mb-3">
									<table class="table mb-0">
										<thead class="table-light">
											<tr>
												<th>ID</th>
												<th>Nombre</th>
												<th>Acciones</th>
											</tr>
										</thead>
										<tbody id="presentationTable">
											<?php
											// Consulta para obtener las presentaciones
											$sql = "SELECT id, nombre, descripcion FROM presentacion";
											$result = $conn->query($sql);

											// Verificar si hay resultados
											if ($result->num_rows > 0) {
												// Recorrer los resultados y generar las filas de la tabla
												while ($row = $result->fetch_assoc()) {
													echo "<tr class='presentation-row'>";
													echo "<td>" . htmlspecialchars($row['id']) . "</td>";
													echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
													echo "<td>
															<button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editPresentationModal' onclick='populateEditPresentationModal(" . htmlspecialchars($row['id']) . ", \"" . htmlspecialchars($row['nombre']) . "\", \"" . htmlspecialchars($row['descripcion']) . "\")'>Editar</button>
															<button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deletePresentationModal' onclick='setDeleteId(" . $row['id'] . ")'>Eliminar</button>
														</td>";
													echo "</tr>";
												}
											} else {
												// Mostrar un mensaje si no hay presentaciones
												echo "<tr><td colspan='4' class='text-center'>No se encontraron presentaciones</td></tr>";
											}

											?>
											
										</tbody>
										
										<script>
											function setDeleteId(id) {
												document.getElementById('deletePresentationId').value = id;
											}
										</script>
									</table>
									<!-- Modal para Confirmar Eliminación -->
										<div class="modal fade" id="deletePresentationModal" tabindex="-1" aria-labelledby="deletePresentationModalLabel" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="deletePresentationModalLabel">Eliminar Presentación</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													</div>
													<div class="modal-body">
														<p>¿Estás seguro de que deseas eliminar esta presentación?</p>
														<form id="deletePresentationForm" action="" method="POST">
															<input type="hidden" name="formType" value="deletePresentation">
															<input type="hidden" id="deletePresentationId" name="presentationId">
															<button type="submit" class="btn btn-danger">Eliminar</button>
															<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
														</form>
													</div>
												</div>
											</div>
										</div>
									<!-- Modal para Editar Presentación -->
									<div class="modal fade" id="editPresentationModal" tabindex="-1" aria-labelledby="editPresentationModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="editPresentationModalLabel">Editar Presentación</h5>
													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
												</div>
												<div class="modal-body">
													<form id="editPresentationForm" action="" method="POST">
														<input type="hidden" name="formType" value="editPresentation">
														<input type="hidden" id="editPresentationId" name="presentationId">
														<div class="mb-3">
															<label for="editPresentationName" class="form-label">Nombre de la Presentación</label>
															<input type="text" class="form-control" id="editPresentationName" name="presentationName" required>
														</div>
														<div class="mb-3">
															<label for="editPresentationDescription" class="form-label">Descripción</label>
															<textarea class="form-control" id="editPresentationDescription" name="presentationDescription" rows="3" placeholder="Ingrese una descripción aquí..." required></textarea>
														</div>
														<button type="submit" class="btn btn-primary">Guardar Cambios</button>
													</form>
												</div>
											</div>
										</div>
									</div>
									<script>
										function populateEditPresentationModal(id, name, description) {
											document.getElementById('editPresentationId').value = id;
											document.getElementById('editPresentationName').value = name;
											document.getElementById('editPresentationDescription').value = description;
										}
									</script>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
									
		<script> // Filtrado de tablas
			// Obtener los elementos del DOM
			const categorySearch = document.getElementById('categorySearch');
			const categoryTable = document.getElementById('categoryTable');
			const presentationSearch = document.getElementById('presentationSearch');
			const presentationTable = document.getElementById('presentationTable');

			// Función para filtrar la tabla de categorías
			categorySearch.addEventListener('input', function () {
				const filter = categorySearch.value.toLowerCase(); // Convertir el texto a minúsculas
				const rows = categoryTable.getElementsByTagName('tr'); // Obtener todas las filas de la tabla

				for (let i = 0; i < rows.length; i++) {
					const cells = rows[i].getElementsByTagName('td'); // Obtener las celdas de la fila
					if (cells.length > 0) {
						const categoryName = cells[1].textContent.toLowerCase(); // Columna "Nombre"
						if (categoryName.includes(filter)) {
							rows[i].style.display = ''; // Mostrar la fila si coincide
						} else {
							rows[i].style.display = 'none'; // Ocultar la fila si no coincide
						}
					}
				}
			});

			// Función para filtrar la tabla de presentaciones
			presentationSearch.addEventListener('input', function () {
				const filter = presentationSearch.value.toLowerCase(); // Convertir el texto a minúsculas
				const rows = presentationTable.getElementsByTagName('tr'); // Obtener todas las filas de la tabla

				for (let i = 0; i < rows.length; i++) {
					const cells = rows[i].getElementsByTagName('td'); // Obtener las celdas de la fila
					if (cells.length > 0) {
						const presentationName = cells[1].textContent.toLowerCase(); // Columna "Nombre"
						if (presentationName.includes(filter)) {
							rows[i].style.display = ''; // Mostrar la fila si coincide
						} else {
							rows[i].style.display = 'none'; // Ocultar la fila si no coincide
						}
					}
				}
			});
		</script>
		<style>
			.custom-divider {
				display: block !important;
				border-left: 1px solid #ddd !important;
				height: 100% !important;
				width: 0;
			}



			/* Estilo para que las tablas se apilen verticalmente en pantallas pequeñas */
			@media (max-width: 1250px) {
				.custom-divider {
					border-top: 2px solid #ddd;  /* Cambio a 1px en lugar de border-left */
					margin: 25px 0;
					width: 100%; /* Hacemos que la línea ocupe todo el ancho */
					height: 0;  /* Removemos la altura para evitar que sea visible verticalmente */
				}
				.page-wrapper .row {
					flex-direction: column;
					align-items: stretch;
				}
				
				.col-md-6 {
					width: 100%;
					margin-bottom: 20px; /* Añadir margen entre las secciones */
				}
			}

			/* Estilo para la línea divisoria entre Categorías y Presentaciones */
			/* Línea divisoria entre Categorías y Presentaciones */
			.page-wrapper .col-md-12 {
				border-top: 1px solid #ddd; /* Asegura que la línea sea horizontal */
				margin-top: 20px; /* Da un poco de espacio entre las tablas y la línea */
				margin-bottom: 20px; /* Espacio debajo de la línea */
			}

		</style>		
		
		
		


		

		

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
	





	<!-- search modal -->
    


	
  




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