#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de Predicción de Demanda – Tienda Doña Elena
Ejecución automática: lee productos activos, calcula demanda para la próxima semana,
y guarda en prediccion_demanda mediante UPSERT.
Uso: python predict.py
"""

import sys
import os
import datetime
import logging
import psycopg2
from psycopg2 import sql

# ─── CONFIGURACIÓN DE BASE DE DATOS ────────────────────────────────
# IMPORTANTE: En producción usa variables de entorno o un config.ini
DB_CONFIG = {
    'dbname': 'tienda',
    'user': 'postgres',
    'password': '122173',        # Cambiar por seguridad
    'host': 'localhost',
    'port': 5432
}

# ─── CONFIGURACIÓN DE LOGGING ──────────────────────────────────────
#LOG_FILE = '/var/log/tienda_elena_ia.log'
# Si no tienes permisos para /var/log, usa la carpeta del script:
LOG_FILE = os.path.join(os.path.dirname(__file__), 'tienda_elena_ia.log')

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(message)s',
    handlers=[
        logging.FileHandler(LOG_FILE),
        logging.StreamHandler(sys.stdout)  # opcional: también muestra en terminal
    ]
)
logger = logging.getLogger(__name__)

# ─── FUNCIÓN PLANTILLA PARA EL MODELO REAL (simulación actual) ─────
# Al inicio de predict.py, agrega:
import predecir  # Importa el módulo que acabamos de crear

# Y reemplaza la función predecir_demanda_producto por:
def predecir_demanda_producto(id_producto, cursor):
    """Usa el módulo reutilizable para predecir"""
    query = """
        SELECT v.fecha, dv.cantidad AS cantidad_vendida, dv.id_producto
        FROM detalle_venta dv
        JOIN venta v ON dv.id_venta = v.id_venta
        WHERE dv.id_producto = %s
          AND v.fecha >= CURRENT_DATE - INTERVAL '28 days'
    """
    cursor.execute(query, (id_producto,))
    rows = cursor.fetchall()
    
    if not rows:
        return 0.0
    
    # Convertir a DataFrame y usar el módulo
    import pandas as pd
    df = pd.DataFrame(rows, columns=['fecha', 'cantidad_vendida', 'producto_id'])
    
    try:
        resultado = predecir.predecir_demanda(df, producto_id=id_producto)
        return resultado.get(id_producto, 0.0)
    except ValueError as e:
        logger.warning(f"Producto {id_producto}: {e}")
        return 0.0


def main():
    logger.info("=== INICIO DE PREDICCIÓN DE DEMANDA ===")
    try:
        # ── Conexión a PostgreSQL ───────────────────────────────────
        conn = psycopg2.connect(**DB_CONFIG)
        conn.autocommit = False
        cursor = conn.cursor()
        logger.info("Conexión a la base de datos establecida.")

        # ── Calcular próxima semana ─────────────────────────────────
        hoy = datetime.date.today()
        anio, semana_actual, _ = hoy.isocalendar()
        semana_objetivo = semana_actual + 1
        logger.info(f"Semana objetivo: Año {anio}, Semana {semana_objetivo}")

        # ── Obtener todos los productos activos ─────────────────────
        cursor.execute("""
            SELECT p.id_producto,
                COALESCE(i.stock_actual, 0) AS stock
            FROM producto p
            LEFT JOIN inventario i
                ON p.id_producto = i.id_producto
        """)
        productos = cursor.fetchall()
        logger.info(f"Productos encontrados: {len(productos)}")

        # ── Preparar la sentencia UPSERT ────────────────────────────
        upsert_sql = """
            INSERT INTO prediccion_demanda
                (id_producto, anio, semana, cantidad_predicha, cantidad_sugerida, fecha_calculo)
            VALUES (%s, %s, %s, %s, %s, NOW())
            ON CONFLICT (id_producto, anio, semana)
            DO UPDATE SET
                cantidad_predicha = EXCLUDED.cantidad_predicha,
                cantidad_sugerida = EXCLUDED.cantidad_sugerida,
                fecha_calculo = NOW()
        """

        procesados = 0
        for prod_id, stock in productos:
            try:
                demanda = predecir_demanda_producto(prod_id, cursor)

                # Lógica de negocio: cantidad sugerida = máximo(demanda - stock, 0)
                sugerido = max(int(demanda - stock + 0.5), 0)  # redondeo hacia arriba

                cursor.execute(upsert_sql, (prod_id, anio, semana_objetivo, demanda, sugerido))
                procesados += 1

            except Exception as e:
                logger.error(f"Error procesando producto ID {prod_id}: {e}")
                conn.rollback()  # falló este producto, pero continuamos
                # opcional: podrías decidir parar todo; aquí seguimos con el resto

        conn.commit()
        logger.info(f"Procesados exitosamente: {procesados} productos.")

    except psycopg2.Error as e:
        logger.critical(f"Error crítico de base de datos: {e}")
        if 'conn' in locals():
            conn.rollback()
        sys.exit(1)
    except Exception as e:
        logger.critical(f"Error inesperado: {e}")
        sys.exit(1)
    finally:
        if 'cursor' in locals():
            cursor.close()
        if 'conn' in locals():
            conn.close()
            logger.info("Conexión cerrada.")

    logger.info("=== FIN DEL SCRIPT ===\n")

if __name__ == "__main__":
    main()