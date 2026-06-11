<?php
/**
 * Validator — Tienda Doña Elena
 *
 * Clase de validación reutilizable.
 * Recoge todos los errores antes de reportar (no falla en el primero).
 *
 * Uso:
 *   $v = new Validator($_POST);
 *   $v->required('nombre')->minLength('nombre', 3)->positiveNumber('precio');
 *   if ($v->fails()) { Flash::errors($v->errors()); ... }
 */
class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // ── Reglas ───────────────────────────────────────────────────────────────

    /** Campo obligatorio (no vacío). */
    public function required(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value === '') {
            $this->errors[] = "El campo «{$label}» es obligatorio.";
        }
        return $this;
    }

    /** Longitud mínima. */
    public function minLength(string $field, int $min, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value !== '' && strlen($value) < $min) {
            $this->errors[] = "«{$label}» debe tener al menos {$min} caracteres.";
        }
        return $this;
    }

    /** Número mayor que cero (precio, monto). */
    public function positiveNumber(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? null;
        if ($value !== null && $value !== '' && (float) $value <= 0) {
            $this->errors[] = "«{$label}» debe ser mayor que 0.";
        }
        return $this;
    }

    /** Número mayor o igual a cero (stock). */
    public function nonNegativeInt(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? null;
        if ($value !== null && $value !== '' && (int) $value < 0) {
            $this->errors[] = "«{$label}» no puede ser negativo.";
        }
        return $this;
    }

    /** Formato de email. */
    public function email(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "«{$label}» no es un email válido.";
        }
        return $this;
    }

    /** Solo letras, números y guiones (SKU). */
    public function sku(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value !== '' && !preg_match('/^[A-Za-z0-9\-_]+$/', $value)) {
            $this->errors[] = "«{$label}» solo puede contener letras, números, guiones y guiones bajos.";
        }
        return $this;
    }

    /** Fecha en formato Y-m-d. */
    public function date(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value !== '') {
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                $this->errors[] = "«{$label}» no es una fecha válida (formato esperado: AAAA-MM-DD).";
            }
        }
        return $this;
    }

    /** Contraseña mínima de 6 caracteres. */
    public function password(string $field, string $label = 'Contraseña'): static
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && strlen($value) < 6) {
            $this->errors[] = "«{$label}» debe tener al menos 6 caracteres.";
        }
        return $this;
    }

    // ── Resultados ───────────────────────────────────────────────────────────

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /** Añade un error personalizado. */
    public function addError(string $msg): static
    {
        $this->errors[] = $msg;
        return $this;
    }
}
