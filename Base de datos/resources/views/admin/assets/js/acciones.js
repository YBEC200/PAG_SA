// Función para cargar los detalles del cliente para editar
function cargarClienteParaActualizar(userId) {
    fetch(`../../../controllers/userAcciones.php?action=edit&id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user) {
                // Llenar el formulario con los datos del usuario
                document.getElementById('editUserId').value = data.user.id;
                document.getElementById('editName').value = data.user.nombre;
                document.getElementById('editDni').value = data.user.dni;
                document.getElementById('editEmail').value = data.user.correo;
                document.getElementById('editPhone').value = data.user.telefono;
                document.getElementById('editRole').value = data.user.rol;
                
                // Mostrar el modal
                new bootstrap.Modal(document.getElementById('editClientModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Función mejorada para mostrar el toast
function showToast(message, type = 'success') {
    return new Promise((resolve) => {
        const toast = document.getElementById('successToast');
        const toastMessage = document.getElementById('toastMessage');
        
        // Configurar el toast
        toast.classList.remove('bg-success', 'bg-danger');
        toast.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');
        toastMessage.textContent = message;
        
        // Crear instancia del toast con mayor duración
        const bsToast = new bootstrap.Toast(toast, {
            delay: 3500,
            animation: true,
            autohide: true
        });
        
        // Evento para resolver la promesa cuando el toast se oculte
        toast.addEventListener('hidden.bs.toast', () => {
            resolve();
        }, { once: true });
        
        bsToast.show();
    });
}

// Evento para guardar cambios
document.getElementById('saveChangesBtn').addEventListener('click', async function() {
    const formData = {
        action: 'update',
        id: document.getElementById('editUserId').value,
        nombre: document.getElementById('editName').value,
        dni: document.getElementById('editDni').value,
        correo: document.getElementById('editEmail').value,
        telefono: document.getElementById('editPhone').value,
        rol: document.getElementById('editRole').value
    };

    try {
        const response = await fetch('../../../controllers/userAcciones.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();

        if (data.success) {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editClientModal'));
            modal.hide();
            
            // Recargar la página
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
    }
});

// Función para eliminar usuario
async function eliminarCliente(userId) {
    if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
        try {
            const response = await fetch(`../../../controllers/userAcciones.php?action=delete&id=${userId}`);
            const data = await response.json();

            if (data.success) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}
