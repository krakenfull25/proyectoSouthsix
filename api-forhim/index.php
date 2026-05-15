<?php

require __DIR__ . '/Slim/autoload.php';
require "src/funciones_CTES.php";

$app = new \Slim\App;
$app = new \Slim\App([
    'settings' => ['displayErrorDetails' => true]
]);

// -------------------------
//  AUTH
// -------------------------

// Registro de nuevo cliente
$app->post('/registro', function ($request) {
    $datos = [
        "email"     => $request->getParam("email"),
        "username"  => $request->getParam("username"),
        "password"  => $request->getParam("password"),
        "nombre"    => $request->getParam("nombre"),
        "apellidos" => $request->getParam("apellidos"),
        "telefono"  => $request->getParam("telefono")
    ];
    echo json_encode(registro($datos));
});

// Login
$app->post('/login', function ($request) {
    $datos = [
        "email"    => $request->getParam("email"),
        "password" => $request->getParam("password")
    ];
    echo json_encode(login($datos));
});

// -------------------------
//  PRODUCTOS
// -------------------------

$app->get('/productos', function () {
    echo json_encode(obtener_productos());
});

$app->get('/productos/{id_producto}', function ($request) {
    $id_producto = $request->getAttribute("id_producto");
    echo json_encode(obtener_producto($id_producto));
});

$app->get('/productos/categoria/{id_categoria}', function ($request) {
    $id_categoria = $request->getAttribute("id_categoria");
    echo json_encode(obtener_productos_categoria($id_categoria));
});

// -------------------------
//  CATEGORIAS
// -------------------------

$app->get('/categorias', function () {
    echo json_encode(obtener_categorias());
});

// -------------------------
//  USUARIOS
// -------------------------

$app->get('/usuarios', function () {
    echo json_encode(obtener_usuarios());
});

$app->get('/usuarios/{id_usuario}', function ($request) {
    $id_usuario = $request->getAttribute("id_usuario");
    echo json_encode(obtener_usuario($id_usuario));
});


$app->post('/compra', function ($request) {

    // Verificar token
    $token = $request->getHeader("Authorization");
    if (empty($token)) {
        echo json_encode(["error" => "Token no proporcionado"]);
        return;
    }

    $token    = str_replace("Bearer ", "", $token[0]);
    $payload  = verificar_token($token);

    if (!$payload) {
        echo json_encode(["error" => "Token inválido o expirado"]);
        return;
    }

    $id_usuario    = $payload["id_usuario"];
    $productos     = json_decode($request->getParam("productos"), true);
    $direccion     = $request->getParam("direccion_envio");
    $id_cupon      = $request->getParam("id_cupon") ?? null;

    if (!$productos || !$direccion) {
        echo json_encode(["error" => "Faltan datos obligatorios"]);
        return;
    }

    echo json_encode(realizar_compra($id_usuario, $productos, $direccion, $id_cupon));
});

// -------------------------
//  ADMIN
// -------------------------

// Helper para verificar admin
function es_admin($request) {
    $token = $request->getHeader("Authorization");
    if (empty($token)) return false;
    $token   = str_replace("Bearer ", "", $token[0]);
    $payload = verificar_token($token);
    return ($payload && $payload["rol"] == "admin");
}

// Borrar cliente
$app->delete('/admin/clientes/{id_usuario}', function ($request) {
    if (!es_admin($request)) { echo json_encode(["error" => "Acceso denegado"]); return; }
    echo json_encode(borrar_cliente($request->getAttribute("id_usuario")));
});

// Editar stock
$app->put('/admin/productos/{id_producto}/stock', function ($request) {
    if (!es_admin($request)) { echo json_encode(["error" => "Acceso denegado"]); return; }
    echo json_encode(editar_stock($request->getAttribute("id_producto"), $request->getParam("stock")));
});

// Modificar estado pedido
$app->put('/admin/pedidos/{id_pedido}', function ($request) {
    if (!es_admin($request)) { echo json_encode(["error" => "Acceso denegado"]); return; }
    echo json_encode(modificar_pedido($request->getAttribute("id_pedido"), $request->getParam("estado")));
});

// Eliminar pedido
$app->delete('/admin/pedidos/{id_pedido}', function ($request) {
    if (!es_admin($request)) { echo json_encode(["error" => "Acceso denegado"]); return; }
    echo json_encode(eliminar_pedido($request->getAttribute("id_pedido")));
});

// Editar perfil propio
$app->put('/perfil', function ($request) {
    $token = $request->getHeader("Authorization");
    if (empty($token)) { echo json_encode(["error" => "Token no proporcionado"]); return; }
    $token   = str_replace("Bearer ", "", $token[0]);
    $payload = verificar_token($token);
    if (!$payload) { echo json_encode(["error" => "Token inválido o expirado"]); return; }

    echo json_encode(editar_perfil(
        $payload["id_usuario"],
        $request->getParam("username"),
        $request->getParam("nombre"),
        $request->getParam("telefono"),
        $request->getParam("email")
    ));
});
$app->run();
?>