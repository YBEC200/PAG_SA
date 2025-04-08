<?php
include_once '../../../config/database.php';
// Consultas para obtener los datos necesarios
$totalPedidosSql = "SELECT COUNT(*) AS total FROM pedido";
$totalPedidosResult = $conn->query($totalPedidosSql);
$totalPedidos = $totalPedidosResult->fetch_assoc()['total'];

$pendientesSql = "SELECT COUNT(*) AS pendientes FROM pedido WHERE estado = 'pendiente'";
$pendientesResult = $conn->query($pendientesSql);
$pendientes = $pendientesResult->fetch_assoc()['pendientes'];

$entregadosSql = "SELECT COUNT(*) AS entregados FROM pedido WHERE estado = 'entregado'";
$entregadosResult = $conn->query($entregadosSql);
$entregados = $entregadosResult->fetch_assoc()['entregados'];

$canceladosSql = "SELECT COUNT(*) AS cancelados FROM pedido WHERE estado = 'cancelado'";
$canceladosResult = $conn->query($canceladosSql);
$cancelados = $canceladosResult->fetch_assoc()['cancelados'];
if (isset($_GET['id_pedido'])) {
    $idPedido = intval($_GET['id_pedido']);

} else {
    die("ID de pedido no proporcionado.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $detalleId = intval($_POST['delete_id']);

    // Llamar al procedimiento almacenado
    $sql = "CALL eliminar_detalle_pedido(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $detalleId);

    if ($stmt->execute()) {
       // Redirigir a la misma página con el parámetro id_pedido
       header("Location: Historial-Pedidos.php?id_pedido=$idPedido");
       exit;
     } else {
        echo "<script>alert('Error al eliminar el detalle: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $detalleId = intval($_POST['edit_id']);
    $productoId = intval($_POST['producto_id']);
    $cantidad = intval($_POST['cantidad']);

    // Actualizar el detalle en la base de datos
    $sql = "UPDATE detalle_pedido 
            SET id_producto = ?, cantidad = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $productoId, $cantidad, $detalleId);

    if ($stmt->execute()) {
        // Redirigir a la misma página con el parámetro id_pedido
        header("Location: Historial-Pedidos.php?id_pedido=$idPedido");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error al actualizar el detalle: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'], $_POST['id_producto'], $_POST['cantidad'])) {
    $idPedido = intval($_POST['id_pedido']);
    $idProducto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);

    // Insertar el nuevo detalle en la tabla detalle_pedido
    $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $idPedido, $idProducto, $cantidad);

    if ($stmt->execute()) {
        // Redirigir a la misma página con el parámetro id_pedido
        header("Location: Historial-Pedidos.php?id_pedido=$idPedido");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error al agregar el detalle: " . $stmt->error . "</div>";
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



<!--start page wrapper -->
<div class="page-wrapper">
  <div class="page-content">
      <!-- Breadcrum -->
      <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
          <div class="breadcrumb-title pe-3">Comercio Electrónico</div>
          <div class="ps-3">
              <nav aria-label="breadcrumb">
                  <ol class="breadcrumb mb-0 p-0">
                      
                      <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                      <li class="breadcrumb-item active" aria-current="page">Gestión de pedidos</li>
                      <li class="breadcrumb-item active" aria-current="page">Historial de Pedidos #<?php echo htmlspecialchars($idPedido); ?> </li>
                  </ol>
              </nav>
          </div>
      </div>

      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
          <div class="col">
              <div class="card radius-10 bg-gradient-cosmic">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="me-auto">
                              <p class="mb-0 text-white" style="font-weight: 600;">Total Pedidos</p>
                              <h4 class="my-1 text-white"><?php echo $totalPedidos; ?></h4>
                          </div>
                          <div>
                              <i class="bx bx-cart text-white" style="font-size: 2.5rem;"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col">
              <div class="card radius-10 bg-gradient-kyoto">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="me-auto">
                              <p class="mb-0 text-white" style="font-weight: 600;">Pendientes</p>
                              <h4 class="my-1 text-white"><?php echo $pendientes; ?></h4>
                          </div>
                          <div>
                              <i class="bx bx-time text-white" style="font-size: 2.5rem;"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col">
              <div class="card radius-10 bg-gradient-ohhappiness">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="me-auto">
                              <p class="mb-0 text-white" style="font-weight: 600;">Entregados</p>
                              <h4 class="my-1 text-white"><?php echo $entregados; ?></h4>
                          </div>
                          <div>
                              <i class="bx bx-check-circle text-white" style="font-size: 2.5rem;"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col">
              <div class="card radius-10 bg-gradient-ibiza">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="me-auto">
                              <p class="mb-0 text-white" style="font-weight: 600;">Cancelados</p>
                              <h4 class="my-1 text-white"><?php echo $cancelados; ?></h4>
                          </div>
                          <div>
                              <i class="bx bx-x-circle text-white" style="font-size: 2.5rem;"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Tabla de historial de pedidos -->
      <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6>Historial del Pedido #<?php echo htmlspecialchars($idPedido); ?></h6>
                </div>
                <a href="javascript:;" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addDetailModal">Agregar Detalle</a>
                <a href="Gestion-Pedidos.php" class="btn btn-danger ms-auto">Volver</a>
            </div>
        </div>

          <div class="card-body">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                        <th>ID Detalle</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                        <th>Acciones</th> <!-- Nueva columna -->
                    </tr>
                    </thead>
                    <tbody id="orderTable">
                    <?php
                    $sql = "SELECT 
                                detalle_pedido.id AS DetalleID,
                                producto.nombre AS Producto,
                                detalle_pedido.cantidad AS Cantidad,
                                detalle_pedido.precio_unitario AS PrecioUnitario,
                                detalle_pedido.subtotal AS Subtotal
                            FROM detalle_pedido
                            INNER JOIN producto ON detalle_pedido.id_producto = producto.id
                            WHERE detalle_pedido.id_pedido = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $idPedido);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['DetalleID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Producto']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Cantidad']) . "</td>";
                            echo "<td>S/" . number_format($row['PrecioUnitario'], 2) . "</td>";
                            echo "<td>S/" . number_format($row['Subtotal'], 2) . "</td>";
                            echo "<td>
                                    <button class='btn btn-danger btn-sm btn-delete' 
                                            data-id='" . htmlspecialchars($row['DetalleID']) . "'>
                                        Eliminar
                                    </button>
                                    <button class='btn btn-success btn-sm btn-edit' 
                                            data-id='" . htmlspecialchars($row['DetalleID']) . "' 
                                            data-producto='" . htmlspecialchars($row['Producto']) . "' 
                                            data-cantidad='" . htmlspecialchars($row['Cantidad']) . "'>
                                        Editar
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No se encontraron detalles para este pedido.</td></tr>";
                    }
                    ?>
                    </tbody>
                  </table>
                  
              </div>
          </div>
      </div>

  </div>
</div>
<!--end page wrapper -->
<!-- Modal para Agregar Detalle -->
<div class="modal fade" id="addDetailModal" tabindex="-1" aria-labelledby="addDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addDetailModalLabel">Agregar Detalle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDetailForm" method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id_pedido" value="<?php echo htmlspecialchars($idPedido); ?>">
                    <div class="mb-3">
                        <label for="addProducto" class="form-label">Producto</label>
                        <select id="addProducto" name="id_producto" class="form-select" required>
                            <?php
                            // Consulta para obtener los productos
                            $productosSql = "SELECT id, nombre FROM producto";
                            $productosResult = $conn->query($productosSql);
                            while ($producto = $productosResult->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($producto['id']) . "'>" . htmlspecialchars($producto['nombre']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="addCantidad" class="form-label">Cantidad</label>
                        <input type="number" id="addCantidad" name="cantidad" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal de Edición -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="editModalLabel">Editar Detalle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="editId">
                    <div class="mb-3">
                        <label for="editProducto" class="form-label">Producto</label>
                        <select id="editProducto" name="producto_id" class="form-select" required>
                            <?php
                            // Consulta para obtener los productos
                            $productosSql = "SELECT id, nombre FROM producto";
                            $productosResult = $conn->query($productosSql);
                            while ($producto = $productosResult->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($producto['id']) . "'>" . htmlspecialchars($producto['nombre']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editCantidad" class="form-label">Cantidad</label>
                        <input type="number" id="editCantidad" name="cantidad" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal de Eliminación -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este detalle?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" action="">
                    <input type="hidden" name="delete_id" id="deleteId">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
    <style>
        .table td, .table th {
            vertical-align: middle;
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px 15px;
        }


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

            .cliente-nombre {
                background-color: #509faf;
                color: white;
                padding: 5px 10px;
                border-radius: 20px;
                font-weight: 500;
                text-align: center;
                box-shadow: 0 2px 4px #2f7c80;
            }


            .btn-action-details, .btn-action-update {
                display: flex;
                justify-content: center;
                align-items: center;
                color: #f2f2f2;
                padding: 10px;
                border-radius: 20%;
                width: 35px;
                height: 35px;
                border: none;
            }

            .btn-action-details .bx, .btn-action-update .bx{
                font-weight: 400 !important;
                font-size: 18px;
            }

            

            .btn-action-details:hover, .btn-action-update:hover {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }


            .btn-action-details {
                background-color: #17a2b8;
            }

            .btn-action-details:hover {
                background-color: #138496;
            }

            .btn-action-update {
                background-color: #29b466;
            }

            .btn-action-update:hover {
                background-color: #30955c;
            }

            



    </style>


            <script>
                document.getElementById('addDetailForm').addEventListener('submit', function (e) {
    const cantidad = document.getElementById('addCantidad').value;
    const precioUnitario = document.getElementById('addPrecioUnitario').value;

    if (cantidad <= 0 || precioUnitario <= 0) {
        e.preventDefault();
        alert('La cantidad y el precio unitario deben ser mayores a 0.');
    }
});
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function () {
                    const detalleId = this.getAttribute('data-id');
                    const producto = this.getAttribute('data-producto');
                    const cantidad = this.getAttribute('data-cantidad');

                    // Establecer los valores en el modal
                    document.getElementById('editId').value = detalleId;
                    document.getElementById('editProducto').value = producto;
                    document.getElementById('editCantidad').value = cantidad;

                    // Mostrar el modal
                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                });
            });
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function () {
                    const detalleId = this.getAttribute('data-id');
                    const deleteForm = document.getElementById('deleteForm');
                    const deleteIdInput = document.getElementById('deleteId');

                    // Establecer el ID del detalle en el campo oculto del formulario
                    deleteIdInput.value = detalleId;

                    // Establecer la acción del formulario para incluir el parámetro id_pedido
                    deleteForm.action = 'Historial-Pedidos.php?id_pedido=<?php echo $idPedido; ?>';

                    // Mostrar el modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                    deleteModal.show();
                });
            });
            </script>
	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/plugins/chartjs/js/chart.js"></script>
    <script src="assets/plugins/sparkline-charts/jquery.sparkline.min.js"></script>
	<script src="assets/js/index.js"></script>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

	<!--app JS-->
	<script src="assets/js/app.js"></script>
	<script>
		new PerfectScrollbar(".app-container")
	</script>
</body>

</html>