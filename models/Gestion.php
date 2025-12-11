<?php
require_once __DIR__ . '/../config.php';

class Gestion {
    
    /**
     * Crear una nueva gestión
     */
    public static function crear($data) {
        try {
            $conn = getDBConnection();
            
            // Construir fecha_hora_recordatorio si se proporcionan fecha y hora
            $fecha_hora_recordatorio = null;
            if (!empty($data['fecha_recordatorio']) && !empty($data['hora_recordatorio'])) {
                $fecha_hora_recordatorio = $data['fecha_recordatorio'] . ' ' . $data['hora_recordatorio'] . ':00';
            }
            
            $sql = "INSERT INTO gestiones (
                asesor_cedula,
                cliente_id,
                canal_contacto,
                contrato_id,
                nivel1_tipo,
                nivel2_clasificacion,
                nivel3_detalle,
                observaciones,
                llamada_telefonica,
                whatsapp,
                correo_electronico,
                sms,
                correo_fisico,
                mensajeria_aplicacion,
                duracion_segundos,
                fecha_pago,
                valor_pago,
                fecha_hora_recordatorio
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $params = [
                $data['asesor_cedula'],
                $data['cliente_id'],
                $data['canal_contacto'] ?? null,
                $data['contrato_id'] ?? null,
                (!empty($data['nivel1_tipo']) && trim($data['nivel1_tipo']) !== '') ? trim($data['nivel1_tipo']) : null,
                (!empty($data['nivel2_clasificacion']) && trim($data['nivel2_clasificacion']) !== '') ? trim($data['nivel2_clasificacion']) : null,
                (!empty($data['nivel3_detalle']) && trim($data['nivel3_detalle']) !== '') ? trim($data['nivel3_detalle']) : null,
                $data['observaciones'] ?? null,
                $data['llamada_telefonica'] ?? 'no',
                $data['whatsapp'] ?? 'no',
                $data['correo_electronico'] ?? 'no',
                $data['sms'] ?? 'no',
                $data['correo_fisico'] ?? 'no',
                $data['mensajeria_aplicacion'] ?? 'no',
                $data['duracion_segundos'] ?? 0,
                $data['fecha_pago'] ?? null,
                $data['valor_pago'] ?? null,
                $fecha_hora_recordatorio
            ];
            
            $result = $stmt->execute($params);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Gestión guardada exitosamente',
                    'id' => $conn->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al guardar la gestión'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener gestiones de un asesor para un cliente
     */
    public static function obtenerGestionesCliente($cliente_id, $asesor_cedula) {
        try {
            $conn = getDBConnection();
            
            // Optimizado con índices: usa idx_gestiones_cliente y idx_gestiones_asesor_fecha
            $sql = "SELECT * FROM gestiones 
                    WHERE cliente_id = ? AND asesor_cedula = ?
                    ORDER BY fecha_creacion DESC
                    LIMIT 100";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$cliente_id, $asesor_cedula]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener historial de gestiones de un cliente
     */
    public static function obtenerHistorial($cliente_id) {
        try {
            $conn = getDBConnection();
            
            // Optimizado: usa idx_gestiones_cliente y limita resultados
            $sql = "SELECT g.*, u.nombre_completo as asesor_nombre 
                    FROM gestiones g
                    INNER JOIN usuarios u ON CAST(g.asesor_cedula AS CHAR) = CAST(u.cedula AS CHAR)
                    WHERE g.cliente_id = ?
                    ORDER BY g.fecha_creacion DESC
                    LIMIT 100";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$cliente_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtener recordatorios del día para un asesor
     * @param string $asesor_cedula
     * @param string $fecha Fecha en formato Y-m-d (opcional, por defecto hoy)
     * @return array
     */
    public static function obtenerRecordatoriosDia($asesor_cedula, $fecha = null) {
        try {
            $conn = getDBConnection();
            
            if ($fecha === null) {
                $fecha = date('Y-m-d');
            }
            
            // Obtener recordatorios del día específico
            $sql = "SELECT g.*, 
                           c.nombre as cliente_nombre,
                           c.cc as cliente_cc,
                           c.cel1 as cliente_celular
                    FROM gestiones g
                    INNER JOIN clientes c ON g.cliente_id = c.id
                    WHERE CAST(g.asesor_cedula AS CHAR) = CAST(? AS CHAR)
                      AND g.fecha_hora_recordatorio IS NOT NULL
                      AND DATE(g.fecha_hora_recordatorio) = ?
                      AND g.nivel3_detalle = 'seguimiento'
                    ORDER BY g.fecha_hora_recordatorio ASC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$asesor_cedula, $fecha]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener recordatorios: " . $e->getMessage());
            return [];
        }
    }
}

?>
