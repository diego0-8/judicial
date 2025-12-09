<?php
/**
 * Softphone WebRTC Standalone
 * Vista independiente del softphone que puede cargarse en iframe
 * para evitar desconexiones al recargar la página principal
 */

require_once '../config.php';
require_once '../utils/auth.php';

// Verificar autenticación
requireAuth();

// Verificar que el usuario sea asesor y tenga extensión
require_once '../models/Usuario.php';
$usuario_model = new Usuario();
$usuario_data = $usuario_model->obtenerPorCedula($_SESSION['usuario_id'] ?? '');

$mostrar_softphone = (
    isset($_SESSION['usuario_rol']) && 
    $_SESSION['usuario_rol'] === 'asesor' &&
    $usuario_data &&
    !empty($usuario_data['extension'] ?? '') &&
    !empty($usuario_data['sip_password'] ?? '')
);

if (!$mostrar_softphone) {
    die('No tienes permisos para acceder al softphone.');
}

require_once '../config/asterisk.php';
$webrtc_config = getWebRTCConfig();
$extension = $_SESSION['usuario_extension'] ?? '';
$sip_password = $_SESSION['usuario_sip_password'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Softphone WebRTC - APEX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/webrtc-softphone.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        
        .softphone-container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .webrtc-softphone-panel {
            width: 100%;
            height: 100%;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="softphone-container">
        <div id="webrtc-softphone" class="webrtc-softphone-panel"></div>
    </div>

    <script src="../assets/js/sip.min.js"></script>
    <script src="../assets/js/softphone-web.js"></script>
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

        // Inicializar softphone cuando esté listo
        function inicializarSoftphone() {
            let intentos = 0;
            const maxIntentos = 100;
            
            const intervalo = setInterval(function() {
                intentos++;
                
                const sipjsListo = typeof SIP !== 'undefined' && typeof SIP.UserAgent !== 'undefined';
                const softphoneListo = typeof WebRTCSoftphone !== 'undefined';
                
                if (sipjsListo && softphoneListo) {
                    clearInterval(intervalo);
                    
                    try {
                        const container = document.getElementById('webrtc-softphone');
                        if (!container) {
                            console.error('❌ [Softphone Standalone] Contenedor no encontrado');
                            return;
                        }
                        
                        // Inicializar softphone
                        window.webrtcSoftphone = new WebRTCSoftphone(webrtcConfig);
                        console.log('✅ [Softphone Standalone] Softphone inicializado correctamente');
                        
                        // Exponer API para comunicación con página principal
                        window.softphoneAPI = {
                            callNumber: (numero) => {
                                if (window.webrtcSoftphone && window.webrtcSoftphone.callNumber) {
                                    window.webrtcSoftphone.callNumber(numero);
                                    return true;
                                }
                                return false;
                            },
                            getStatus: () => {
                                if (window.webrtcSoftphone) {
                                    return {
                                        isConnected: window.webrtcSoftphone.isConnected,
                                        isRegistered: window.webrtcSoftphone.isRegistered,
                                        status: window.webrtcSoftphone.status,
                                        currentCall: window.webrtcSoftphone.currentCall ? {
                                            number: window.webrtcSoftphone.currentNumber,
                                            state: window.webrtcSoftphone.currentCall.state
                                        } : null
                                    };
                                }
                                return null;
                            },
                            hangup: () => {
                                if (window.webrtcSoftphone && window.webrtcSoftphone.hangup) {
                                    window.webrtcSoftphone.hangup();
                                    return true;
                                }
                                return false;
                            }
                        };
                        
                        // Escuchar mensajes de la página principal
                        window.addEventListener('message', function(event) {
                            // Validar origen por seguridad
                            if (event.origin !== window.location.origin) {
                                console.warn('⚠️ [Softphone Standalone] Mensaje de origen no válido:', event.origin);
                                return;
                            }
                            
                            const { type, data } = event.data || {};
                            
                            switch (type) {
                                case 'CALL_NUMBER':
                                    if (window.softphoneAPI && window.softphoneAPI.callNumber) {
                                        const success = window.softphoneAPI.callNumber(data.number);
                                        // Responder con confirmación
                                        event.source.postMessage({
                                            type: 'CALL_NUMBER_RESPONSE',
                                            data: { success: success, number: data.number }
                                        }, event.origin);
                                    }
                                    break;
                                
                                case 'GET_STATUS':
                                    if (window.softphoneAPI && window.softphoneAPI.getStatus) {
                                        event.source.postMessage({
                                            type: 'STATUS_RESPONSE',
                                            data: window.softphoneAPI.getStatus()
                                        }, event.origin);
                                    }
                                    break;
                                
                                case 'HANGUP':
                                    if (window.softphoneAPI && window.softphoneAPI.hangup) {
                                        const success = window.softphoneAPI.hangup();
                                        event.source.postMessage({
                                            type: 'HANGUP_RESPONSE',
                                            data: { success: success }
                                        }, event.origin);
                                    }
                                    break;
                                
                                case 'PING':
                                    // Responder a ping para verificar que el iframe está activo
                                    event.source.postMessage({
                                        type: 'PONG',
                                        data: { timestamp: Date.now() }
                                    }, event.origin);
                                    break;
                            }
                        });
                        
                        // Notificar a la página principal que el softphone está listo
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                type: 'SOFTPHONE_READY',
                                data: { 
                                    extension: webrtcConfig.extension,
                                    timestamp: Date.now()
                                }
                            }, '*');
                        }
                        
                        // Interceptar actualizaciones de estado para notificar cambios
                        const originalUpdateStatus = window.webrtcSoftphone.updateStatus;
                        if (originalUpdateStatus) {
                            window.webrtcSoftphone.updateStatus = function(status) {
                                originalUpdateStatus.call(this, status);
                                
                                // Notificar cambio de estado a la página principal
                                if (window.parent !== window) {
                                    window.parent.postMessage({
                                        type: 'SOFTPHONE_STATUS_CHANGED',
                                        data: window.softphoneAPI.getStatus()
                                    }, '*');
                                }
                            };
                        }
                        
                        // Interceptar cambios en llamadas para notificar
                        const originalMakeCall = window.webrtcSoftphone.makeCall;
                        if (originalMakeCall) {
                            window.webrtcSoftphone.makeCall = function() {
                                const result = originalMakeCall.call(this);
                                
                                // Notificar inicio de llamada
                                if (window.parent !== window) {
                                    window.parent.postMessage({
                                        type: 'SOFTPHONE_CALL_STARTED',
                                        data: { number: this.currentNumber }
                                    }, '*');
                                }
                                
                                return result;
                            };
                        }
                        
                        const originalHangup = window.webrtcSoftphone.hangup;
                        if (originalHangup) {
                            window.webrtcSoftphone.hangup = function() {
                                const result = originalHangup.call(this);
                                
                                // Notificar fin de llamada
                                if (window.parent !== window) {
                                    window.parent.postMessage({
                                        type: 'SOFTPHONE_CALL_ENDED',
                                        data: { number: this.currentNumber }
                                    }, '*');
                                }
                                
                                return result;
                            };
                        }
                        
                    } catch (error) {
                        console.error('❌ [Softphone Standalone] Error al inicializar:', error);
                        
                        // Notificar error a la página principal
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                type: 'SOFTPHONE_ERROR',
                                data: { error: error.message }
                            }, '*');
                        }
                    }
                    
                } else {
                    if (intentos >= maxIntentos) {
                        clearInterval(intervalo);
                        console.error('❌ [Softphone Standalone] Timeout esperando componentes');
                        
                        // Notificar timeout a la página principal
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                type: 'SOFTPHONE_ERROR',
                                data: { error: 'Timeout esperando componentes del softphone' }
                            }, '*');
                        }
                    }
                }
            }, 100);
        }
        
        // Iniciar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarSoftphone);
        } else {
            inicializarSoftphone();
        }
    </script>
</body>
</html>

