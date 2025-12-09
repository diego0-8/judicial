<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cliente - <?php echo APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="assets/css/coordinador-dashboard.css">
    <link rel="stylesheet" href="assets/css/asesor_gestionar.css">
</head>
<body data-user-id="<?php echo $_SESSION['usuario_id'] ?? ''; ?>">

    <?php 
    // Incluir navbar compartido
    $action = 'asesor_gestionar';
    include __DIR__ . '/Navbar.php'; 
    ?>

    <div class="gestion-container">

        <!-- Contenido principal en tres columnas -->
        <div class="gestion-content-tres-columnas">
            
            <!-- COLUMNA 1: INFORMACIÓN DEL CLIENTE Y CONTRATOS -->
            <div class="columna-uno">
                <!-- Información del Cliente -->
                <div class="seccion-info-cliente">
                    <h3><i class="fas fa-user"></i> Información del Cliente</h3>
                    <div class="cliente-detalles">
                        <h4 id="cliente-nombre-completo">Cargando...</h4>
                        <div class="cliente-datos-lista">
                            <div class="cliente-dato">
                                <span class="dato-label"><i class="fas fa-id-card"></i> CC:</span>
                                <span id="cliente-cedula">Cargando...</span>
                            </div>
                            <div class="cliente-dato">
                                <span class="dato-label"><i class="fas fa-phone"></i> Celulares:</span>
                                <div class="telefonos-cliente" id="telefonos-cliente">
                                    <span>Cargando...</span>
                                </div>
                            </div>
                            <div class="cliente-dato" id="cliente-email-container" style="display: none;">
                                <span class="dato-label"><i class="fas fa-envelope"></i> Email:</span>
                                <span id="cliente-email">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Obligaciones -->
                <div class="seccion-contratos">
                    <h3 id="contratos-titulo"><i class="fas fa-file-invoice-dollar"></i> Obligaciones</h3>
                    <div class="contratos-container" id="contratos-container">
                        <div class="cargando-contratos">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Cargando obligaciones...</p>
                        </div>
                    </div>
                </div>

                <!-- Botón agregar información -->
                <button class="btn-agregar-info">
                    <i class="fas fa-plus"></i> Agregar más información
                </button>

            </div>

            <!-- COLUMNA 2: ÁRBOL DE TIPIFICACIÓN -->
            <div class="columna-dos">
                <div class="seccion-tipificacion">
                    <h3><i class="fas fa-sitemap"></i> Perfilación del cliente</h3>
                    <div class="tipificacion-form">
                        <div class="form-group">
                            <label><i class="fas fa-phone-alt"></i> Canal de Contacto:</label>
                            <select id="canal-contacto">
                                <option value="">Selecciona una opción</option>
                                <option value="llamada">Llamada</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="email">Correo Electrónico</option>
                                <option value="sms">SMS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-file-invoice"></i> Obligación a Gestionar: <small style="color: #666;">(Opcional - Si no selecciona ninguna, se guardará como "Ninguna")</small></label>
                            <select id="contrato-gestionar">
                                <option value="">Selecciona una factura (opcional)</option>
                                <option value="ninguna">Ninguna (Cliente no quiso pagar ninguna)</option>
                                <!-- Las facturas se cargarán dinámicamente -->
                            </select>
                        </div>
                        <div class="form-group" id="opciones-todas-facturas" style="display: none; margin-top: 10px;">
                            <div style="display: flex; gap: 15px; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                                <label style="margin: 0; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="gestionar-obligaciones" value="todas" id="radio-todas" onchange="manejarSeleccionObligaciones('todas')">
                                    <span style="font-weight: 500;"><i class="fas fa-check-double"></i> Tipificar todas las obligaciones</span>
                                </label>
                                <label style="margin: 0; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="gestionar-obligaciones" value="ninguna" id="radio-ninguna" onchange="manejarSeleccionObligaciones('ninguna')">
                                    <span style="font-weight: 500;"><i class="fas fa-times"></i> Ninguna</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Nivel 1 - Tipo de Contacto:</label>
                            <select id="tipo-contacto-nivel1" required>
                                <option value="">Selecciona una opción</option>
                                <option value="llamada_saliente">LLAMADA SALIENTE</option>
                                <option value="whatsapp">WHATSAPP</option>
                                <option value="email">EMAIL</option>
                                <option value="recibir_llamada">RECIBIR LLAMADA</option>
                            </select>
                        </div>
                        <!-- Nivel 2 - Visible solo si hay selección en Nivel 1 -->
                        <div class="form-group" id="nivel2-container" style="display: none;">
                            <label><i class="fas fa-tag"></i> Nivel 2 - Clasificación:</label>
                            <select id="tipo-contacto-nivel2">
                                <option value="">Primero selecciona el Nivel 1</option>
                            </select>
                        </div>
                        <!-- Nivel 3 - Visible solo si hay selección en Nivel 2 -->
                        <div class="form-group" id="nivel3-container" style="display: none;">
                            <label><i class="fas fa-tag"></i> Nivel 3 - Detalle:</label>
                            <select id="tipo-contacto-nivel3">
                                <option value="">Primero selecciona el Nivel 2</option>
                            </select>
                        </div>
                        <!-- Campos adicionales para ACUERDO DE PAGO y COMPROMISO DE PAGO -->
                        <div class="form-group" id="campos-fecha-valor" style="display: none;">
                            <label><i class="fas fa-calendar-alt"></i> Fecha y Valor:</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="date" id="fecha-pago" placeholder="Fecha de pago" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" min="">
                                <div style="flex: 1; position: relative;">
                                    <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #666; font-weight: 600;">$</span>
                                    <input type="text" id="valor-pago" placeholder="0" style="width: 100%; padding: 8px 8px 8px 30px; border: 1px solid #ddd; border-radius: 4px;" inputmode="numeric">
                                </div>
                            </div>
                        </div>
                        <!-- Campos adicionales para RECORDATORIO (Seguimiento negociación vigente) -->
                        <div class="form-group" id="campos-recordatorio" style="display: none;">
                            <label><i class="fas fa-clock"></i> Fecha y Hora del Recordatorio:</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="date" id="fecha-recordatorio" placeholder="Fecha" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" min="">
                                <input type="time" id="hora-recordatorio" placeholder="Hora" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Se te recordará llamar a este cliente en la fecha y hora seleccionadas
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Observaciones y Comentarios -->
                <div class="seccion-observaciones">
                    <h3><i class="fas fa-comment-dots"></i> Observaciones y Comentarios</h3>
                    <p class="instrucciones">Documente las interacciones y seguimientos pertinentes</p>
                    <div class="observaciones-detalladas">
                        <label>Observaciones Detalladas:</label>
                        <textarea id="observaciones-texto" rows="10" placeholder="Describe detalladamente el resultado de la gestión, acuerdos, próximos pasos, objeciones del cliente, etc."></textarea>
                    </div>
                </div>
                
                <!-- Canales de Comunicación -->
                <div class="seccion-canales">
                    <h3><i class="fas fa-broadcast-tower"></i> Canales de Comunicación Autorizados</h3>
                    <p class="instrucciones">Seleccione los canales autorizados por la empresa para futuras comunicaciones</p>
                    <div class="canales-lista">
                        <div class="canal-item">
                            <input type="checkbox" id="canal-llamada">
                            <label for="canal-llamada">
                                <i class="fas fa-phone"></i>
                                Llamada Telefónica
                            </label>
                        </div>
                        <div class="canal-item">
                            <input type="checkbox" id="canal-whatsapp">
                            <label for="canal-whatsapp">
                                <i class="fab fa-whatsapp"></i>
                                WhatsApp
                            </label>
                        </div>
                        <div class="canal-item">
                            <input type="checkbox" id="canal-email">
                            <label for="canal-email">
                                <i class="fas fa-envelope"></i>
                                Correo Electrónico
                            </label>
                        </div>
                        <div class="canal-item">
                            <input type="checkbox" id="canal-sms">
                            <label for="canal-sms">
                                <i class="fas fa-sms"></i>
                                SMS
                            </label>
                        </div>
                        <div class="canal-item">
                            <input type="checkbox" id="canal-correo">
                            <label for="canal-correo">
                                <i class="fas fa-mail-bulk"></i>
                                Correo Físico
                            </label>
                        </div>
                        <div class="canal-item">
                            <input type="checkbox" id="canal-mensajeria">
                            <label for="canal-mensajeria">
                                <i class="fas fa-comments"></i>
                                Mensajería por Aplicaciones
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA 3: SOFTPHONE Y CANALES -->
            <div class="columna-tres">
                <!-- Softphone WebRTC - Solo visible para asesores con extensión -->
                <?php
                // Obtener datos del usuario desde la base de datos para verificar extensión
                require_once 'models/Usuario.php';
                $usuario_model = new Usuario();
                
                // Intentar obtener el usuario de múltiples formas
                $usuario_data = false;
                $identificador_usado = '';
                
                // Método 1: Por cédula desde sesión
                if (!empty($_SESSION['usuario_cedula'])) {
                    $identificador_usado = $_SESSION['usuario_cedula'];
                    $usuario_data = $usuario_model->obtenerPorCedula($identificador_usado);
                    if ($usuario_data && defined('ASTERISK_DEBUG_MODE') && ASTERISK_DEBUG_MODE) {
                        error_log("DEBUG Softphone - Usuario encontrado por usuario_cedula: " . $identificador_usado);
                    }
                }
                
                // Método 2: Por usuario_id (que también es la cédula según AuthController)
                if (!$usuario_data && !empty($_SESSION['usuario_id'])) {
                    $identificador_usado = $_SESSION['usuario_id'];
                    $usuario_data = $usuario_model->obtenerPorCedula($identificador_usado);
                    if ($usuario_data && defined('ASTERISK_DEBUG_MODE') && ASTERISK_DEBUG_MODE) {
                        error_log("DEBUG Softphone - Usuario encontrado por usuario_id: " . $identificador_usado);
                    }
                }
                
                // DEBUG: Verificar datos obtenidos
                if (defined('ASTERISK_DEBUG_MODE') && ASTERISK_DEBUG_MODE) {
                    error_log("DEBUG Softphone - Variables de sesión:");
                    error_log("  - usuario_cedula: " . ($_SESSION['usuario_cedula'] ?? 'NO DEFINIDA'));
                    error_log("  - usuario_id: " . ($_SESSION['usuario_id'] ?? 'NO DEFINIDA'));
                    error_log("  - usuario_rol: " . ($_SESSION['usuario_rol'] ?? 'NO DEFINIDO'));
                    
                    if ($usuario_data) {
                        error_log("DEBUG Softphone - Usuario encontrado:");
                        error_log("  - Cédula: " . ($usuario_data['cedula'] ?? 'NO DEFINIDA'));
                        error_log("  - Extension: " . ($usuario_data['extension'] ?? 'NO DEFINIDA'));
                        error_log("  - SIP Password: " . (!empty($usuario_data['sip_password']) ? 'DEFINIDA (' . strlen($usuario_data['sip_password']) . ' caracteres)' : 'VACIA'));
                    } else {
                        error_log("DEBUG Softphone - ERROR: Usuario NO encontrado");
                        error_log("  - Intentó con: " . ($identificador_usado ?: 'NINGUNO'));
                    }
                }
                
                // Verificar que el usuario sea asesor Y tenga extensión y clave SIP asignadas
                $mostrar_softphone = (
                    isset($_SESSION['usuario_rol']) && 
                    $_SESSION['usuario_rol'] === 'asesor' &&
                    $usuario_data &&
                    !empty($usuario_data['extension'] ?? '') &&
                    !empty($usuario_data['sip_password'] ?? '')
                );
                
                // DEBUG: Verificar resultado de mostrar_softphone
                if (defined('ASTERISK_DEBUG_MODE') && ASTERISK_DEBUG_MODE) {
                    error_log("DEBUG Softphone - Mostrar softphone: " . ($mostrar_softphone ? 'SI' : 'NO'));
                    error_log("DEBUG Softphone - Rol: " . ($_SESSION['usuario_rol'] ?? 'NO DEFINIDO'));
                }
                
                if ($mostrar_softphone):
                ?>
                <div class="seccion-softphone-wrapper" style="margin-bottom: 20px;">
                    <div id="webrtc-softphone" class="webrtc-softphone-panel inline"></div>
                </div>
                <?php endif; ?>

                <!-- Cuadro de WhatsApp Web -->
                <div class="seccion-whatsapp">
                    <h3><i class="fab fa-whatsapp"></i> WhatsApp Web</h3>
                    
                    <div class="whatsapp-container">
                        <div class="whatsapp-mock">
                            <div class="whatsapp-mock-header">
                                <div class="whatsapp-mock-avatar"><i class="fab fa-whatsapp"></i></div>
                                <div class="whatsapp-mock-info">
                                    <div class="whatsapp-mock-name">Your Company <i class="fas fa-check-circle verificado"></i></div>
                                    <div class="whatsapp-mock-status">Business Account</div>
                                </div>
                                <div class="whatsapp-mock-actions"><i class="fas fa-ellipsis-v"></i></div>  
                            </div>
                            <div class="whatsapp-mock-messages" id="whatsapp-mock-messages">
                                <div class="bubble incoming">
                                    <div class="bubble-text">Hi there,<br>how can we help you?</div>
                                    <div class="bubble-options">Options</div>
                                    <div class="bubble-time">8:32 am</div>
                                </div>
                            </div>
                            <form id="whatsapp-mock-form" class="whatsapp-mock-input">
                                <div class="mock-icons-left">
                                    <i class="fas fa-smile"></i>
                                </div>
                                <textarea id="whatsapp-mock-input" rows="1" placeholder="Type a message"></textarea>
                                <div class="mock-icons-right">
                                    <i class="fas fa-paperclip"></i>
                                    <i class="fas fa-camera"></i>
                                </div>
                                <button type="button" class="mock-send" id="whatsapp-mock-send"><i class="fas fa-microphone"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de Correo Electrónico -->
                <div class="seccion-email">
                    <h3><i class="fas fa-envelope"></i> Enviar Correo Electrónico</h3>
                    
                    <div class="email-container">
                        <form id="email-form" class="email-form">
                            <div class="email-field">
                                <label for="email-destinatario">
                                    <i class="fas fa-user"></i> Para:
                                </label>
                                <input type="email" id="email-destinatario" placeholder="correo@ejemplo.com" required>
                                <button type="button" class="btn-cargar-email" id="btn-cargar-email-cliente" title="Cargar email del cliente">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                            </div>
                            
                            <div class="email-field">
                                <label for="email-asunto">
                                    <i class="fas fa-tag"></i> Asunto:
                                </label>
                                <input type="text" id="email-asunto" placeholder="Asunto del correo" required>
                            </div>
                            
                            <div class="email-field">
                                <label for="email-mensaje">
                                    <i class="fas fa-comment"></i> Mensaje:
                                </label>
                                <textarea id="email-mensaje" rows="8" placeholder="Escribe tu mensaje aquí..." required></textarea>
                            </div>
                            
                            <div class="email-actions">
                                <button type="button" class="btn-email btn-adjuntar" id="btn-adjuntar-archivo" title="Adjuntar archivo">
                                    <i class="fas fa-paperclip"></i> Adjuntar
                                </button>
                                <input type="file" id="email-archivo" style="display: none;" multiple>
                                <span id="email-archivos-nombres" class="email-archivos-nombres"></span>
                                <button type="submit" class="btn-email btn-enviar">
                                    <i class="fas fa-paper-plane"></i> Enviar Correo
                                </button>
                            </div>
                            
                            <div id="email-status" class="email-status"></div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Botones de acción principales -->
        <div class="action-buttons" id="action-buttons-container" style="display: flex; gap: 15px; justify-content: center; align-items: center; flex-wrap: wrap;">
            <!-- Botones iniciales (antes de guardar) -->
            <div id="botones-iniciales" style="display: flex; gap: 15px; align-items: center;">
                <button class="btn-action btn-primary" onclick="guardarGestion()">
                    <i class="fas fa-save"></i> Guardar Gestión
                </button>
                <button class="btn-action btn-secondary" onclick="volverTareas()">
                    <i class="fas fa-tasks"></i> Volver a Tareas
                </button>
                <button class="btn-action btn-success" onclick="irDashboard()">
                    <i class="fas fa-home"></i> Ir al Dashboard
                </button>
                <button class="btn-action btn-warning" onclick="irJudicializado()">
                    <i class="fas fa-gavel"></i> Judicializado
                </button>
            </div>
            
            <!-- Botones después de guardar (ocultos inicialmente) -->
            <div id="botones-despues-guardar" style="display: none; gap: 15px; align-items: center;">
                <button class="btn-action btn-primary" id="btn-siguiente-cliente" onclick="irSiguienteCliente()" style="display: none;">
                    <i class="fas fa-arrow-right"></i> Siguiente Cliente
                </button>
                <button class="btn-action btn-info" onclick="mostrarBusquedaCliente()">
                    <i class="fas fa-search"></i> Buscar Cliente
                </button>
                <button class="btn-action btn-secondary" onclick="volverClientes()">
                    <i class="fas fa-users"></i> Volver a Clientes
                </button>
            </div>
        </div>

        <!-- Historial de gestiones (ancho completo) -->
        <div class="seccion-historial-full">
            <h3><i class="fas fa-history"></i> Historial de Gestiones</h3>
            <div id="historial-container">
                <div class="historial-vacio">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando historial...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Tiempo de Sesión -->
    <div id="modal-tiempo-sesion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; min-width: 400px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #007bff;">
                    <i class="fas fa-clock"></i> Tiempo de Sesión
                </h3>
                <button onclick="toggleTiempoModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <span style="display: block; margin-bottom: 5px; color: #666; font-size: 13px;">Hora Actual</span>
                    <span id="reloj-activo" style="font-size: 20px; font-weight: 700; color: #007bff;">--:-- --</span>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <span style="display: block; margin-bottom: 5px; color: #666; font-size: 13px;">Tiempo de Sesión</span>
                    <span id="tiempo-sesion" style="font-size: 20px; font-weight: 700; color: #28a745;">00:00:00</span>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <button id="btn-pausa" onclick="iniciarPausaBreak()" style="padding: 12px; background: #ffc107; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-coffee"></i> Break
                    </button>
                    <button id="btn-almuerzo" onclick="iniciarPausaAlmuerzo()" style="padding: 12px; background: #fd7e14; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-utensils"></i> Almuerzo
                    </button>
                    <button id="btn-bano" onclick="iniciarPausaBano()" style="padding: 12px; background: #17a2b8; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-toilet"></i> Baño
                    </button>
                    <button id="btn-mantenimiento" onclick="iniciarPausaMantenimiento()" style="padding: 12px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-tools"></i> Mantenimiento
                    </button>
                    <button id="btn-pausa-activa" onclick="iniciarPausaActiva()" style="padding: 12px; background: #20c997; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-running"></i> Pausa Activa
                    </button>
                    <button id="btn-actividad-extra" onclick="iniciarActividadExtra()" style="padding: 12px; background: #6610f2; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-stopwatch"></i> Actividad Extra
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Pausa (cuando está en pausa) -->
    <div id="modal-pausa" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10001; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; max-width: 400px;">
            <i class="fas fa-clock" style="font-size: 48px; color: #ffc107; margin-bottom: 20px;"></i>
            <h3 style="margin: 0 0 10px 0; color: #333;">En Pausa</h3>
            <p style="margin: 0 0 20px 0; color: #666;" id="tipo-pausa-texto">Break de 30 minutos</p>
            <div style="font-size: 32px; font-weight: 700; color: #007bff; margin-bottom: 20px;">
                <span class="tiempo-pausa">30:00</span>
            </div>
            <button onclick="mostrarModalVerificacion()" class="btn btn-primary" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                <i class="fas fa-play"></i> Continuar Trabajo
            </button>
        </div>
    </div>

    <!-- Modal de Verificación de Contraseña -->
    <div id="modal-verificacion-contrasena" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10002; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <i class="fas fa-lock" style="font-size: 48px; color: #007bff; margin-bottom: 20px;"></i>
            <h3 style="margin: 0 0 10px 0; color: #333;">Verificación de Contraseña</h3>
            <p style="margin: 0 0 20px 0; color: #666;">Ingrese su contraseña para reanudar la sesión</p>
            
            <div style="margin-bottom: 20px; text-align: left;">
                <label for="input-contrasena-verificacion" style="display: block; margin-bottom: 8px; color: #666; font-size: 14px;">Contraseña:</label>
                <input type="password" id="input-contrasena-verificacion" placeholder="Ingrese su contraseña" 
                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;"
                       onkeypress="if(event.key === 'Enter') verificarContrasena();">
            </div>
            
            <div id="mensaje-error-verificacion" style="display: none; background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px;">
                Contraseña incorrecta. Intentos restantes: <span id="intentos-restantes">3</span>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="verificarContrasena()" class="btn btn-primary" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-check"></i> Verificar
                </button>
                <button onclick="cerrarModalVerificacion()" class="btn btn-secondary" style="padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Actividad Extra (cronómetro) -->
    <div id="modal-actividad-extra" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10001; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; max-width: 400px;">
            <i class="fas fa-stopwatch" style="font-size: 48px; color: #6610f2; margin-bottom: 20px;"></i>
            <h3 style="margin: 0 0 10px 0; color: #333;">Actividad Extra</h3>
            <p style="margin: 0 0 20px 0; color: #666;">En progreso...</p>
            <div style="font-size: 32px; font-weight: 700; color: #007bff; margin-bottom: 20px;">
                <span id="tiempo-actividad-extra">00:00:00</span>
            </div>
            <button onclick="finalizarActividadExtra()" class="btn btn-primary" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                <i class="fas fa-stop"></i> Finalizar Actividad
            </button>
        </div>
    </div>

    <!-- Modal de Búsqueda de Cliente -->
    <div id="modal-busqueda-cliente" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10003; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; max-width: 500px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #007bff;">
                    <i class="fas fa-search"></i> Buscar Cliente
                </h3>
                <button onclick="cerrarModalBusqueda()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="busqueda-cliente-input" style="display: block; margin-bottom: 8px; color: #666; font-size: 14px;">CC o Celular:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="busqueda-cliente-input" placeholder="Ingrese CC o celular..." 
                           style="flex: 1; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;"
                           onkeypress="if(event.key === 'Enter') buscarClienteDesdeModal();">
                    <button onclick="buscarClienteDesdeModal()" style="padding: 12px 20px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <!-- Resultados de búsqueda -->
            <div id="resultados-busqueda-cliente" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa;">
                <div style="padding: 20px; text-align: center; color: #666;">
                    <i class="fas fa-search"></i>
                    <p>Ingrese CC o celular para buscar</p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/asesor-gestionar.js"></script>
    <script src="assets/js/asesor-tiempos.js"></script>
    <script src="assets/js/hybrid-updater.js"></script>
    
    <script>
        // Función para abrir/cerrar modal de tiempo
        function toggleTiempoModal() {
            const modalTiempo = document.getElementById('modal-tiempo-sesion');
            const modalPausa = document.getElementById('modal-pausa');
            
            // Si está en pausa, mostrar el modal de pausa en vez del de tiempo
            if (window.asesorTiemposGlobal && window.asesorTiemposGlobal.estaPausado) {
                if (modalPausa) {
                    modalPausa.style.display = 'flex';
                }
                // No abrir el modal de tiempo si está en pausa
                return;
            }
            
            // Si no está en pausa, mostrar el modal de tiempo normal
            if (modalTiempo) {
                modalTiempo.style.display = modalTiempo.style.display === 'none' ? 'flex' : 'none';
            }
        }
        
        // Funciones globales para los botones de pausa
        function iniciarPausaBreak() {
            if (window.asesorTiempos) {
                window.asesorTiempos.iniciarPausa('break');
            }
        }
        
        function iniciarPausaAlmuerzo() {
            if (window.asesorTiempos) {
                window.asesorTiempos.iniciarPausa('almuerzo');
            }
        }
        
        function finalizarPausa() {
            if (window.asesorTiempos) {
                window.asesorTiempos.finalizarPausa();
            }
        }
        
        // Variables para la verificación de contraseña
        let intentosVerificacion = 3;
        
        function mostrarModalVerificacion() {
            const modal = document.getElementById('modal-verificacion-contrasena');
            if (modal) {
                modal.style.display = 'flex';
                document.getElementById('input-contrasena-verificacion').value = '';
                document.getElementById('mensaje-error-verificacion').style.display = 'none';
                intentosVerificacion = 3;
                document.getElementById('intentos-restantes').textContent = '3';
            }
        }
        
        function cerrarModalVerificacion() {
            const modal = document.getElementById('modal-verificacion-contrasena');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        async function verificarContrasena() {
            const contrasena = document.getElementById('input-contrasena-verificacion').value;
            const mensajeError = document.getElementById('mensaje-error-verificacion');
            const intentosRestantes = document.getElementById('intentos-restantes');
            
            if (!contrasena) {
                alert('Por favor ingrese su contraseña');
                return;
            }
            
            try {
                const response = await fetch('index.php?action=verificar_contrasena', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        contrasena: contrasena
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Contraseña correcta, cerrar modal de verificación
                    cerrarModalVerificacion();
                    
                    // Finalizar la pausa
                    if (window.asesorTiempos) {
                        window.asesorTiempos.finalizarPausa();
                    }
                    
                    intentosVerificacion = 3;
                } else {
                    // Contraseña incorrecta
                    intentosVerificacion--;
                    
                    if (intentosVerificacion > 0) {
                        mensajeError.style.display = 'block';
                        intentosRestantes.textContent = intentosVerificacion;
                        document.getElementById('input-contrasena-verificacion').value = '';
                    } else {
                        alert('Demasiados intentos fallidos. La cuenta será bloqueada temporalmente por seguridad.');
                        window.location.href = 'index.php?action=logout';
                    }
                }
            } catch (error) {
                console.error('Error al verificar contraseña:', error);
                alert('Error al verificar la contraseña. Por favor intente nuevamente.');
            }
        }
        
        function iniciarPausaBano() {
            if (window.asesorTiempos) {
                window.asesorTiempos.iniciarPausa('bano');
            }
        }
        
        function iniciarPausaMantenimiento() {
            if (window.asesorTiempos) {
                window.asesorTiempos.iniciarPausa('mantenimiento');
            }
        }
        
        function iniciarPausaActiva() {
            if (window.asesorTiempos) {
                window.asesorTiempos.iniciarPausa('pausa_activa');
            }
        }
        
        function iniciarActividadExtra() {
            if (window.asesorTiempos) {
                window.asesorTiempos.iniciarActividadExtra();
            }
        }
        
        function finalizarActividadExtra() {
            if (window.asesorTiempos) {
                window.asesorTiempos.finalizarActividadExtra();
            }
        }
        
        // Funciones para los nuevos botones después de guardar gestión
        function mostrarBotonesDespuesGuardar() {
            document.getElementById('botones-iniciales').style.display = 'none';
            document.getElementById('botones-despues-guardar').style.display = 'flex';
            
            // Verificar si hay siguiente cliente disponible
            verificarSiguienteCliente();
        }
        
        async function verificarSiguienteCliente() {
            try {
                const response = await fetch('index.php?action=obtener_siguiente_cliente', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                const btnSiguienteCliente = document.getElementById('btn-siguiente-cliente');
                
                if (data.success && data.cliente) {
                    btnSiguienteCliente.style.display = 'inline-block';
                    btnSiguienteCliente.title = `Siguiente: ${data.cliente['NOMBRE CONTRATANTE']}`;
                } else {
                    btnSiguienteCliente.style.display = 'none';
                }
                
            } catch (error) {
                console.error('Error al verificar siguiente cliente:', error);
                document.getElementById('btn-siguiente-cliente').style.display = 'none';
            }
        }
        
        async function irSiguienteCliente() {
            try {
                const response = await fetch('index.php?action=obtener_siguiente_cliente', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.cliente) {
                    // Redirigir al siguiente cliente
                    window.location.href = `index.php?action=asesor_gestionar&cliente_id=${data.cliente.ID_CLIENTE}`;
                } else {
                    alert('No hay más clientes pendientes por gestionar');
                }
                
            } catch (error) {
                console.error('Error al obtener siguiente cliente:', error);
                alert('Error al obtener el siguiente cliente');
            }
        }
        
        function mostrarBusquedaCliente() {
            const modal = document.getElementById('modal-busqueda-cliente');
            if (modal) {
                modal.style.display = 'flex';
                document.getElementById('busqueda-cliente-input').value = '';
                document.getElementById('resultados-busqueda-cliente').innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #666;">
                        <i class="fas fa-search"></i>
                        <p>Ingrese CC o celular para buscar</p>
                    </div>
                `;
            }
        }
        
        function cerrarModalBusqueda() {
            const modal = document.getElementById('modal-busqueda-cliente');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        async function buscarClienteDesdeModal() {
            const termino = document.getElementById('busqueda-cliente-input').value.trim();
            const resultadosDiv = document.getElementById('resultados-busqueda-cliente');
            
            if (!termino) {
                alert('Por favor ingrese CC o celular');
                return;
            }
            
            // Mostrar loading
            resultadosDiv.innerHTML = `
                <div style="padding: 20px; text-align: center; color: #666;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Buscando cliente...</p>
                </div>
            `;
            
            try {
                const response = await fetch('index.php?action=buscar_cliente_asesor', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        termino: termino,
                        criterio: 'mixto'
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.clientes && data.clientes.length > 0) {
                    let html = '';
                    data.clientes.forEach(comercio => {
                        const comercioId = comercio.ID_COMERCIO || comercio.id || comercio.ID_CLIENTE;
                        const nombreCliente = comercio.nombre || comercio['NOMBRE CONTRATANTE'] || comercio.NOMBRE_CLIENTE || 'N/A';
                        const cc = comercio.cc || comercio.IDENTIFICACION || 'N/A';
                        const celular = comercio.CEL || comercio['TEL 1'] || comercio.cel || 'N/A';
                        
                        html += `
                            <div style="padding: 15px; border-bottom: 1px solid #dee2e6; cursor: pointer;" 
                                 onclick="gestionarClienteDesdeModal('${comercioId}')">
                                <div style="font-weight: 600; color: #333; margin-bottom: 5px;">
                                    ${nombreCliente}
                                </div>
                                <div style="font-size: 13px; color: #666;">
                                    <div>CC: ${cc}</div>
                                    <div>Celular: ${celular}</div>
                                </div>
                            </div>
                        `;
                    });
                    resultadosDiv.innerHTML = html;
                } else {
                    resultadosDiv.innerHTML = `
                        <div style="padding: 20px; text-align: center; color: #dc3545;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>No se encontraron clientes</p>
                            <small>Verifique el CC o celular ingresado</small>
                        </div>
                    `;
                }
                
            } catch (error) {
                console.error('Error al buscar cliente:', error);
                resultadosDiv.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al buscar cliente</p>
                        <small>Intente nuevamente</small>
                    </div>
                `;
            }
        }
        
        function gestionarClienteDesdeModal(clienteId) {
            cerrarModalBusqueda();
            window.location.href = `index.php?action=asesor_gestionar&cliente_id=${clienteId}`;
        }
        
        function volverClientes() {
            window.location.href = 'index.php?action=asesor_dashboard#tab-clientes';
        }
        
        // Función global para ser llamada desde asesor-gestionar.js después de guardar
        window.mostrarBotonesDespuesGuardar = mostrarBotonesDespuesGuardar;
    </script>

    <!-- WebRTC Softphone Integration -->
    <?php
    if ($mostrar_softphone):
        // Incluir configuración WebRTC
        require_once 'config/asterisk.php';
        $webrtc_config = getWebRTCConfig();
        
        // Usar datos de sesión directamente (ya están cargados en AuthController)
        $extension = $_SESSION['usuario_extension'] ?? '';
        $sip_password = $_SESSION['usuario_sip_password'] ?? '';
    ?>
    <script src="assets/js/sip.min.js"></script>
    <script src="assets/js/softphone-web.js"></script>
    <script>
        // Configuración del softphone
        const webrtcConfig = {
            wss_server: '<?php echo $webrtc_config['wss_server']; ?>',
            sip_domain: '<?php echo $webrtc_config['sip_domain']; ?>',
            extension: '<?php echo htmlspecialchars($extension, ENT_QUOTES, 'UTF-8'); ?>',
            password: '<?php echo htmlspecialchars($sip_password, ENT_QUOTES, 'UTF-8'); ?>',
            display_name: '<?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Asesor', ENT_QUOTES, 'UTF-8'); ?>',
            preferredRtpPort: <?php echo (int) ($webrtc_config['preferred_rtp_port'] ?? 10000); ?>,
            iceServers: <?php 
                $iceServers = $webrtc_config['iceServers'] ?? [];
                if (!is_array($iceServers) || empty($iceServers)) {
                    $iceServers = [];
                }
                echo json_encode($iceServers, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            ?>,
            debug_mode: <?php echo $webrtc_config['debug_mode'] ? 'true' : 'false'; ?>
        };
        
        // DEBUG: Mostrar configuración en consola ANTES de inicializar
        console.log('🔍 [DEBUG] Configuración del softphone (ANTES de validar):');
        console.log('  - Extension:', webrtcConfig.extension || 'VACIA');
        console.log('  - Password:', webrtcConfig.password ? 'DEFINIDA (' + webrtcConfig.password.length + ' caracteres)' : 'VACIA');
        console.log('  - WSS Server:', webrtcConfig.wss_server || 'VACIO');
        console.log('  - SIP Domain:', webrtcConfig.sip_domain || 'VACIO');
        console.log('  - Debug Mode:', webrtcConfig.debug_mode);
        
        // Verificar que los valores críticos no estén vacíos
        if (!webrtcConfig.extension || webrtcConfig.extension.trim() === '') {
            console.error('❌ [ERROR CRÍTICO] La extensión está vacía. Verifica la base de datos.');
        }
        if (!webrtcConfig.password || webrtcConfig.password.trim() === '') {
            console.error('❌ [ERROR CRÍTICO] La contraseña SIP está vacía. Verifica la base de datos.');
        }
        if (!webrtcConfig.wss_server || webrtcConfig.wss_server.trim() === '') {
            console.error('❌ [ERROR CRÍTICO] El servidor WSS está vacío. Verifica config/asterisk.php');
        }
        if (!webrtcConfig.sip_domain || webrtcConfig.sip_domain.trim() === '') {
            console.error('❌ [ERROR CRÍTICO] El dominio SIP está vacío. Verifica config/asterisk.php');
        }

        // Esperar a que TANTO SIP.js COMO softphone-web.js estén cargados
        function inicializarSoftphoneConVerificacion() {
            let intentos = 0;
            const maxIntentos = 100;
            
            const intervalo = setInterval(function() {
                intentos++;
                
                // Verificar que TODO esté listo
                const sipjsListo = typeof SIP !== 'undefined' && 
                                  typeof SIP.UserAgent !== 'undefined';
                
                const softphoneListo = typeof WebRTCSoftphone !== 'undefined';
                
                if (sipjsListo && softphoneListo) {
                    clearInterval(intervalo);
                    console.log('✅ Todos los componentes listos, inicializando softphone...');
                    
                        try {
                            // Verificar que el contenedor existe
                            const container = document.getElementById('webrtc-softphone');
                            if (!container) {
                                console.warn('⚠️ [WebRTC Softphone] Contenedor del softphone no encontrado. El usuario puede no tener extensión asignada.');
                                return;
                            }
                            
                            // Verificar configuración antes de inicializar
                            console.log('🔄 [WebRTC Softphone] Inicializando softphone...');
                            console.log('📝 [WebRTC Softphone] Verificando configuración:', {
                                extension: webrtcConfig.extension || 'VACIA',
                                password: webrtcConfig.password ? 'DEFINIDA' : 'VACIA',
                                wss_server: webrtcConfig.wss_server,
                                sip_domain: webrtcConfig.sip_domain,
                                debug_mode: webrtcConfig.debug_mode
                            });
                            
                            // Validar que la extensión y password no estén vacías
                            if (!webrtcConfig.extension || webrtcConfig.extension.trim() === '') {
                                console.error('❌ [WebRTC Softphone] Error: Extension está vacía');
                                alert('Error: La extensión SIP no está configurada. Contacta al administrador.');
                                return;
                            }
                            
                            if (!webrtcConfig.password || webrtcConfig.password.trim() === '') {
                                console.error('❌ [WebRTC Softphone] Error: Password está vacía');
                                alert('Error: La contraseña SIP no está configurada. Contacta al administrador.');
                                return;
                            }
                            
                            window.webrtcSoftphone = new WebRTCSoftphone(webrtcConfig);
                            console.log('✅ [WebRTC Softphone] Softphone WebRTC inicializado correctamente');
                            console.log('📞 [WebRTC Softphone] Extensión:', webrtcConfig.extension);
                            
                            // Función para verificar estado (útil para debugging)
                            window.verificarEstadoSoftphone = function() {
                                if (window.webrtcSoftphone) {
                                    console.log('📊 [WebRTC Softphone] Estado actual:', {
                                        extension: window.webrtcSoftphone.config.extension,
                                        sip_domain: window.webrtcSoftphone.config.sip_domain,
                                        wss_server: window.webrtcSoftphone.config.wss_server,
                                        isRegistered: window.webrtcSoftphone.isRegistered,
                                        isConnected: window.webrtcSoftphone.isConnected,
                                        status: window.webrtcSoftphone.status,
                                        transportState: window.webrtcSoftphone.userAgent?.transport?.state,
                                        registrationState: window.webrtcSoftphone.userAgent?.registration?.state
                                    });
                                } else {
                                    console.warn('⚠️ [WebRTC Softphone] El softphone no está inicializado');
                                }
                            };
                            
                            console.log('💡 [WebRTC Softphone] Tip: Ejecuta verificarEstadoSoftphone() en la consola para ver el estado actual');
                            
                        } catch (error) {
                            console.error('❌ [WebRTC Softphone] Error al inicializar softphone:', error);
                            console.error('❌ [WebRTC Softphone] Stack:', error.stack);
                            if (webrtcConfig.debug_mode) {
                                alert('Error al inicializar el softphone: ' + error.message);
                            }
                        }
                    
                } else {
                    if (intentos % 10 === 0) {
                        console.log(`⏳ Esperando componentes... (${intentos}/${maxIntentos})`);
                        console.log('  SIP.js listo:', sipjsListo);
                        console.log('  WebRTCSoftphone listo:', softphoneListo);
                    }
                    
                    if (intentos >= maxIntentos) {
                        clearInterval(intervalo);
                        console.error('❌ Timeout esperando componentes del softphone');
                        if (webrtcConfig.debug_mode) {
                            alert('El softphone no se pudo inicializar. Por favor, recarga la página.');
                        }
                    }
                }
            }, 100);
        }

        // Iniciar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarSoftphoneConVerificacion);
        } else {
            inicializarSoftphoneConVerificacion();
        }
        
        // Función global para llamar desde click-to-call
        function llamarDesdeWebRTC(numero) {
            if (typeof window.webrtcSoftphone !== 'undefined' && 
                window.webrtcSoftphone !== null && 
                window.webrtcSoftphone.callNumber) {
                window.webrtcSoftphone.callNumber(numero);
            } else {
                console.warn('Softphone no disponible. Por favor, espera a que se inicialice.');
            }
        }
    </script>
    <style>
    /* Estilos básicos para el softphone inline */
    .seccion-softphone-wrapper {
        background: white;
        border-radius: 8px;
        padding: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
        margin-bottom: 20px;
        overflow: hidden;
        max-width: 100%;
    }
    
    .webrtc-softphone-panel.inline {
        position: relative !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
    }
    
    .webrtc-softphone-panel.inline.hidden {
        display: none !important;
    }
    
    .webrtc-softphone-panel.inline .softphone-header {
        background: #007bff;
        color: white;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 8px 8px 0 0;
    }
    
    .webrtc-softphone-panel.inline .softphone-header h3 {
        margin: 0;
        color: white;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .webrtc-softphone-panel.inline .softphone-body {
        padding: 15px;
        background: white;
    }
    
    .webrtc-softphone-panel.inline .softphone-status {
        margin-bottom: 15px;
        text-align: center;
    }
    
    .webrtc-softphone-panel.inline .status-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 13px;
    }
    
    .webrtc-softphone-panel.inline .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .webrtc-softphone-panel.inline .status-dot.connected {
        background: #28a745;
    }
    
    .webrtc-softphone-panel.inline .status-dot.disconnected {
        background: #dc3545;
    }
    
    .webrtc-softphone-panel.inline .status-dot.connecting {
        background: #ffc107;
        animation: pulse 1.5s infinite;
    }
    
    .webrtc-softphone-panel.inline .status-dot.in-call {
        background: #007bff;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .webrtc-softphone-panel.inline .number-display {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        padding: 12px;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        min-height: 30px;
    }
    
    .webrtc-softphone-panel.inline .dialpad {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .webrtc-softphone-panel.inline .dialpad-btn {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        padding: 15px 5px;
        font-size: 18px;
        font-weight: 600;
        color: #333;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 50px;
    }
    
    .webrtc-softphone-panel.inline .dialpad-btn:hover {
        background: #f8f9fa;
        border-color: #007bff;
    }
    
    .webrtc-softphone-panel.inline .dialpad-btn-letter {
        font-size: 10px;
        color: #666;
        margin-top: 2px;
    }
    
    .webrtc-softphone-panel.inline .action-buttons {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .webrtc-softphone-panel.inline .action-btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }
    
    .webrtc-softphone-panel.inline .delete-btn {
        background: #dc3545;
        color: white;
    }
    
    .webrtc-softphone-panel.inline .delete-btn:hover {
        background: #c82333;
    }
    
    .webrtc-softphone-panel.inline .call-btn {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }
    
    .webrtc-softphone-panel.inline .call-btn:hover {
        background: linear-gradient(135deg, #218838, #1ea080);
    }
    
    .webrtc-softphone-panel.inline .hangup-btn {
        background: #dc3545;
        color: white;
    }
    
    .webrtc-softphone-panel.inline .hangup-btn:hover {
        background: #c82333;
    }
    
    .webrtc-softphone-panel.inline .call-info {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 15px;
        text-align: center;
        display: none;
    }
    
    .webrtc-softphone-panel.inline .call-info.active {
        display: block;
    }
    
    .webrtc-softphone-panel.inline .call-controls {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .webrtc-softphone-panel.inline .control-btn {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        padding: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #333;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }
    
    .webrtc-softphone-panel.inline .control-btn:hover {
        background: #f8f9fa;
        border-color: #007bff;
    }
    
    .webrtc-softphone-panel.inline .control-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    .webrtc-softphone-panel.inline .conference-btn {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        border-color: #17a2b8;
    }
    
    .webrtc-softphone-panel.inline .conference-btn:hover {
        background: linear-gradient(135deg, #138496, #117a8b);
        border-color: #138496;
    }
    
    .webrtc-softphone-panel.inline .transfer-btn {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #333;
        border-color: #ffc107;
    }
    
    .webrtc-softphone-panel.inline .transfer-btn:hover {
        background: linear-gradient(135deg, #e0a800, #d39e00);
        border-color: #e0a800;
    }
    
    /* Estilos para modales */
    .softphone-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 100000;
        align-items: center;
        justify-content: center;
    }
    
    .softphone-modal .modal-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        max-width: 400px;
        width: 90%;
        max-height: 90vh;
        overflow: auto;
    }
    
    .softphone-modal .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .softphone-modal .modal-header h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .softphone-modal .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #666;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    
    .softphone-modal .modal-close:hover {
        background: #f8f9fa;
        color: #333;
    }
    
    .softphone-modal .modal-body {
        padding: 20px;
    }
    
    .softphone-modal .modal-body p {
        margin: 0 0 15px 0;
        color: #666;
        font-size: 14px;
    }
    
    .softphone-modal .modal-input {
        width: 100%;
        padding: 12px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        font-size: 16px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }
    
    .softphone-modal .modal-input:focus {
        outline: none;
        border-color: #007bff;
    }
    
    .softphone-modal .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .softphone-modal .modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    
    .softphone-modal .modal-btn-primary {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }
    
    .softphone-modal .modal-btn-primary:hover {
        background: linear-gradient(135deg, #218838, #1ea080);
    }
    
    .softphone-modal .modal-btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .softphone-modal .modal-btn-secondary:hover {
        background: #5a6268;
    }
    </style>
    <?php endif; ?>

    <!-- Modal de Judicializado -->
    <div id="modal-judicializado" class="modal-judicializado" style="display: none;">
        <div class="modal-judicializado-content">
            <div class="modal-judicializado-header">
                <h3>
                    <i class="fas fa-gavel"></i> Consulta de Expedientes Judiciales
                </h3>
                <button onclick="cerrarModalJudicializado()" class="modal-judicializado-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-judicializado-body">
                <div class="judicial-buttons-grid">
                    <button onclick="showMaintenanceJudicial('Demandante')" class="judicial-btn">
                        Demandante
                    </button>
                    <button onclick="showMaintenanceJudicial('Cedula - NIT')" class="judicial-btn">
                        Cedula - NIT
                    </button>
                    <button onclick="showMaintenanceJudicial('Persona natural')" class="judicial-btn">
                        Persona natural
                    </button>
                    <button onclick="showMaintenanceJudicial('Persona juridica')" class="judicial-btn">
                        Persona juridica
                    </button>
                    <button onclick="showMaintenanceJudicial('Ciudad')" class="judicial-btn">
                        Ciudad
                    </button>
                    <button onclick="showMaintenanceJudicial('Juzgado Origen')" class="judicial-btn">
                        Juzgado Origen
                    </button>
                    <button onclick="showMaintenanceJudicial('Juzgado actual')" class="judicial-btn">
                        Juzgado actual
                    </button>
                    <button onclick="showMaintenanceJudicial('Radicado')" class="judicial-btn">
                        Radicado
                    </button>
                    <button onclick="toggleJudicialEtapaButtons()" class="judicial-btn">
                        Etapa actual
                    </button>
                    <button onclick="toggleJudicialMedidaCautelarButtons()" class="judicial-btn">
                        Medida cautelar
                    </button>
                    <button onclick="showMaintenanceJudicial('Alerta317')" class="judicial-btn">
                        Alerta 317
                    </button>
                    <button onclick="showDocumentUploadModalJudicial()" class="judicial-btn">
                        Cargue de documentos
                    </button>
                    <button onclick="showAbogadoModalJudicial()" class="judicial-btn">
                        Abogado Actual
                    </button>
                    <button onclick="showObservacionesModalJudicial()" class="judicial-btn">
                        Observaciones
                    </button>
                </div>
                
                <!-- Contenedor para los botones de etapa actual -->
                <div id="judicial-etapa-buttons-container" class="judicial-sub-buttons-container" style="display: none;">
                    <button onclick="showFechaEtapaButtonJudicial('Notificación')" class="judicial-sub-btn">
                        Notificación
                    </button>
                    <button onclick="showFechaEtapaButtonJudicial('Mandamiento')" class="judicial-sub-btn">
                        Mandamiento
                    </button>
                    <button onclick="showFechaEtapaButtonJudicial('Sentencia')" class="judicial-sub-btn">
                        Sentencia
                    </button>
                    <button onclick="showFechaEtapaButtonJudicial('Avalúo')" class="judicial-sub-btn">
                        Avalúo
                    </button>
                    <button onclick="showFechaEtapaButtonJudicial('Remate')" class="judicial-sub-btn">
                        Remate
                    </button>
                </div>
                
                <!-- Botón de Fecha de Etapa -->
                <div id="judicial-fecha-etapa-container" class="judicial-fecha-etapa-container" style="display: none;">
                    <button onclick="showFechaEtapaModalJudicial()" class="judicial-fecha-etapa-btn">
                        <i class="fas fa-calendar-alt"></i> Fecha de etapa
                    </button>
                    <span id="judicial-etapa-seleccionada" class="judicial-etapa-seleccionada"></span>
                </div>
                
                <!-- Contenedor para los botones de medida cautelar -->
                <div id="judicial-medida-cautelar-container" class="judicial-sub-buttons-container" style="display: none;">
                    <button onclick="toggleJudicialEfectivaButtons()" class="judicial-sub-btn">
                        Efectiva
                    </button>
                    <button onclick="toggleJudicialNoEfectivaButtons()" class="judicial-sub-btn">
                        No efectiva
                    </button>
                </div>
                
                <!-- Contenedor para los botones de Efectiva -->
                <div id="judicial-efectiva-container" class="judicial-sub-buttons-container" style="display: none;">
                    <button onclick="showMaintenanceJudicial('Inmueble')" class="judicial-sub-btn">
                        Inmueble
                    </button>
                    <button onclick="showMaintenanceJudicial('Vehículo')" class="judicial-sub-btn">
                        Vehículo
                    </button>
                    <button onclick="showMaintenanceJudicial('Cuentas Bancarias')" class="judicial-sub-btn">
                        Cuentas Bancarias
                    </button>
                    <button onclick="showMaintenanceJudicial('Salario')" class="judicial-sub-btn">
                        Salario
                    </button>
                </div>
                
                <!-- Contenedor para los botones de No Efectiva -->
                <div id="judicial-no-efectiva-container" class="judicial-sub-buttons-container" style="display: none;">
                    <button onclick="showMaintenanceJudicial('Pendiente Oficios')" class="judicial-sub-btn">
                        Pendiente Oficios
                    </button>
                    <button onclick="showMaintenanceJudicial('Pendiente Fecha de Secuestro')" class="judicial-sub-btn">
                        Pendiente Fecha de Secuestro
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Fecha de Etapa Judicial -->
    <div id="judicial-fecha-etapa-modal" class="modal-judicializado" style="display: none;">
        <div class="modal-judicializado-content modal-small">
            <div class="modal-judicializado-header">
                <h3>Fecha de Etapa</h3>
                <button onclick="hideFechaEtapaModalJudicial()" class="modal-judicializado-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-judicializado-body">
                <form id="judicial-fecha-etapa-form" onsubmit="event.preventDefault(); guardarFechaEtapaJudicial();">
                    <div class="form-group">
                        <label>Etapa Seleccionada</label>
                        <p id="judicial-etapa-modal-nombre" class="judicial-etapa-nombre-display"></p>
                    </div>
                    <div class="form-group">
                        <label for="judicial-fecha-etapa-input">Fecha de la Etapa</label>
                        <input type="date" id="judicial-fecha-etapa-input" class="form-control" required>
                    </div>
                    <div class="modal-judicializado-actions">
                        <button type="button" onclick="hideFechaEtapaModalJudicial()" class="btn-action btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-action btn-primary">
                            Guardar Fecha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function guardarFechaEtapaJudicial() {
            const fechaInput = document.getElementById('judicial-fecha-etapa-input');
            const fecha = fechaInput ? fechaInput.value : '';
            const etapa = etapaSeleccionadaJudicial || 'Etapa no seleccionada';
            
            if (fecha) {
                const fechaFormateada = new Date(fecha).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                alert(`Fecha guardada exitosamente:\nEtapa: ${etapa}\nFecha: ${fechaFormateada}`);
                hideFechaEtapaModalJudicial();
                ocultarFechaEtapaJudicial();
            } else {
                alert('Por favor, seleccione una fecha');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Cerrar modal al hacer clic fuera
            document.addEventListener('click', function(event) {
                const modal = document.getElementById('modal-judicializado');
                if (modal && event.target === modal) {
                    cerrarModalJudicializado();
                }
                
                const fechaModal = document.getElementById('judicial-fecha-etapa-modal');
                if (fechaModal && event.target === fechaModal) {
                    hideFechaEtapaModalJudicial();
                }
                
                const documentModal = document.getElementById('judicial-document-upload-modal');
                if (documentModal && event.target === documentModal) {
                    hideDocumentUploadModalJudicial();
                }
                
                const abogadoModal = document.getElementById('judicial-abogado-modal');
                if (abogadoModal && event.target === abogadoModal) {
                    hideAbogadoModalJudicial();
                }
                
                const observacionesModal = document.getElementById('judicial-observaciones-modal');
                if (observacionesModal && event.target === observacionesModal) {
                    hideObservacionesModalJudicial();
                }
            });
            
            // Manejo del formulario de carga de documentos
            const documentUploadForm = document.getElementById('judicial-document-upload-form');
            if (documentUploadForm) {
                documentUploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    alert('Documento cargado exitosamente');
                    hideDocumentUploadModalJudicial();
                });
            }
            
            // Mostrar nombre del archivo seleccionado
            const fileInput = document.getElementById('judicial-file-input');
            const fileName = document.getElementById('judicial-file-name');
            if (fileInput && fileName) {
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        fileName.textContent = 'Archivo seleccionado: ' + e.target.files[0].name;
                        fileName.classList.remove('hidden');
                    } else {
                        fileName.classList.add('hidden');
                    }
                });
            }
            
            // Contador de caracteres para observaciones
            const observacionesText = document.getElementById('judicial-observaciones-text');
            const charCount = document.getElementById('judicial-char-count');
            if (observacionesText && charCount) {
                observacionesText.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
            
            // Manejo del formulario de observaciones
            const observacionesForm = document.getElementById('judicial-observaciones-form');
            if (observacionesForm) {
                observacionesForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    alert('Observaciones guardadas exitosamente');
                    hideObservacionesModalJudicial();
                });
            }

            // Mock de WhatsApp (simulación de envío)
            initWhatsAppMock();
        });

    function initWhatsAppMock() {
        const form = document.getElementById('whatsapp-mock-form');
        const input = document.getElementById('whatsapp-mock-input');
        const messages = document.getElementById('whatsapp-mock-messages');
        const sendBtn = document.getElementById('whatsapp-mock-send');
        if (!form || !input || !messages || !sendBtn) return;

        const updateSendIcon = () => {
            const hasText = (input.value || '').trim().length > 0;
            if (sendBtn) {
                sendBtn.innerHTML = hasText ? '<i class="fas fa-paper-plane"></i>' : '<i class="fas fa-microphone"></i>';
            }
        };

        input.addEventListener('input', updateSendIcon);
        updateSendIcon();

        const sendMessage = () => {
            const text = (input.value || '').trim();
            if (!text) return;
            const bubble = document.createElement('div');
            bubble.className = 'bubble outgoing';
            bubble.textContent = text;
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;
            input.value = '';
            updateSendIcon();
        };

        // Evitar cualquier submit que recargue la vista
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        // Click en el botón (type="button") para enviar
        sendBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    </script>
    
    <!-- Modal de Cargue de Documentos Judicial -->
    <div id="judicial-document-upload-modal" class="modal-judicializado" style="display: none;">
        <div class="modal-judicializado-content modal-medium">
            <div class="modal-judicializado-header">
                <h3>
                    <i class="fas fa-file-upload"></i> Cargue de Documentos
                </h3>
                <button onclick="hideDocumentUploadModalJudicial()" class="modal-judicializado-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-judicializado-body">
                <form id="judicial-document-upload-form" class="judicial-form">
                    <div class="form-group">
                        <label for="judicial-document-type">Tipo de Documento</label>
                        <select id="judicial-document-type" class="form-control" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="demanda">Demanda</option>
                            <option value="sentencia">Sentencia</option>
                            <option value="auto">Auto</option>
                            <option value="oficio">Oficio</option>
                            <option value="notificacion">Notificación</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="judicial-document-description">Descripción</label>
                        <input type="text" id="judicial-document-description" class="form-control" placeholder="Ingrese una descripción del documento">
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Archivo</label>
                        <div class="judicial-file-upload-area">
                            <input type="file" id="judicial-file-input" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <label for="judicial-file-input" class="judicial-file-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Haga clic para seleccionar o arrastre el archivo aquí</p>
                                <small>PDF, DOC, DOCX, JPG, PNG (máx. 10MB)</small>
                            </label>
                        </div>
                        <p id="judicial-file-name" class="judicial-file-name hidden"></p>
                    </div>
                    <div class="modal-judicializado-actions">
                        <button type="button" onclick="hideDocumentUploadModalJudicial()" class="btn-action btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-action btn-primary">
                            <i class="fas fa-upload"></i> Cargar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal de Abogado Actual Judicial -->
    <div id="judicial-abogado-modal" class="modal-judicializado" style="display: none;">
        <div class="modal-judicializado-content modal-medium">
            <div class="modal-judicializado-header">
                <h3>
                    <i class="fas fa-user-tie"></i> Abogado Actual
                </h3>
                <button onclick="hideAbogadoModalJudicial()" class="modal-judicializado-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-judicializado-body">
                <div class="judicial-abogado-info">
                    <div class="judicial-abogado-header">
                        <div class="judicial-abogado-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="judicial-abogado-details">
                            <h4>Dr. Carlos Mendoza</h4>
                            <p class="judicial-abogado-specialty">Abogado Especialista en Derecho Civil</p>
                            <p class="judicial-abogado-card">Tarjeta Profesional: 123456</p>
                        </div>
                    </div>
                    
                    <div class="judicial-abogado-section">
                        <h5>
                            <i class="fas fa-address-card"></i> Información de Contacto
                        </h5>
                        <div class="judicial-contact-list">
                            <div class="judicial-contact-item">
                                <i class="fas fa-envelope"></i>
                                <span>carlos.mendoza@speedwayia.com</span>
                            </div>
                            <div class="judicial-contact-item">
                                <i class="fas fa-phone"></i>
                                <span>+57 300 123 4567</span>
                            </div>
                            <div class="judicial-contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Bogotá, Colombia</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="judicial-abogado-section">
                        <h5>
                            <i class="fas fa-briefcase"></i> Especialidades
                        </h5>
                        <div class="judicial-specialties">
                            <span class="judicial-specialty-badge">Derecho Civil</span>
                            <span class="judicial-specialty-badge">Derecho Comercial</span>
                            <span class="judicial-specialty-badge">Derecho Laboral</span>
                        </div>
                    </div>
                    
                    <div class="modal-judicializado-actions">
                        <button onclick="hideAbogadoModalJudicial()" class="btn-action btn-primary">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Observaciones Judicial -->
    <div id="judicial-observaciones-modal" class="modal-judicializado" style="display: none;">
        <div class="modal-judicializado-content modal-medium">
            <div class="modal-judicializado-header">
                <h3>
                    <i class="fas fa-comment-dots"></i> Observaciones
                </h3>
                <button onclick="hideObservacionesModalJudicial()" class="modal-judicializado-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-judicializado-body">
                <form id="judicial-observaciones-form" class="judicial-form">
                    <div class="form-group">
                        <label for="judicial-observaciones-fecha">Fecha</label>
                        <input type="date" id="judicial-observaciones-fecha" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="judicial-observaciones-text">Observaciones</label>
                        <textarea id="judicial-observaciones-text" rows="8" class="form-control" placeholder="Ingrese sus observaciones aquí..." required></textarea>
                        <p class="judicial-char-count-info">
                            Caracteres: <span id="judicial-char-count">0</span>
                        </p>
                    </div>
                    <div class="modal-judicializado-actions">
                        <button type="button" onclick="hideObservacionesModalJudicial()" class="btn-action btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-action btn-primary">
                            <i class="fas fa-save"></i> Guardar Observaciones
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
