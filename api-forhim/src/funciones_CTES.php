<?php
require_once __DIR__ . "/../vendor/autoload.php";
const SERVIDOR_BD  = "localhost";
const USUARIO_BD   = "api_viewer";
const CLAVE_BD     = "ApiView2026!";
const NOMBRE_BD    = "tienda_online";
const JWT_SECRET = "pagamenos_secret_key_2026_tienda_online_forhim_super_secreta";
const JWT_DURACION = 604800; // 7 días en segundos


function registro($datos)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    // Comprobar si el email ya existe
    try {
        $consulta  = "SELECT id_usuario FROM usuarios WHERE email = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$datos["email"]]);
    } catch (PDOException $e) {
        $respuesta["error"] = "Error al verificar el email: " . $e->getMessage();
        return $respuesta;
    }

    if ($sentencia->rowCount() > 0) {
        $respuesta["error"] = "El email ya está registrado";
        return $respuesta;
    }

    // Insertar usuario
    try {
        $password_hash = hash('sha256', $datos["password"]);
        $consulta  = "INSERT INTO usuarios (email, username, password_hash, nombre, apellidos, telefono, rol, estado) VALUES (?, ?, ?, ?, ?, ?, 'cliente', 'activo')";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([
            $datos["email"],
            $datos["username"],
            $password_hash,
            $datos["nombre"],
            $datos["apellidos"],
            $datos["telefono"]
        ]);
    } catch (PDOException $e) {
        $respuesta["error"] = "Error al registrar el usuario: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["mensaje"] = "Usuario registrado correctamente";
    $conexion = null;
    return $respuesta;
}


function login($datos)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta  = "SELECT * FROM usuarios WHERE email = ? AND estado = 'activo'";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$datos["email"]]);
    } catch (PDOException $e) {
        $respuesta["error"] = "Error en la consulta: " . $e->getMessage();
        return $respuesta;
    }

    if ($sentencia->rowCount() == 0) {
        $respuesta["error"] = "Email o contraseña incorrectos";
        return $respuesta;
    }

    $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($usuario["password_hash"] !== hash('sha256', $datos["password"])) {
        $respuesta["error"] = "Email o contraseña incorrectos";
        return $respuesta;
    }

    // Generar JWT
    $payload = [
        "iat"        => time(),
        "exp"        => time() + JWT_DURACION,
        "id_usuario" => $usuario["id_usuario"],
        "email"      => $usuario["email"],
        "rol"        => $usuario["rol"]
    ];

    
    $token = \Firebase\JWT\JWT::encode($payload, JWT_SECRET, "HS256");

    $respuesta["token"]   = $token;
    $respuesta["usuario"] = [
        "id"        => $usuario["id_usuario"],
        "email"     => $usuario["email"],
        "username"  => $usuario["username"],
        "nombre"    => $usuario["nombre"],
        "apellidos" => $usuario["apellidos"],
        "telefono"  => $usuario["telefono"],
        "rol"       => $usuario["rol"]
    ];

    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}


function verificar_token($token)
{
    try {
        $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(JWT_SECRET, "HS256"));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}


function obtener_productos()
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta  = "SELECT productos.id_producto, productos.nombre, productos.descripcion, productos.precio, productos.imagen, productos.imagen2, productos.imagen3,productos.stock, categoria.nombre as categoria FROM productos, categoria WHERE productos.id_categoria = categoria.id_categoria";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion  = null;
        $respuesta["error"] = "No se pudo realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $productos = [];
    foreach ($sentencia->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $productos[] = [
            "id"              => $fila["id_producto"],
            "nombre_producto" => $fila["nombre"],
            "descripcion"     => $fila["descripcion"],
            "tipo_producto"   => $fila["categoria"],
            "imagenes"        => array_values(array_filter([$fila["imagen"], $fila["imagen2"], $fila["imagen3"]])),
            "precio"          => floatval($fila["precio"]),
            "stock"           => intval($fila["stock"])
        ];
    }

    $respuesta["productos"] = $productos;
    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}


function obtener_producto($id_producto)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta  = "SELECT productos.id_producto, productos.nombre, productos.descripcion, productos.precio, productos.imagen, productos.imagen2, productos.imagen3, categoria.nombre as categoria FROM productos, categoria WHERE productos.id_categoria = categoria.id_categoria AND id_producto = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_producto]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion  = null;
        $respuesta["error"] = "No se pudo realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    if ($sentencia->rowCount() > 0) {
        $fila = $sentencia->fetch(PDO::FETCH_ASSOC);
        $respuesta["producto"] = [
            "id"              => $fila["id_producto"],
            "nombre_producto" => $fila["nombre"],
            "descripcion"     => $fila["descripcion"],
            "tipo_producto"   => $fila["categoria"],
            "imagenes"        => array_values(array_filter([$fila["imagen"], $fila["imagen2"], $fila["imagen3"]])),
            "precio"          => floatval($fila["precio"])
        ];
    } else {
        $respuesta["mensaje"] = "Producto no encontrado";
    }

    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}


function obtener_productos_categoria($id_categoria)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta  = "SELECT productos.id_producto, productos.nombre, productos.descripcion, productos.precio, productos.imagen, productos.imagen2, productos.imagen3, categoria.nombre as categoria FROM productos, categoria WHERE productos.id_categoria = categoria.id_categoria AND productos.id_categoria = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_categoria]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion  = null;
        $respuesta["error"] = "No se pudo realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $productos = [];
    foreach ($sentencia->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $productos[] = [
            "id"              => $fila["id_producto"],
            "nombre_producto" => $fila["nombre"],
            "descripcion"     => $fila["descripcion"],
            "tipo_producto"   => $fila["categoria"],
            "imagenes"        => array_values(array_filter([$fila["imagen"], $fila["imagen2"], $fila["imagen3"]])),
            "precio"          => floatval($fila["precio"])
        ];
    }

    $respuesta["productos"] = $productos;
    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}


function obtener_categorias()
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta  = "SELECT * FROM categoria";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion  = null;
        $respuesta["error"] = "No se pudo realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $categorias = [];
    foreach ($sentencia->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $categorias[] = [
            "id"          => $fila["id_categoria"],
            "nombre"      => $fila["nombre"],
            "descripcion" => $fila["descripcion"]
        ];
    }

    $respuesta["categorias"] = $categorias;
    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}


function obtener_usuarios()
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta  = "SELECT id_usuario, email, username, nombre, apellidos, telefono, rol, estado, fecha_registro FROM usuarios";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion  = null;
        $respuesta["error"] = "No se pudo realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $usuarios = [];
    foreach ($sentencia->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $usuarios[] = [
            "id"             => $fila["id_usuario"],
            "email"          => $fila["email"],
            "username"       => $fila["username"],
            "nombre"         => $fila["nombre"],
            "apellidos"      => $fila["apellidos"],
            "telefono"       => $fila["telefono"],
            "rol"            => $fila["rol"],
            "estado"         => $fila["estado"],
            "fecha_registro" => $fila["fecha_registro"]
        ];
    }

    $respuesta["usuarios"] = $usuarios;
    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}


function obtener_usuario($id_usuario)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se pudo conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta  = "SELECT id_usuario, email, username, nombre, apellidos, telefono, rol, estado, fecha_registro FROM usuarios WHERE id_usuario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_usuario]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion  = null;
        $respuesta["error"] = "No se pudo realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    if ($sentencia->rowCount() > 0) {
        $fila = $sentencia->fetch(PDO::FETCH_ASSOC);
        $respuesta["usuario"] = [
            "id"             => $fila["id_usuario"],
            "email"          => $fila["email"],
            "username"       => $fila["username"],
            "nombre"         => $fila["nombre"],
            "apellidos"      => $fila["apellidos"],
            "telefono"       => $fila["telefono"],
            "rol"            => $fila["rol"],
            "estado"         => $fila["estado"],
            "fecha_registro" => $fila["fecha_registro"]
        ];
    } else {
        $respuesta["mensaje"] = "Usuario no encontrado";
    }

    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}


function realizar_compra($id_usuario, $productos, $direccion_envio, $id_cupon = null)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar a la base de datos: " . $e->getMessage()];
    }

    try {
        $conexion->beginTransaction();

        $total = 0;

        // Verificar stock y calcular total
        foreach ($productos as $item) {
            $consulta  = "SELECT precio, stock FROM productos WHERE id_producto = ?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$item["id_producto"]]);
            $producto  = $sentencia->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                $conexion->rollBack();
                return ["error" => "Producto " . $item["id_producto"] . " no encontrado"];
            }

            if ($producto["stock"] < $item["cantidad"]) {
                $conexion->rollBack();
                return ["error" => "Stock insuficiente para el producto " . $item["id_producto"]];
            }

            $total += $producto["precio"] * $item["cantidad"];
        }

        // Aplicar cupón si existe
        if ($id_cupon) {
            $consulta  = "SELECT * FROM cupones WHERE id_cupon = ? AND activo = 1 AND (fecha_fin IS NULL OR fecha_fin >= CURDATE()) AND (uso_maximo IS NULL OR usos_realizados < uso_maximo)";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$id_cupon]);
            $cupon = $sentencia->fetch(PDO::FETCH_ASSOC);

            if ($cupon) {
                if ($cupon["tipo_descuento"] == "porcentaje") {
                    $total = $total - ($total * $cupon["valor_descuento"] / 100);
                } else {
                    $total = $total - $cupon["valor_descuento"];
                }
                // Incrementar usos del cupón
                $consulta  = "UPDATE cupones SET usos_realizados = usos_realizados + 1 WHERE id_cupon = ?";
                $sentencia = $conexion->prepare($consulta);
                $sentencia->execute([$id_cupon]);
            } else {
                $conexion->rollBack();
                return ["error" => "Cupón no válido o expirado"];
            }
        }

        $total = max(0, round($total, 2));

        // Crear pedido
        $consulta  = "INSERT INTO pedidos (id_usuario, total, direccion_envio, estado, id_cupon) VALUES (?, ?, ?, 'pendiente', ?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_usuario, $total, $direccion_envio, $id_cupon]);
        $id_pedido = $conexion->lastInsertId();

        // Insertar detalle y descontar stock
        foreach ($productos as $item) {
            $consulta  = "SELECT precio FROM productos WHERE id_producto = ?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$item["id_producto"]]);
            $precio_unitario = $sentencia->fetchColumn();

            $consulta  = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$id_pedido, $item["id_producto"], $item["cantidad"], $precio_unitario]);

            $consulta  = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$item["cantidad"], $item["id_producto"]]);
        }

        $conexion->commit();

        return [
            "mensaje"   => "Pedido realizado correctamente",
            "id_pedido" => $id_pedido,
            "total"     => $total,
            "estado"    => "pendiente"
        ];

    } catch (PDOException $e) {
        $conexion->rollBack();
        return ["error" => "Error al procesar el pedido: " . $e->getMessage()];
    }
}


// ============================================
//  FUNCIONES ADMIN
// ============================================

function borrar_cliente($id_usuario)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        // No permitir borrar admins
        $consulta  = "SELECT rol FROM usuarios WHERE id_usuario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_usuario]);
        $usuario   = $sentencia->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) return ["error" => "Usuario no encontrado"];
        if ($usuario["rol"] == "admin") return ["error" => "No se puede eliminar un administrador"];

        $consulta  = "DELETE FROM usuarios WHERE id_usuario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_usuario]);

        return ["mensaje" => "Cliente eliminado correctamente"];
    } catch (PDOException $e) {
        return ["error" => "Error al eliminar: " . $e->getMessage()];
    }
}


function editar_stock($id_producto, $stock)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        $consulta  = "UPDATE productos SET stock = ? WHERE id_producto = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$stock, $id_producto]);

        if ($sentencia->rowCount() == 0) return ["error" => "Producto no encontrado"];

        return ["mensaje" => "Stock actualizado correctamente", "id_producto" => $id_producto, "stock" => $stock];
    } catch (PDOException $e) {
        return ["error" => "Error al actualizar stock: " . $e->getMessage()];
    }
}


function modificar_pedido($id_pedido, $estado)
{
    $estados_validos = ["pendiente", "confirmado", "enviado", "entregado", "cancelado"];

    if (!in_array($estado, $estados_validos)) {
        return ["error" => "Estado no válido. Usa: " . implode(", ", $estados_validos)];
    }

    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        $consulta  = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$estado, $id_pedido]);

        if ($sentencia->rowCount() == 0) return ["error" => "Pedido no encontrado"];

        return ["mensaje" => "Pedido actualizado correctamente", "id_pedido" => $id_pedido, "estado" => $estado];
    } catch (PDOException $e) {
        return ["error" => "Error al modificar pedido: " . $e->getMessage()];
    }
}


function eliminar_pedido($id_pedido)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        $conexion->beginTransaction();

        // Restaurar stock antes de eliminar
        $consulta  = "SELECT id_producto, cantidad FROM detalle_pedido WHERE id_pedido = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_pedido]);
        $detalles  = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detalles as $detalle) {
            $consulta  = "UPDATE productos SET stock = stock + ? WHERE id_producto = ?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$detalle["cantidad"], $detalle["id_producto"]]);
        }

        // Eliminar detalle y pedido
        $consulta  = "DELETE FROM detalle_pedido WHERE id_pedido = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_pedido]);

        $consulta  = "DELETE FROM pedidos WHERE id_pedido = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_pedido]);

        $conexion->commit();
        return ["mensaje" => "Pedido eliminado y stock restaurado correctamente"];
    } catch (PDOException $e) {
        $conexion->rollBack();
        return ["error" => "Error al eliminar pedido: " . $e->getMessage()];
    }
}

function editar_perfil($id_usuario, $username, $nombre, $telefono, $email)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        $consulta  = "UPDATE usuarios SET username = ?, nombre = ?, telefono = ?, email = ? WHERE id_usuario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$username, $nombre, $telefono, $email, $id_usuario]);
        return ["mensaje" => "Perfil actualizado correctamente"];
    } catch (PDOException $e) {
        return ["error" => "Error al actualizar: " . $e->getMessage()];
    }
}

function obtener_pedidos()
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        $consulta  = "SELECT pedidos.*, usuarios.username, usuarios.email FROM pedidos, usuarios WHERE pedidos.id_usuario = usuarios.id_usuario ORDER BY pedidos.id_pedido DESC";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
    } catch (PDOException $e) {
        return ["error" => "No se pudo realizar la consulta: " . $e->getMessage()];
    }

    $pedidos = [];
    foreach ($sentencia->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $pedidos[] = [
            "id_pedido"       => $fila["id_pedido"],
            "id_usuario"      => $fila["id_usuario"],
            "username"        => $fila["username"],
            "email"           => $fila["email"],
            "total"           => floatval($fila["total"]),
            "estado"          => $fila["estado"],
            "direccion_envio" => $fila["direccion_envio"],
            "fecha_pedido"    => $fila["fecha_pedido"]
        ];
    }

    $respuesta["pedidos"] = $pedidos;
    $sentencia = null;
    $conexion  = null;
    return $respuesta;
}

function obtener_direcciones($id_usuario)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        $consulta  = "SELECT * FROM direcciones WHERE id_usuario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$id_usuario]);
    } catch (PDOException $e) {
        return ["error" => "Error al obtener direcciones: " . $e->getMessage()];
    }

    $respuesta["direcciones"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    return $respuesta;
}

function añadir_producto($nombre, $descripcion, $precio, $stock, $id_categoria)
{
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, "phpmyadmin", "EstePona26@", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        return ["error" => "No se pudo conectar: " . $e->getMessage()];
    }

    try {
        $consulta  = "INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria) VALUES (?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$nombre, $descripcion, $precio, $stock, $id_categoria]);
        return ["mensaje" => "Producto añadido correctamente", "id_producto" => $conexion->lastInsertId()];
    } catch (PDOException $e) {
        return ["error" => "Error al añadir producto: " . $e->getMessage()];
    }
}
?>