import pytest
import pandas as pd
import sys
import os

# Agregar scripts al path
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'scripts')))
import predecir


def test_prediccion_con_datos_suficientes():
    """CP-01: Datos válidos deben retornar predicción > 0"""
    # Crear datos ficticios de 30 días
    fechas = pd.date_range(end=pd.Timestamp.now(), periods=30, freq='D')
    df = pd.DataFrame({
        'fecha': fechas,
        'cantidad_vendida': [10] * 30,  # 10 unidades diarias
        'producto_id': [1] * 30
    })
    
    resultado = predecir.predecir_demanda(df, producto_id=1)
    
    assert 1 in resultado
    assert resultado[1] > 0, "La predicción debe ser mayor a 0"


def test_prediccion_sin_datos_suficientes():
    """CP-12: Pocos datos deben lanzar ValueError"""
    df_pocos = pd.DataFrame({
        'fecha': [pd.Timestamp.now()],
        'cantidad_vendida': [5],
        'producto_id': [1]
    })
    
    with pytest.raises(ValueError, match="No hay datos suficientes"):
        predecir.predecir_demanda(df_pocos, producto_id=1)


def test_calcular_sugerido():
    """Prueba adicional: lógica de cantidad sugerida"""
    assert predecir.calcular_sugerido(20, 15) == 5   # Faltan 5
    assert predecir.calcular_sugerido(10, 15) == 0   # Hay de sobra
    assert predecir.calcular_sugerido(10, 10) == 0   # Justo