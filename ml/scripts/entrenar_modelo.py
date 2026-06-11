# ml/scripts/entrenar_modelo.py
import os
import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error
import joblib

# logs para errores
import logging

# --- CORRECCIÓN DE RUTA DEL LOG ---
DIRECTORIO_SCRIPT = os.path.dirname(os.path.abspath(__file__))
ruta_log = os.path.join(DIRECTORIO_SCRIPT, 'tienda_elena_ia.log')

logging.basicConfig(
    filename=ruta_log,  # Usamos la ruta absoluta dinámica
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

# Ejemplo de uso en tu código:
logging.info("Iniciando entrenamiento del modelo...")
try:
    # Tu código de entrenamiento aquí
    logging.info("Modelo guardado exitosamente.")
except Exception as e:
    logging.error(f"Error al entrenar: {str(e)}")


def entrenar():
    # --- ENCONTRAR LAS RUTAS ABSOLUTAS AUTOMÁTICAMENTE ---
    DIRECTORIO_SCRIPT = os.path.dirname(os.path.abspath(__file__))
    DIRECTORIO_ML = os.path.dirname(DIRECTORIO_SCRIPT)
    ruta_csv = os.path.join(DIRECTORIO_ML, "data", "dataset_entrenamiento.csv")
    ruta_modelo = os.path.join(DIRECTORIO_ML, "models", "random_forest_demand.pkl")
    
    print(f"Leyendo dataset desde: {ruta_csv}")
    df = pd.read_csv(ruta_csv)
    if df.empty:
        print("[ERROR] El dataset está vacío. Corre extraer_datos.py primero.")
        return

    # Definir variables predictoras (X) y variable objetivo (y)
    features = ['semana', 'precio_promedio', 'venta_sem_anterior', 'promedio_2_sem', 'promedio_4_sem']
    target = 'unidades_vendidas'
    
    # Ordenar cronológicamente para respetar la línea de tiempo del negocio
    df = df.sort_values(by=['anio', 'semana'])
    
    # Separación: 80% de las semanas para entrenar, 20% más reciente para evaluar
    split_idx = int(len(df) * 0.8)
    train_df = df.iloc[:split_idx]
    test_df = df.iloc[split_idx:]
    
    X_train, y_train = train_df[features], train_df[target]
    X_test, y_test = test_df[features], test_df[target]
    
    print("Entrenando el modelo Random Forest... (esto puede tomar unos segundos)")
    
    # Configuramos el modelo de forma ligera y eficiente para optimizar el consumo de hardware
    model = RandomForestRegressor(n_estimators=50, random_state=42, max_depth=10, n_jobs=-1)
    model.fit(X_train, y_train)
    
    # Evaluar la precisión con el set de prueba (el 20% que el modelo no conocía)
    predicciones = model.predict(X_test)
    error = mean_absolute_error(y_test, predicciones)
    
    print(f"\n=========================================")
    print(f"        === EVALUACIÓN DEL MODELO ===")
    print(f"=========================================")
    print(f"Error Medio Absoluto (MAE): {error:.2f} unidades.")
    print(f"Significado: En promedio, las predicciones semanales del modelo")
    print(f"se desvían por {error:.2f} unidades respecto a las ventas reales.")
    print(f"=========================================\n")
    
    # Guardar el "cerebro" del modelo entrenado en un archivo binario
    joblib.dump(model, ruta_modelo)
    print(f"[ÉXITO] Modelo guardado perfectamente en:\n--> {ruta_modelo}\n")

if __name__ == "__main__":
    entrenar()