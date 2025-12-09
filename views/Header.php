<?php
/**
 * Header Compartido - Sistema IPS CRM
 * Header superior para todas las vistas del sistema
 */

// Obtener el rol del usuario actual
$rol_usuario = $_SESSION['usuario_rol'] ?? 'guest';
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$usuario_inicial = substr($usuario_nombre, 0, 1);
?>

<!-- Header Superior Compartido -->
<header class="top-header">
    <div class="header-left">
        <div class="user-info">
            <i class="fas fa-<?php 
                echo $rol_usuario === 'administrador' ? 'user-shield' : 
                    ($rol_usuario === 'coordinador' ? 'user-tie' : 
                    ($rol_usuario === 'asesor' ? 'user' : 'user-circle')); 
            ?>"></i>
            <div class="user-details">
                <span class="user-role"><?php 
                    echo $rol_usuario === 'administrador' ? 'Administrador' : 
                        ($rol_usuario === 'coordinador' ? 'Coordinador' : 
                        ($rol_usuario === 'asesor' ? 'Asesor' : 'Usuario')); 
                ?></span>
                <span class="user-name"><?php echo htmlspecialchars($usuario_nombre); ?></span>
            </div>
        </div>
    </div>
    <div class="header-right">
        <div class="header-actions">
            <span class="action-icon" title="Información"><i class="fas fa-circle-info"></i></span>
            <span class="action-icon notification-bell" id="notification-bell" title="Recordatorios" onclick="toggleRecordatorios()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notification-badge" style="display: none;">0</span>
            </span>
        </div>
        <div class="user-profile">
            <img src="https://placehold.co/30x30/FFFFFF/000000?text=<?php echo $usuario_inicial; ?>" 
                 class="user-avatar"
                 alt="Avatar de <?php echo htmlspecialchars($usuario_nombre); ?>">
            <div class="user-menu">
                <span class="user-name"><?php echo htmlspecialchars($usuario_nombre); ?></span>
                <i class="fas fa-caret-down"></i>
            </div>
        </div>
    </div>
</header>

<style>
/* Estilos para el header mejorado */
.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.user-role {
    font-size: 12px;
    font-weight: 600;
    color: #fafafaff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.user-name {
    font-size: 14px;
    font-weight: 500;
    color: #ffffffff;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-right: 15px;
}

.action-icon {
    font-size: 16px;
    color: #ffffff;
    cursor: pointer;
    transition: color 0.3s ease;
    padding: 8px;
    border-radius: 50%;
}

.action-icon:hover {
    color: #f0f0f0;
    background: rgba(255, 255, 255, 0.1);
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 20px;
    transition: background-color 0.3s ease;
}

.user-profile:hover {
    background: #f8f9fa;
}

.user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 5px;
}

.user-menu .user-name {
    font-size: 14px;
    font-weight: 500;
    color: #ebebebff;
}

    .user-menu i {
        font-size: 12px;
        color: #ffffff;
        transition: transform 0.3s ease;
    }
    
    .user-profile:hover .user-menu i {
        transform: rotate(180deg);
    }
    
    /* Estilos para el icono de notificaciones */
    .notification-bell {
        position: relative;
    }
    
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 10px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
    }
    
    /* Dropdown de recordatorios */
    .recordatorios-dropdown {
        display: none;
        position: absolute;
        top: 50px;
        right: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        min-width: 350px;
        max-width: 450px;
        max-height: 500px;
        overflow-y: auto;
        z-index: 10000;
        border: 1px solid #dee2e6;
    }
    
    .recordatorios-dropdown.active {
        display: block;
    }
    
    .recordatorios-header {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }
    
    .recordatorios-header h4 {
        margin: 0;
        font-size: 16px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .recordatorios-close {
        background: none;
        border: none;
        font-size: 18px;
        color: #666;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.2s;
    }
    
    .recordatorios-close:hover {
        background: #e9ecef;
    }
    
    .recordatorios-body {
        padding: 10px;
    }
    
    .recordatorio-item {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    
    .recordatorio-item:hover {
        background: #f8f9fa;
    }
    
    .recordatorio-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .recordatorio-cliente {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
        font-size: 14px;
    }
    
    .recordatorio-info {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
    }
    
    .recordatorio-hora {
        font-size: 13px;
        color: #007bff;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .recordatorios-empty {
        padding: 30px;
        text-align: center;
        color: #666;
    }
    
    .recordatorios-empty i {
        font-size: 48px;
        color: #dee2e6;
        margin-bottom: 10px;
    }

/* Responsive para móviles */
@media (max-width: 768px) {
    .header-left .user-details {
        display: none;
    }
    
    .header-actions {
        margin-right: 10px;
    }
    
    .action-icon {
        font-size: 14px;
        padding: 6px;
    }
    
    .user-menu .user-name {
        display: none;
    }
}
</style>

<!-- Dropdown de Recordatorios -->
<div class="recordatorios-dropdown" id="recordatorios-dropdown">
    <div class="recordatorios-header">
        <h4><i class="fas fa-bell"></i> Recordatorios del Día</h4>
        <button class="recordatorios-close" onclick="toggleRecordatorios()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="recordatorios-body" id="recordatorios-body">
        <div class="recordatorios-empty">
            <i class="fas fa-bell-slash"></i>
            <p>Cargando recordatorios...</p>
        </div>
    </div>
</div>

<script>
let recordatoriosAbierto = false;

function toggleRecordatorios() {
    const dropdown = document.getElementById('recordatorios-dropdown');
    if (!dropdown) return;
    
    recordatoriosAbierto = !recordatoriosAbierto;
    
    if (recordatoriosAbierto) {
        dropdown.classList.add('active');
        cargarRecordatorios();
    } else {
        dropdown.classList.remove('active');
    }
}

async function cargarRecordatorios() {
    const body = document.getElementById('recordatorios-body');
    const badge = document.getElementById('notification-badge');
    if (!body) return;
    
    try {
        const response = await fetch('index.php?action=obtener_recordatorios_dia');
        const data = await response.json();
        
        if (data.success && data.recordatorios && data.recordatorios.length > 0) {
            let html = '';
            data.recordatorios.forEach(recordatorio => {
                const fechaHora = new Date(recordatorio.fecha_hora_recordatorio);
                const horaFormateada = fechaHora.toLocaleTimeString('es-ES', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
                const fechaFormateada = fechaHora.toLocaleDateString('es-ES', {
                    day: 'numeric',
                    month: 'short'
                });
                
                html += `
                    <div class="recordatorio-item" onclick="gestionarClienteDesdeRecordatorio(${recordatorio.cliente_id})">
                        <div class="recordatorio-cliente">
                            <i class="fas fa-user"></i> ${recordatorio.cliente_nombre || 'Cliente'}
                        </div>
                        <div class="recordatorio-info">
                            <i class="fas fa-id-card"></i> CC: ${recordatorio.cliente_cc || 'N/A'}
                        </div>
                        <div class="recordatorio-info">
                            <i class="fas fa-phone"></i> ${recordatorio.cliente_celular || 'N/A'}
                        </div>
                        <div class="recordatorio-hora">
                            <i class="fas fa-clock"></i> ${horaFormateada} - ${fechaFormateada}
                        </div>
                    </div>
                `;
            });
            body.innerHTML = html;
            
            // Actualizar badge
            if (badge) {
                badge.textContent = data.recordatorios.length;
                badge.style.display = 'flex';
            }
        } else {
            body.innerHTML = `
                <div class="recordatorios-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No hay recordatorios para hoy</p>
                    <small>Los recordatorios aparecerán aquí cuando agendes llamadas</small>
                </div>
            `;
            
            // Ocultar badge
            if (badge) {
                badge.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error al cargar recordatorios:', error);
        body.innerHTML = `
            <div class="recordatorios-empty">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error al cargar recordatorios</p>
            </div>
        `;
    }
}

function gestionarClienteDesdeRecordatorio(clienteId) {
    window.location.href = 'index.php?action=asesor_gestionar&cliente_id=' + clienteId;
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('recordatorios-dropdown');
    const bell = document.getElementById('notification-bell');
    
    if (dropdown && bell && recordatoriosAbierto) {
        if (!dropdown.contains(event.target) && !bell.contains(event.target)) {
            toggleRecordatorios();
        }
    }
});

// Cargar recordatorios al cargar la página (solo si es asesor)
<?php if ($rol_usuario === 'asesor'): ?>
document.addEventListener('DOMContentLoaded', function() {
    cargarRecordatorios();
    // Actualizar cada 5 minutos
    setInterval(cargarRecordatorios, 300000);
});
<?php endif; ?>
</script>
