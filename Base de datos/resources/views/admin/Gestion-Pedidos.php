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

// Consulta para obtener los clientes
$clientesSql = "SELECT id, nombre FROM usuario WHERE rol = 'cliente'";
$clientesResult = $conn->query($clientesSql);
$clientes = [];
while ($cliente = $clientesResult->fetch_assoc()) {
    $clientes[] = $cliente;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Actualizar un pedido existente
  if (isset($_POST['pedido_id'], $_POST['cliente_id'], $_POST['estado'])) {
      $pedidoId = intval($_POST['pedido_id']);
      $clienteId = intval($_POST['cliente_id']);
      $estado = $_POST['estado'];

      // Verificar que los datos no estén vacíos
      if ($pedidoId > 0 && $clienteId > 0 && !empty($estado)) {
          // Llamar al procedimiento almacenado
          $sql = "CALL actualizar_pedido(?, ?, ?)";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("iis", $pedidoId, $clienteId, $estado);

          if ($stmt->execute()) {
              header("Location: Gestion-Pedidos.php?success=1");
              exit;
          } else {
              echo "<div class='alert alert-danger'>Error al actualizar el pedido: " . $stmt->error . "</div>";
          }

          $stmt->close();
      } else {
          echo "<div class='alert alert-danger'>Por favor, complete todos los campos.</div>";
      }
  }

  // Agregar un nuevo pedido
  elseif (isset($_POST['id_usuario'], $_POST['total'], $_POST['estado'])) {
      $idUsuario = intval($_POST['id_usuario']);
      $total = floatval($_POST['total']);
      $estado = $_POST['estado'];

      // Insertar el nuevo pedido en la tabla pedido
      $sql = "INSERT INTO pedido (id_usuario, total, estado) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ids", $idUsuario, $total, $estado);

      if ($stmt->execute()) {
          // Redirigir a la misma página con un mensaje de éxito
          header("Location: Gestion-Pedidos.php?success=1");
          exit;
      } else {
          echo "<div class='alert alert-danger'>Error al crear el pedido: " . $stmt->error . "</div>";
      }

      $stmt->close();
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

                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Pedidos</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Gestión de pedidos</li>
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
  
				<div class="card radius-10">
                    <div class="card-header">
                      <div class="d-flex align-items-center">
                        <div>
                          <h6 class="mb-0">Gestión de Pedidos</h6>
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



                   
                



                    <style>
                        .badge {
                          display: inline-block;
                          padding: 0.5rem 1rem;
                          font-size: 0.8rem;
                          font-weight: 600;
                          border-radius: 20px; 
                          text-align: center;
                        }
                        .badge-pendiente {
                          background-color: rgb(255 193 7 / .11); 
                          color: #ffc107;
                        }
                        .badge-entregado {
                          background-color: rgb(23 160 14 / .11); 
                          color: #15ca20; 
                        }
                        .badge-cancelado {
                          background-color: rgb(244 17 39 / .11);
                          color: #fd3550;
                        }
                      </style>

                      
                  <div class="card-body">
                    <div class="table-responsive">
                      <div class="d-flex justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3 flex-grow-1 flex-wrap">
                          <div class="d-flex align-items-center gap-3">
                            <!--<label for="showSelect">Mostrar</label>
                            <select id="showSelect" class="form-select w-auto" style="cursor: pointer;">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                            </select>-->
                          </div>
                    
                            
                            <div class="position-relative flex-grow-1">
                              <input type="search" class="form-control ps-5 radius-30" placeholder="Buscar según el cliente" id="searchInput"  onkeyup="searchClient()">
                              <span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
                            </div>
                            
                        </div>
                    
                        <div class="d-flex align-items-center gap-2 ms-5">
                            <div class="dropdown me-4">
                                <button class="btn btn-light dropdown-toggle" type="button" id="filterDateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrar por Fecha
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="filterDateDropdown">
                                    <li>
                                        <div class="d-flex gap-2">
                                            <input type="date" class="form-control" id="startDate">
                                            <span>a</span>
                                            <input type="date" class="form-control" id="endDate">
                                        </div>
                                    </li>
                                </ul>
                            </div>
                    
                            <select class="form-select w-auto me-4" id="filterSelect">
                              <option value="" selected>Filtrar por Estado</option>
                              <option value="Mostrar todo">Mostrar todo</option>
                              <option value="Pendiente">Pendiente</option>
                              <option value="Entregado">Entregado</option>
                              <option value="Cancelado">Cancelado</option>
                            </select>
                            
                            <button class="btn btn-success" id="exportExcelBtn">
                                <i class="bx bx-file"></i> Excel
                            </button>
                            
                            <button class="btn btn-danger" id="exportPdfBtn">
                                <i class="bx bxs-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOrderModal">
    <i class="bx bx-plus"></i> Crear Pedido
</button>
                        </div>
                    </div>
                    

                    
                    <table class="table align-middle mb-0">
                      <thead class="table-light">
                          <tr>
                              <th>Pedido ID</th>
                              <th>Cliente</th>
                              <th>Fecha</th>
                              <th>Hora</th>
                              <th>Total (S/)</th>
                              <th>Estado</th>
                              <th>Acciones</th>
                          </tr>
                      </thead>
                      <tbody id="orderTable">
                      <?php
                      // Consulta para obtener los datos de la tabla `pedido`
                      $sql = "SELECT 
                      pedido.id AS PedidoID, 
                      usuario.nombre AS Cliente, 
                      DATE(pedido.fecha_pedido) AS Fecha, 
                      TIME(pedido.fecha_pedido) AS Hora, 
                      pedido.total AS Total, 
                      pedido.estado AS Estado
                      FROM pedido
                      INNER JOIN usuario ON pedido.id_usuario = usuario.id
                      ORDER BY pedido.fecha_pedido DESC";

                      $result = $conn->query($sql);
                      if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($row['PedidoID']) . "</td>";
                              echo "<td><span class='badge cliente-nombre'>" . htmlspecialchars($row['Cliente']) . "</span></td>";
                              echo "<td>" . htmlspecialchars($row['Fecha']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['Hora']) . "</td>";
                              echo "<td>S/" . number_format($row['Total'], 2) . "</td>";

                              // Asignar clases de estilo según el estado
                              $estadoClase = '';
                              switch ($row['Estado']) {
                                  case 'pendiente':
                                      $estadoClase = 'badge-pendiente';
                                      break;
                                  case 'entregado':
                                      $estadoClase = 'badge-entregado';
                                      break;
                                  case 'cancelado':
                                      $estadoClase = 'badge-cancelado';
                                      break;
                              }

                              echo "<td><span class='badge $estadoClase'>" . ucfirst($row['Estado']) . "</span></td>";
                              echo "<td><div class='d-flex justify-content-center gap-2'>
                                    <a href='historial-pedidos.php?id_pedido=" . htmlspecialchars($row['PedidoID']) . "' 
                                      class='btn-action-details' 
                                      data-bs-toggle='tooltip' 
                                      data-bs-placement='top' 
                                      title='Detalles'>
                                        <i class='bx bx-file'></i>
                                    </a>
                                    <button class='btn-action-update' data-bs-toggle='modal' data-bs-target='#updateOrderModal'
                                            data-id='{$row['PedidoID']}' 
                                            data-cliente='{$row['Cliente']}' 
                                            data-estado='{$row['Estado']}'>
                                        <i class='bx bxs-edit'></i>
                                    </button></div>
                                  </td>";
                              echo "</tr>";
                          }
                      } else {
                          echo "<tr><td colspan='7' class='text-center'>No se encontraron pedidos</td></tr>";
                      }
                      ?>
                      </tbody>
                    </table>
                    <!-- Mensaje de error -->
                  
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



                    
                  
                  
                  
                  
                  
                    

                  </div>


                      
                    
                </div>

                  <!-- Modal para Crear Pedido -->
                  <div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                          <div class="modal-content">
                              <div class="modal-header bg-primary text-white">
                                  <h5 class="modal-title" id="createOrderModalLabel">Crear Nuevo Pedido</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <form id="createOrderForm" method="POST" action="">
                                  <div class="modal-body">
                                      <div class="mb-3">
                                          <label for="createCliente" class="form-label">Cliente</label>
                                          <select id="createCliente" name="id_usuario" class="form-select" required>
                                              <?php
                                              // Consulta para obtener los clientes
                                              $clientesSql = "SELECT id, nombre FROM usuario WHERE rol = 'cliente'";
                                              $clientesResult = $conn->query($clientesSql);
                                              while ($cliente = $clientesResult->fetch_assoc()) {
                                                  echo "<option value='" . htmlspecialchars($cliente['id']) . "'>" . htmlspecialchars($cliente['nombre']) . "</option>";
                                              }
                                              ?>
                                          </select>
                                      </div>
                                      <div class="mb-3">
                                          <label for="createTotal" class="form-label">Total (S/)</label>
                                          <input type="number" id="createTotal" name="total" class="form-control" step="0.01" min="0" required>
                                      </div>
                                      <div class="mb-3">
                                          <label for="createEstado" class="form-label">Estado</label>
                                          <select id="createEstado" name="estado" class="form-select" required>
                                              <option value="pendiente">Pendiente</option>
                                              <option value="entregado">Entregado</option>
                                              <option value="cancelado">Cancelado</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                      <button type="submit" class="btn btn-primary">Crear</button>
                                  </div>
                              </form>
                          </div>
                      </div>
                  </div>
                  
                    <div class="modal fade" id="updateOrderModal" tabindex="-1">
                      <div class="modal-dialog">
                          <div class="modal-content">
                          <form method="POST" action="Gestion-Pedidos.php">
                              <div class="modal-header">
                                  <h5 class="modal-title">Actualizar Pedido</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                  <input type="hidden" id="updatePedidoId" name="pedido_id">
                                  <div class="mb-3">
                                      <label for="updateCliente">Cliente</label>
                                      <select id="updateCliente" name="cliente_id" class="form-select">
                                          <?php foreach ($clientes as $cliente): ?>
                                              <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre']; ?></option>
                                          <?php endforeach; ?>
                                      </select>
                                  </div>
                                  <div class="mb-3">
                                      <label for="updateEstado">Estado</label>
                                      <select id="updateEstado" name="estado" class="form-select">
                                          <option value="pendiente">Pendiente</option>
                                          <option value="entregado">Entregado</option>
                                          <option value="cancelado">Cancelado</option>
                                      </select>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                  <button type="submit" class="btn btn-primary">Guardar</button>
                              </div>
                          </form>
                          </div>
                      </div>
                    </div>
                    
                  
                  
                  
			</div>
		</div>
    

		 <div class="overlay toggle-icon"></div>
	
		  <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
	</div>
  <script>
    document.getElementById('createOrderForm').addEventListener('submit', function (e) {
    const total = document.getElementById('createTotal').value;

    if (total <= 0) {
        e.preventDefault();
        alert('El total debe ser mayor a 0.');
    }
});
                      document.querySelectorAll('.btn-action-update').forEach(button => {
                        button.addEventListener('click', function () {
                            const pedidoId = this.getAttribute('data-id');
                            const clienteId = this.getAttribute('data-cliente-id'); // Cambiar a data-cliente-id si es necesario
                            const estado = this.getAttribute('data-estado');

                            document.getElementById('updatePedidoId').value = pedidoId;
                            document.getElementById('updateCliente').value = clienteId;
                            document.getElementById('updateEstado').value = estado;

                            const modal = new bootstrap.Modal(document.getElementById('updateOrderModal'));
                            modal.show();
                        });
                    });
                  </script>


  <script>
                              // Filtro por cliente (barra de búsqueda)
                            function searchClient() {
                                const input = document.getElementById('searchInput'); // Obtener el campo de búsqueda
                                const filter = input.value.toLowerCase(); // Convertir el texto ingresado a minúsculas
                                const rows = document.querySelectorAll('#orderTable tr'); // Seleccionar todas las filas de la tabla

                                rows.forEach(row => {
                                    const clientCell = row.querySelector('td:nth-child(2)'); // Seleccionar la celda del cliente (2ª columna)
                                    if (clientCell) {
                                        const clientName = clientCell.textContent.toLowerCase(); // Obtener el texto del cliente en minúsculas

                                        // Mostrar u ocultar la fila según si coincide con el filtro
                                        if (clientName.includes(filter)) {
                                            row.style.display = ''; // Mostrar la fila
                                        } else {
                                            row.style.display = 'none'; // Ocultar la fila
                                        }
                                    }
                                });
                            }

                            // Filtro por fecha
                            document.getElementById('startDate').addEventListener('change', filterByDate);
                            document.getElementById('endDate').addEventListener('change', filterByDate);

                            function filterByDate() {
                                const startDate = document.getElementById('startDate').value; // Fecha de inicio
                                const endDate = document.getElementById('endDate').value; // Fecha de fin
                                const rows = document.querySelectorAll('#orderTable tr'); // Filas de la tabla

                                rows.forEach(row => {
                                    const dateCell = row.querySelector('td:nth-child(3)'); // Celda de la fecha (3ª columna)
                                    if (dateCell) {
                                        const rowDate = dateCell.textContent.trim(); // Fecha de la fila en formato YYYY-MM-DD

                                        // Mostrar u ocultar la fila según el rango de fechas
                                        if (
                                            (!startDate || rowDate >= startDate) && 
                                            (!endDate || rowDate <= endDate)
                                        ) {
                                            row.style.display = ''; // Mostrar la fila
                                        } else {
                                            row.style.display = 'none'; // Ocultar la fila
                                        }
                                    }
                                });
                            }

                            // Filtro por estado
                            document.getElementById('filterSelect').addEventListener('change', function () {
                                const filterValue = this.value.toLowerCase(); // Obtener el valor seleccionado y convertirlo a minúsculas
                                const rows = document.querySelectorAll('#orderTable tr'); // Seleccionar todas las filas de la tabla

                                rows.forEach(row => {
                                    const estadoCell = row.querySelector('td:nth-child(6)'); // Seleccionar la celda de "Estado" (6ª columna)
                                    if (estadoCell) {
                                        const estadoText = estadoCell.textContent.toLowerCase(); // Obtener el texto del estado en minúsculas

                                        // Mostrar u ocultar la fila según el filtro
                                        if (filterValue === '' || filterValue === 'mostrar todo' || estadoText === filterValue) {
                                            row.style.display = ''; // Mostrar la fila
                                        } else {
                                            row.style.display = 'none'; // Ocultar la fila
                                        }
                                    }
                                });
                            });
                            </script>









    <script>
        function loadOrderDetails(orderId) {
            const orders = {
                "P-0001": {
                customerName: "Juan Pérez Pérez",
                date: "25 Ene 2025",
                time: "14:30",
                status: "Pendiente",
                products: [
                    { name: "Producto A", quantity: 2, price: 20, subtotal: 40 },
                    { name: "Producto B", quantity: 1, price: 80, subtotal: 80 },
                ],
                total: 120,
                },
            };

            const order = orders[orderId];
            if (order) {
                document.getElementById("modalOrderId").textContent = orderId;
                document.getElementById("modalCustomerName").textContent = order.customerName;
                document.getElementById("modalOrderDate").textContent = order.date;
                document.getElementById("modalOrderTime").textContent = order.time;
                document.getElementById("modalOrderStatus").textContent = order.status;
                document.getElementById("modalOrderTotal").textContent = `S/ ${order.total.toFixed(2)}`;

                const productsTable = document.getElementById("modalOrderProducts");
                productsTable.innerHTML = "";
                order.products.forEach((product) => {
                const row = `
                    <tr>
                    <td>${product.name}</td>
                    <td>${product.quantity}</td>
                    <td>S/ ${product.price.toFixed(2)}</td>
                    <td>S/ ${product.subtotal.toFixed(2)}</td>
                    </tr>
                `;
                productsTable.innerHTML += row;
                });

                const modal = new bootstrap.Modal(document.getElementById("orderDetailsModal"));
                modal.show();
            }
            }











            // Datos de ejemplo
            const orders = {
            "P-0001": {
                customerName: "Juan Pérez Pérez",
                date: "25 Ene 2025",
                time: "14:30",
                status: "Pendiente",
                total: 120,
            },
            };

            // Cargar datos en el modal de actualización
            function loadOrderForUpdate(orderId) {
            const order = orders[orderId];
            if (order) {
                document.getElementById("updateOrderId").value = orderId;
                document.getElementById("updateCustomerName").value = order.customerName;
                document.getElementById("updateOrderDate").value = order.date;
                document.getElementById("updateOrderTime").value = order.time;
                document.getElementById("updateOrderStatus").value = order.status;

                const modal = new bootstrap.Modal(document.getElementById("updateOrderModal"));
                modal.show();
            }
            }

            // Guardar cambios del pedido
            function saveOrderUpdate() {
            const orderId = document.getElementById("updateOrderId").value;
            const newStatus = document.getElementById("updateOrderStatus").value;

            if (orders[orderId]) {
                orders[orderId].status = newStatus;

                // Actualizar la tabla de pedidos (simulación)
                const statusBadge = document.querySelector(`tr[data-order-id="${orderId}"] .badge`);
                if (statusBadge) {
                statusBadge.textContent = newStatus;
                switch (newStatus) {
                    case "Pendiente":
                    statusBadge.style.backgroundColor = "#ffcc00";
                    statusBadge.style.color = "#333";
                    break;
                    case "En preparación":
                    statusBadge.style.backgroundColor = "#007bff";
                    statusBadge.style.color = "#fff";
                    break;
                    case "Enviado":
                    statusBadge.style.backgroundColor = "#17a2b8";
                    statusBadge.style.color = "#fff";
                    break;
                    case "Entregado":
                    statusBadge.style.backgroundColor = "#28a745";
                    statusBadge.style.color = "#fff";
                    break;
                    case "Cancelado":
                    statusBadge.style.backgroundColor = "#dc3545";
                    statusBadge.style.color = "#fff";
                    break;
                }
                }

                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("updateOrderModal"));
                modal.hide();
                alert("Pedido actualizado correctamente.");
            }
            }

           


    </script>


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
                      // Exportar a Excel
                      document.getElementById('exportExcelBtn').addEventListener('click', function () {
                          const tableData = [];
                          const tableHeaders = [
                              "Pedido ID", "Cliente", "Fecha", "Hora", "Total (S/)", "Estado"
                          ];

                          // Recorremos las filas visibles de la tabla para extraer los datos
                          document.querySelectorAll('#orderTable tr').forEach(row => {
                              if (row.style.display !== 'none') { // Solo procesar filas visibles
                                  const cells = row.querySelectorAll('td');
                                  if (cells.length) {  // Asegurarnos de que solo tomamos las filas con datos
                                      const rowData = [
                                          cells[0].textContent.trim(),  // Pedido ID
                                          cells[1].textContent.trim(),  // Cliente
                                          cells[2].textContent.trim(),  // Fecha
                                          cells[3].textContent.trim(),  // Hora
                                          cells[4].textContent.trim(),  // Total (S/)
                                          cells[5].textContent.trim()   // Estado
                                      ];
                                      tableData.push(rowData);
                                  }
                              }
                          });

                          // Crear libro y hoja de Excel
                          const workbook = XLSX.utils.book_new();
                          const worksheet = XLSX.utils.aoa_to_sheet([tableHeaders, ...tableData]);

                          // Ancho automático de columnas
                          worksheet['!cols'] = tableHeaders.map(() => ({ wch: 20 }));

                          // Exportar el archivo
                          XLSX.utils.book_append_sheet(workbook, worksheet, 'Pedidos');
                          XLSX.writeFile(workbook, 'Gestion_Pedidos.xlsx');
                      });

                      // Exportar a PDF
                      document.getElementById('exportPdfBtn').addEventListener('click', function () {
                          const { jsPDF } = window.jspdf;
                          const doc = new jsPDF();

                          // Título en la parte superior
                          doc.setFontSize(16);
                          doc.setFont("helvetica", "bold");
                          doc.text("Gestión de Pedidos", 105, 20, null, null, "center");

                          const headers = [["Pedido ID", "Cliente", "Fecha", "Hora", "Total (S/)", "Estado"]];
                          const rows = [];

                          // Recorre las filas visibles de la tabla
                          document.querySelectorAll('#orderTable tr').forEach(row => {
                              if (row.style.display !== 'none') { // Solo procesar filas visibles
                                  const cells = row.querySelectorAll('td');
                                  if (cells.length > 0) {
                                      rows.push([
                                          cells[0].textContent.trim(),  // Pedido ID
                                          cells[1].textContent.trim(),  // Cliente
                                          cells[2].textContent.trim(),  // Fecha
                                          cells[3].textContent.trim(),  // Hora
                                          cells[4].textContent.trim(),  // Total (S/)
                                          cells[5].textContent.trim()   // Estado
                                      ]);
                                  }
                              }
                          });

                          // Crear la tabla
                          doc.autoTable({
                              head: headers,
                              body: rows,
                              startY: 30, // Empieza debajo del título
                              theme: 'grid',
                              headStyles: {
                                  fillColor: [220, 53, 69],  // Color rojo para los encabezados
                                  textColor: [255, 255, 255],
                                  halign: 'center'
                              },
                              bodyStyles: {
                                  fontSize: 10,
                                  halign: 'center'
                              }
                          });

                          // Guardar el archivo PDF
                          doc.save('Gestion_Pedidos.pdf');
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