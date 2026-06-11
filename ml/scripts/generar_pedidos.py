import os, sys, datetime, psycopg2, pandas as pd, joblib, warnings
warnings.filterwarnings("ignore", category=UserWarning)

DIRECTORIO_SCRIPT = os.path.dirname(os.path.abspath(__file__))
sys.path.append(DIRECTORIO_SCRIPT)
import auditar
db_params = auditar.db_params

def generar_sugerencias_pedido():
    DIRECTORIO_ML = os.path.dirname(DIRECTORIO_SCRIPT)
    ruta_modelo = os.path.join(DIRECTORIO_ML, "models", "random_forest_demand.pkl")
    if not os.path.exists(ruta_modelo):
        print("[ERROR] Modelo no encontrado.")
        return

    model = joblib.load(ruta_modelo)
    hoy = datetime.date.today()
    anio_actual = hoy.year
    semana_a_predecir = hoy.isocalendar()[1] + 1
    print(f"=== PREDICCIÓN AÑO {anio_actual} SEMANA {semana_a_predecir} ===")

    conn = psycopg2.connect(**db_params)

    # Obtener features usando stock_actual de inventario
    query_features = """
        SELECT p.id_producto, p.nombre, COALESCE(i.stock_actual,0) AS stock_actual,
               sub.precio_prom, sub.ult_semana, sub.prom_2, sub.prom_4
        FROM producto p
        LEFT JOIN inventario i ON p.id_producto = i.id_producto
        LEFT JOIN (
            SELECT dv.id_producto,
                   AVG(dv.subtotal) AS precio_prom,
                   SUM(CASE WHEN v.fecha >= NOW() - INTERVAL '7 days'  THEN dv.cantidad ELSE 0 END) AS ult_semana,
                   SUM(CASE WHEN v.fecha >= NOW() - INTERVAL '14 days' THEN dv.cantidad ELSE 0 END)/2.0 AS prom_2,
                   SUM(CASE WHEN v.fecha >= NOW() - INTERVAL '28 days' THEN dv.cantidad ELSE 0 END)/4.0 AS prom_4
            FROM detalle_venta dv
            JOIN venta v ON dv.id_venta = v.id_venta
            GROUP BY dv.id_producto
        ) sub ON p.id_producto = sub.id_producto
    """
    df = pd.read_sql(query_features, conn)
    if df.empty:
        print("Sin datos.")
        conn.close()
        return
    
    df.rename(columns={
        'precio_prom': 'precio_promedio',
        'ult_semana': 'venta_sem_anterior',
        'prom_2': 'promedio_2_sem',
        'prom_4': 'promedio_4_sem'
    }, inplace=True)

    df['semana'] = semana_a_predecir
    features = ['semana','precio_promedio','venta_sem_anterior','promedio_2_sem','promedio_4_sem']
    df[features] = df[features].fillna(0)
    X = df[features]
    df['demanda_predicha'] = model.predict(X)

    # Limpiar pedidos pendientes antiguos
    cursor = conn.cursor()
    cursor.execute("DELETE FROM pedidos_sugeridos WHERE estado = 'pendiente'")
    conn.commit()

    # Insertar predicciones en prediccion_demanda (UPSERT)
    upsert_pred = """
        INSERT INTO prediccion_demanda (id_producto, anio, semana, cantidad_predicha, cantidad_sugerida, fecha_calculo)
        VALUES (%s, %s, %s, %s, 0, NOW())
        ON CONFLICT (id_producto, anio, semana) DO UPDATE SET cantidad_predicha = EXCLUDED.cantidad_predicha, fecha_calculo = NOW()
    """
    insert_pedido = """
        INSERT INTO pedidos_sugeridos (producto_id, cantidad_sugerida, motivo, estado)
        VALUES (%s, %s, %s, 'pendiente')
    """

    for _, row in df.iterrows():
        prod_id = int(row['id_producto'])
        stock = float(row['stock_actual'])
        pred = float(row['demanda_predicha'])
        sugerido = max(int(pred - stock), 0) if stock < pred else 0

        cursor.execute(upsert_pred, (prod_id, anio_actual, semana_a_predecir, pred))

        if sugerido > 0:
            motivo = f"Demanda predicha: {pred:.2f}, stock actual: {stock:.0f}"
            cursor.execute(insert_pedido, (prod_id, sugerido, motivo))

    conn.commit()
    cursor.close()
    conn.close()
    print("Pedidos sugeridos actualizados en pedidos_sugeridos.\n")

if __name__ == "__main__":
    generar_sugerencias_pedido()