<?php
    require_once 'conexion.php';

    $requestMethod = $_SERVER['REQUEST_METHOD'];
    header('Content-Type: application/json');
    
    switch ($requestMethod) {
        case 'GET':
            if (isset($_GET['console']) && isset($_GET['valor'])) {
                $data = calcularValorTotal($_GET['console'], $_GET['valor'], $conn);
            } else {
                $data = obtenerTotalDescuentos($conn);
            }
            break;
        default:
            http_response_code(405);
            $data = ['error' => 'Método no permitido'];
    }

    echo json_encode($data);
    
    function calcularValorTotal($console, $valor, $conn) {
        $query = "SELECT Precio_Minimo, Precio_Maximo, Descuento FROM Consolas WHERE Consola = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $console);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if (!$row) {
            http_response_code(404);
            return ['error' => 'Consola no encontrada'];
        }
    
        $precioMinimo = $row['Precio_Minimo'];
        $descuento    = $row['Descuento'];
    
        if ($valor >= $precioMinimo) {
            $valorConDescuento = $valor - ($valor * ($descuento / 100));
        } else {
            $valorConDescuento = $valor;
            $descuento = 0;
        }
        
        $fecha = date('Y-m-d H:i:s');
        $guardarVenta = guardarVenta($valor, $valorConDescuento, $descuento, $fecha, $conn);

        if($guardarVenta){
            http_response_code(200);
            return ['valorCobrarCliente' => $valorConDescuento];
        } else {
            http_response_code(500);
            return ['error' => 'Error al insertar la venta en la base de datos'];
        }
    
    }

    function guardarVenta($valor, $valorConDescuento, $descuento, $fecha, $conn){
        $query = "INSERT INTO Ventas (valor, valor_con_descuento, descuento, fecha) VALUES (?, ?, ?, ?)";
        $stmt  = $conn->prepare($query);
        $stmt->bind_param("ddds", $valor, $valorConDescuento, $descuento, $fecha);
        $stmt->execute();

        if ($conn->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }

    function obtenerTotalDescuentos($conn) {
        $query  = "SELECT SUM(valor - valor_con_descuento) as totalDescuentos FROM Ventas";
        $result = $conn->query($query);
        $row    = $result->fetch_assoc();
        http_response_code(200);
        return ['totalDescuentos' => $row['totalDescuentos']];
    }
    
    
?>