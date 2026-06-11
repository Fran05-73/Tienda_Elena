#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Módulo reutilizable de predicción de demanda.
Contiene funciones puras que pueden ser importadas desde:
- predict.py (script batch)
- PHP (vía exec o API)
- Tests unitarios (pytest)
"""

import pandas as pd
import numpy as np
import os
import joblib
import logging

logger = logging.getLogger(__name__)

# Ruta al modelo entrenado
MODELO_PATH = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'models', 'random_forest_demand.pkl'))


def cargar_modelo():
    """Carga el modelo de ML desde el archivo .pkl"""
    if not os.path.exists(MODELO_PATH):
        raise FileNotFoundError(f"Modelo no encontrado en: {MODELO_PATH}")
    return joblib.load(MODELO_PATH)


def predecir_demanda(df_historico, producto_id=None):
    """
    Predice la demanda para la próxima semana usando el modelo ML.
    
    Args:
        df_historico: DataFrame con columnas mínimas: 
                      ['producto_id', 'fecha', 'cantidad_vendida']
        producto_id: (Opcional) Filtrar por un producto específico
    
    Returns:
        dict con {'producto_id': demanda_predicha, ...}
    """
    # CP-12: Validar que hay datos suficientes
    if df_historico is None or df_historico.empty or len(df_historico) < 7:
        raise ValueError("No hay datos suficientes para una predicción confiable")
    
    if producto_id is not None:
        df_historico = df_historico[df_historico['producto_id'] == producto_id]
        if len(df_historico) < 7:
            raise ValueError(f"Datos insuficientes para el producto {producto_id}")
    
    try:
        modelo = cargar_modelo()
        
        # Aquí iría la lógica real de features del modelo
        # Por ahora, simulamos con el promedio de las últimas 4 semanas
        df_historico['fecha'] = pd.to_datetime(df_historico['fecha'])
        df_ultimas_4_semanas = df_historico[
            df_historico['fecha'] >= pd.Timestamp.now() - pd.Timedelta(days=28)
        ]
        
        predicciones = {}
        for pid in df_ultimas_4_semanas['producto_id'].unique():
            demanda = df_ultimas_4_semanas[
                df_ultimas_4_semanas['producto_id'] == pid
            ]['cantidad_vendida'].mean()
            predicciones[pid] = round(float(demanda), 2)
        
        return predicciones
    
    except Exception as e:
        logger.error(f"Error en predicción: {e}")
        raise


def calcular_sugerido(demanda_predicha, stock_actual):
    """
    Calcula la cantidad sugerida a pedir.
    Fórmula: max(demanda - stock, 0)
    """
    return max(int(demanda_predicha - stock_actual + 0.5), 0)