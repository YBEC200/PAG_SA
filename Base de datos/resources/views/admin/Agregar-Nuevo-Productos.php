<?php
include_once '../../../config/database.php';

// Obtener las categorías
$sqlCategorias = "SELECT id, nombre FROM categoria";
$resultCategorias = $conn->query($sqlCategorias);

// Obtener las presentaciones
$sqlPresentaciones = "SELECT id, nombre FROM presentacion";
$resultPresentaciones = $conn->query($sqlPresentaciones);

// Obtener los productos
$sqlProductos = "SELECT id, nombre FROM producto";
$resultProductos = $conn->query($sqlProductos);

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre'])) {
        // Crear un producto
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $id_categoria = $_POST['id_categoria'] ?? null;
        $id_presentacion = $_POST['id_presentacion'] ?? null;
        $svg_content = $_POST['svg_content'] ?? null; // Obtener el contenido SVG
        $alt_text = $_POST['alt_text'] ?? null; // Obtener el texto alternativo

        // Validar que los campos requeridos no estén vacíos
        if (!empty($nombre) && !empty($precio) && !empty($id_categoria) && !empty($id_presentacion) && !empty($svg_content)) {
            // Llamar al procedimiento almacenado para insertar el producto
            $stmt = $conn->prepare("CALL insertar_producto(?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $id_categoria, $id_presentacion);

            if ($stmt->execute()) {
                // Obtener el ID del producto recién creado
                $id_producto = $conn->insert_id;

                // Insertar el contenido SVG y el texto alternativo en la tabla 'imagen'
                $stmtImagen = $conn->prepare("INSERT INTO imagen (id_producto, url_imagen, alt_text) VALUES (?, ?, ?)");
                $stmtImagen->bind_param("iss", $id_producto, $svg_content, $alt_text);

                if ($stmtImagen->execute()) {
                    echo "<script>alert('Producto y su contenido SVG creados exitosamente.');</script>";
                } else {
                    echo "<script>alert('Error al insertar el contenido SVG en la base de datos.');</script>";
                }

                $stmtImagen->close();

                // Redirigir después de crear el producto y el SVG
                header("Location: Agregar-Nuevo-Productos.php?success=productCreated");
                exit;
            } else {
                echo "<script>alert('Ocurrió un error al crear el producto.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Por favor, complete todos los campos requeridos para el producto.');</script>";
        }
    } elseif (isset($_POST['id_producto']) && isset($_POST['numero_lote'])) {
        // Crear un lote
        $id_producto = $_POST['id_producto'] ?? null;
        $numero_lote = $_POST['numero_lote'] ?? '';
        $fecha_entrada = $_POST['fecha_entrada'] ?? '';
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';
        $cantidad = $_POST['cantidad'] ?? 0;

        // Validar que los campos requeridos no estén vacíos
        if (!empty($id_producto) && !empty($numero_lote) && !empty($fecha_entrada) && !empty($fecha_vencimiento) && $cantidad > 0) {
            // Llamar al procedimiento almacenado para insertar el lote
            $stmt = $conn->prepare("CALL crear_lote(?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $id_producto, $numero_lote, $fecha_entrada, $fecha_vencimiento, $cantidad);

            if ($stmt->execute()) {
                header("Location: Agregar-Nuevo-Productos.php?success=lotCreated");
                exit;
            } else {
                echo "<script>alert('Ocurrió un error al crear el lote.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Por favor, complete todos los campos requeridos para el lote.');</script>";
        }
    } elseif (isset($_POST['id_producto']) && isset($_POST['svg_content'])) {
        // Guardar imagen
        $id_producto = $_POST['id_producto'] ?? null;
        $svg_content = $_POST['svg_content'] ?? null;
        $alt_text = $_POST['alt_text'] ?? null;

        // Validar que los campos requeridos no estén vacíos
        if (!empty($id_producto) && !empty($svg_content)) {
            // Insertar el contenido SVG y el texto alternativo en la tabla 'imagen'
            $stmtImagen = $conn->prepare("INSERT INTO imagen (id_producto, url_imagen, alt_text) VALUES (?, ?, ?)");
            $stmtImagen->bind_param("iss", $id_producto, $svg_content, $alt_text);

            if ($stmtImagen->execute()) {
                echo "<script>alert('Imagen guardada exitosamente.');</script>";
                header("Location: Agregar-Nuevo-Productos.php?success=imageSaved");
                exit;
            } else {
                echo "<script>alert('Error al guardar la imagen en la base de datos.');</script>";
            }

            $stmtImagen->close();
        } else {
            echo "<script>alert('Por favor, complete todos los campos requeridos para la imagen.');</script>";
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
	
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Productos</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Gestion Productos</li>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Agregar nuevo producto - Agregar nuevo Lote</li>
							</ol>
						</nav>
					</div>
				</div>
				<!--end breadcrumb-->
			  
				<div class="card">
					<div class="card-body p-4">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title mb-0">Agregar Nuevo Producto  - Agregar nuevo Lote</h5>
							<a href="Gestion-Productos.php" class="btn custom-btn radius-30 mt-2 mt-lg-0" 
								style="background-color: #32acbe; border-color: #269cae; color: white; padding: 10px 20px; border-radius: 30px; text-align: center; display: inline-flex; align-items: center; text-decoration: none;">
								<i class="bx bx-file" style="margin-right: 8px;"></i> Ver la gestión de productos
							</a>
						  </div>
						  <hr />
						<div class="form-body mt-4">
							<div class="row">
								
								<!-- Columna Izquierda -->
								<div class="col-lg-4">
									<div class="border border-3 p-4 rounded">
										<form action="" method="POST">
											<div class="mb-3">
												<label for="inputProductTitle" class="form-label">Nombre del Producto</label>
												<input type="text" class="form-control" id="inputProductTitle" name="nombre" placeholder="Ingresa el nombre del producto" required>
											</div>
											<div class="mb-3">
												<label for="inputProductDescription" class="form-label">Descripción</label>
												<textarea class="form-control" id="inputProductDescription" name="descripcion" rows="3" placeholder="Describe el producto"></textarea>
											</div>
											<div class="mb-3">
												<label for="inputPrice" class="form-label">Precio</label>
												<div class="input-group">
													<span class="input-group-text">S/</span>
													<input type="number" class="form-control" id="inputPrice" name="precio" placeholder="00.00" step="0.01" min="0.01" required>
												</div>
											</div>
											<script>
												document.getElementById('inputPrice').addEventListener('input', function () {
													const input = this;
													if (input.value < 0.00) {
														input.value = ''; // Limpia el campo si el valor es negativo
													}
												});
											</script>
											<div class="mb-3">
												<label for="inputCategory" class="form-label">Categoría</label>
												<select class="form-select" id="inputCategory" name="id_categoria" required>
													<option value="">Seleccionar Categoría</option>
													<?php
													if ($resultCategorias->num_rows > 0) {
														while ($row = $resultCategorias->fetch_assoc()) {
															echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
														}
													}
													?>
												</select>
											</div>
											<div class="mb-3">
												<label for="inputPresentation" class="form-label">Presentación</label>
												<select class="form-select" id="inputPresentation" name="id_presentacion" required>
													<option value="">Seleccionar Presentación</option>
													<?php
													if ($resultPresentaciones->num_rows > 0) {
														while ($row = $resultPresentaciones->fetch_assoc()) {
															echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
														}
													}
													?>
												</select>
											</div>
											<br>
											
											<button type="submit" class="btn btn-primary w-100">Guardar Producto</button>
										</form>
										
									</div>
								</div>
										
										
				
								<!-- Columna Derecha -->
								<div class="col-lg-4">
									<div class="border border-3 p-4 rounded">
										<form action="" method="POST">
											<div class="mb-3">
												<label for="inputProduct" class="form-label">Producto</label>
												<select class="form-select" id="inputProduct" name="id_producto" required>
													<option value="">Seleccionar Producto</option>
													<?php
													if ($resultProductos->num_rows > 0) {
														while ($row = $resultProductos->fetch_assoc()) {
															echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
														}
													}
													?>
												</select>
											</div>
											<div class="mb-3">
												<label for="inputBatchNumber" class="form-label">Número de Lote</label>
												<input type="text" class="form-control" id="inputBatchNumber" name="numero_lote" placeholder="Ingrese el número de lote" required>
											</div>
											<div class="row">
											<div class="col-md-6">
												<label for="inputEntryDate" class="form-label">Fecha de Entrada</label>
												<input type="date" class="form-control" id="inputEntryDate" name="fecha_entrada" required>
											</div>
											<div class="col-md-6">
												<label for="inputExpirationDate" class="form-label">Fecha de Vencimiento</label>
												<input type="date" class="form-control" id="inputExpirationDate" name="fecha_vencimiento" required>
											</div>
											</div>
											<div class="mb-3">
												<label for="inputQuantity" class="form-label">Cantidad</label>
												<input type="number" class="form-control" id="inputQuantity" name="cantidad" placeholder="Cantidad" min="1" required>
											</div>
											
											<div class="col-12">
												<button type="submit" class="btn btn-primary w-100">Guardar Lote</button>
											</div>
										</form>
									</div>
								</div>
								<!-- Formulario de Imagen -->
								<div class="col-lg-4">
									<div class="border border-3 p-4 rounded">
										<form action="" method="POST">
											<div class="mb-3">
												<label for="inputProductImage" class="form-label">Producto</label>
												<select class="form-select" id="inputProductImage" name="id_producto" required>
													<option value="">Seleccionar Producto</option>
													<?php
													$sqlProductos = "SELECT id, nombre FROM producto";
													$resultProductos = $conn->query($sqlProductos);
													if ($resultProductos->num_rows > 0) {
														while ($row = $resultProductos->fetch_assoc()) {
															echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
														}
													}
													?>
												</select>
											</div>
											<div class="mb-3">
												<label for="svg-content" class="form-label">🖋️ Contenido SVG para la imagen del Producto</label>
												<textarea class="form-control" id="svg-content" name="svg_content" rows="5" placeholder="Pega aquí el código SVG" required></textarea>
											</div>
											<div class="mb-3">
												<label for="altText" class="form-label">Texto Alternativo para el SVG</label>
												<input type="text" class="form-control" id="altText" name="alt_text" placeholder="Describe brevemente el contenido del SVG (opcional)">
											</div>
											<div class="col-12">
												<button type="submit" class="btn btn-primary w-100">Guardar Imagen</button>
											</div>
										</form>
									</div>
								</div>
							</div> 
						</div>
					</div>
				</div>




				<script>
					// Maneja la carga de la imagen principal
					document.getElementById("imagen-principal").addEventListener("change", function(evento) {
						const archivo = evento.target.files[0];
						const contenedorVistaPrevia = document.getElementById("vista-previa-imagen-principal");
				
						if (archivo) {
							const lector = new FileReader();
							lector.onload = function(e) {
								contenedorVistaPrevia.innerHTML = `<div class="contenedor-imagen">
									<img src="${e.target.result}" class="imagen-vista-previa">
									<button class="boton-eliminar" onclick="eliminarImagen(this)">X</button>
								</div>`;
							};
							lector.readAsDataURL(archivo);
						}
					});
				
					// Maneja la carga de imágenes secundarias
					document.getElementById("imagenes-secundarias").addEventListener("change", function(evento) {
						const entradaArchivos = evento.target;
						const archivos = entradaArchivos.files;
						const contenedorVistaPrevia = document.getElementById("vista-previa-imagenes");
				
						if (contenedorVistaPrevia.children.length + archivos.length > 5) {
							alert("Solo puedes subir hasta 5 imágenes.");
							entradaArchivos.value = ""; // Limpiar selección de archivos
							return;
						}
				
						for (let i = 0; i < archivos.length; i++) {
							if (contenedorVistaPrevia.children.length >= 5) break;
				
							const archivo = archivos[i];
							const lector = new FileReader();
				
							lector.onload = function(e) {
								const contenedorImagen = document.createElement("div");
								contenedorImagen.classList.add("contenedor-imagen");
				
								const imgElemento = document.createElement("img");
								imgElemento.src = e.target.result;
								imgElemento.classList.add("imagen-vista-previa");
				
								const botonEliminar = document.createElement("button");
								botonEliminar.classList.add("boton-eliminar");
								botonEliminar.innerText = "X";
								botonEliminar.onclick = function() {
									eliminarImagen(botonEliminar);
								};
				
								contenedorImagen.appendChild(imgElemento);
								contenedorImagen.appendChild(botonEliminar);
								contenedorVistaPrevia.appendChild(contenedorImagen);
							};
				
							lector.readAsDataURL(archivo);
						}
					});
				
					// Función para eliminar la imagen
					function eliminarImagen(boton) {
						const contenedorImagen = boton.parentElement;
						contenedorImagen.remove();
				
						// Restablece el valor del input después de eliminar la imagen
						document.getElementById("imagen-principal").value = ""; // Para la imagen principal
						document.getElementById("imagenes-secundarias").value = ""; // Para las imágenes secundarias
					}
				</script>
				
				  
				
				<style>
				.container-fotos {
					max-width: 700px;
					margin: auto;
					background: transparent;
					padding: 20px;
					border-radius: 10px;
					box-shadow: 0 4px 10px rgba(163, 163, 163, 0.9);
				}
				
				label {
					font-weight: bold;
					margin-bottom: 5px;
					display: block;
				}
				
				.file-input {
					display: block;
					width: 100%;
					padding: 10px;
					border: 2px dashed #a2cfff;
					border-radius: 8px;
					text-align: center;
					cursor: pointer;
					transition: 0.3s;
					background: transparent;
				}
				
				.file-input:hover {
					background: #e9f5ff;
				}
				
				.preview-container {
					margin-top: 10px;
					text-align: center;
				}
				
				.contenedor-imagen {
					position: relative;
					display: inline-block;
					margin: 5px;
				}
				
				.imagen-vista-previa {
					border-radius: 10px;
					object-fit: cover;
					transition: transform 0.3s;
					width: 120px;
					height: 120px;
				}
				
				
				
				.boton-eliminar {
					position: absolute;
					top: 2px;
					right: 1px;
					background: rgb(0, 0, 0);
					width: 30px;
					color: white;
					border: none;
					padding: 5px;
					font-size: 14px;
					border-radius: 50%;
					cursor: pointer;
					display: none;
				}
				
				.contenedor-imagen:hover .boton-eliminar {
					display: block;
				}
				
				#vista-previa-imagen-principal img {
					width: 150px;
					height: 150px;
					border: 2px solid #b4d8fe;
					padding: 5px;
				}
				
				#vista-previa-imagenes {
					display: flex;
					flex-wrap: wrap;
					gap: 10px;
					justify-content: center;
				}
				
				#vista-previa-imagenes img {
					width: 90px;
					height: 90px;
					border: 2px solid #89e9e6;
					padding: 3px;
				}
				</style>


				
				
				


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

	<!-- search modal -->

    <!-- end search modal -->



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