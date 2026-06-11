# scripts/extraer_datos.py
import psycopg2
import pandas as pd
import numpy as np
import os

# Mismos parámetros de conexión de la Fase 2
import auditar
db_params = auditar.db_params

def procesar_dataset():
    conn = psycopg2.connect(**db_params)
    
    # Consulta base: Agrupar ventas por producto y semana del año
    query = """
        SELECT 
            dv.id_producto,
            EXTRACT(WEEK FROM v.fecha) AS semana,
            EXTRACT(YEAR FROM v.fecha) AS anio,
            SUM(dv.cantidad) AS unidades_vendidas,
            AVG(dv.subtotal) AS precio_promedio
        FROM detalle_venta dv
        JOIN venta v ON dv.id_venta = v.id_venta
        GROUP BY dv.id_producto, anio, semana
        ORDER BY dv.id_producto, anio, semana;
    """
    
    df = pd.read_sql(query, conn)
    
    # Crear Lag Features (Promedios móviles de semanas anteriores)
    # Usamos shift() para evitar "Data Leakage" (que el modelo mire el futuro)
    df['venta_sem_anterior'] = df.groupby('id_producto')['unidades_vendidas'].shift(1)
    df['promedio_2_sem'] = df.groupby('id_producto')['unidades_vendidas'].transform(lambda x: x.shift(1).rolling(2).mean())
    df['promedio_4_sem'] = df.groupby('id_producto')['unidades_vendidas'].transform(lambda x: x.shift(1).rolling(4).mean())
    

    df.fillna(0, inplace=True)
    
    # --- CORRECCIÓN DE RUTA DINÁMICA ---
    # Detecta la ubicación del script y sube un nivel para encontrar 'data'
    DIRECTORIO_ML = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    ruta_guardado = os.path.join(DIRECTORIO_ML, "data", "dataset_entrenamiento.csv")
    
    df.to_csv(ruta_guardado, index=False)
    print(f"Dataset generado con éxito en: {ruta_guardado}")
    conn.close()

if __name__ == "__main__":
    procesar_dataset()