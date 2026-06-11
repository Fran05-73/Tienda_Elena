<?php
/**
 * functions/validacion.php — Tienda Doña Elena
 *
 * Funciones de validación de datos al estilo h3_act3.
 * Retornan TRUE si el dato es válido, o un string con el mensaje de error.
 *
 * Se cargan globalmente desde public/index.php.
 */

/** Limpia y sanitiza un dato de entrada. */
function limpiarDato(string $dato): string
{
    return htmlspecialchars(strip_tags(trim($dato)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida nombre de usuario: obligatorio, min 4 chars, solo alfanumérico.
 * @return true|string
 */
function validarUsuario(string $usuario): bool|string
{
    if (empty($usuario)) {
        return 'El nombre de usuario es obligatorio.';
    }
    if (strlen($usuario) < 4) {
        return 'El usuario debe tener al menos 4 caracteres.';
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
        return 'El usuario solo puede contener letras, números y guiones bajos.';
    }
    return true;
}

/**
 * Valida contraseña: obligatoria, mínimo 6 caracteres.
 * @return true|string
 */
function validarPassword(string $password): bool|string
{
    if (empty($password)) {
        return 'La contraseña es obligatoria.';
    }
    if (strlen($password) < 6) {
        return 'La contraseña debe tener al menos 6 caracteres.';
    }
    return true;
}

/**
 * Valida email: obligatorio, formato válido.
 * @return true|string
 */
function validarEmail(string $email): bool|string
{
    if (empty($email)) {
        return 'El email es obligatorio.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'El email no tiene un formato válido.';
    }
    return true;
}

/**
 * Valida texto genérico: obligatorio, longitud mínima.
 * @return true|string
 */
function validarTexto(string $texto, string $campo = 'Campo', int $min = 2): bool|string
{
    if (empty(trim($texto))) {
        return "{$campo} es obligatorio.";
    }
    if (strlen(trim($texto)) < $min) {
        return "{$campo} debe tener al menos {$min} caracteres.";
    }
    return true;
}

/**
 * Valida SKU: obligatorio, solo letras/números/guiones.
 * @return true|string
 */
function validarSKU(string $sku): bool|string
{
    if (empty(trim($sku))) {
        return 'El SKU es obligatorio.';
    }
    if (!preg_match('/^[A-Za-z0-9\-_]+$/', trim($sku))) {
        return 'El SKU solo puede contener letras, números, guiones y guiones bajos.';
    }
    return true;
}

/**
 * Valida precio: mayor que 0.
 * @return true|string
 */
function validarPrecio(mixed $precio): bool|string
{
    if ($precio === '' || $precio === null) {
        return 'El precio es obligatorio.';
    }
    if (!is_numeric($precio) || (float) $precio <= 0) {
        return 'El precio debe ser un número mayor que 0.';
    }
    return true;
}

/**
 * Valida stock: entero mayor o igual a 0.
 * @return true|string
 */
function validarStock(mixed $stock): bool|string
{
    if ($stock === '' || $stock === null) {
        return 'El stock es obligatorio.';
    }
    if (!ctype_digit((string) $stock) && (int) $stock < 0) {
        return 'El stock debe ser un número entero no negativo.';
    }
    if ((int) $stock < 0) {
        return 'El stock no puede ser negativo.';
    }
    return true;
}

/**
 * Valida fecha en formato Y-m-d.
 * @return true|string
 */
function validarFecha(string $fecha): bool|string
{
    if (empty(trim($fecha))) {
        return true; // La fecha puede ser opcional
    }
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$d || $d->format('Y-m-d') !== $fecha) {
        return 'La fecha no tiene un formato válido (AAAA-MM-DD).';
    }
    return true;
}

/**
 * Recoge errores de múltiples validaciones en un array.
 * Cada $result es el retorno de una función validar*().
 *
 * Uso:
 *   $errores = recogerErrores([
 *       validarSKU($sku),
 *       validarPrecio($precio),
 *   ]);
 */
function recogerErrores(array $resultados): array
{
    $errores = [];
    foreach ($resultados as $r) {
        if ($r !== true) {
            $errores[] = $r;
        }
    }
    return $errores;
}

function validarRecaptcha(string $token): bool|string
{
    if (empty($token)) {
        return 'Por favor completa el captcha.';
    }

    $secretKey = '6LfhUe8sAAAAAAobPLFOTqpQ8kkbZTVRzqPoOtgh'; // <-- Reemplaza con tu Secret Key

    $response = file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify?' . http_build_query([
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ])
    );

    if ($response === false) {
        return 'No se pudo verificar el captcha. Intenta de nuevo.';
    }

    $data = json_decode($response, true);

    if (!isset($data['success']) || $data['success'] !== true) {
        return 'Captcha inválido. Por favor intenta de nuevo.';
    }

    return true;
}
function validarCi(string $ci): bool|string
{
    $ci = trim($ci);
    if (empty($ci)) {
        return 'El CI es obligatorio.';
    }
    if (!preg_match('/^[0-9]+$/', $ci)) {
        return 'El CI solo puede contener números.';
    }
    if (strlen($ci) < 6 || strlen($ci) > 12) {
        return 'El CI debe tener entre 6 y 12 dígitos.';
    }
    return true;
}