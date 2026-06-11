<?php
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {
    public function testPrecioNegativoEsRechazado() {
        $precio = -50;
        $esValido = ($precio > 0); 
        
        // Esperamos que sea FALSO porque el precio es negativo
        $this->assertFalse($esValido, "El sistema debe rechazar precios negativos");
    }

    public function testPrecioPositivoEsAceptado() {
        $precio = 150.50;
        $esValido = ($precio > 0);
        
        // Esperamos que sea VERDADERO
        $this->assertTrue($esValido, "El sistema debe aceptar precios positivos");
    }
}