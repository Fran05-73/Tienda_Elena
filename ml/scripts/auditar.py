# scripts/auditar.py
import psycopg2
import pandas as pd

db_params = {
    "host": "localhost",
    "database": "tienda", # Cambia por el nombre de tu BD
    "user": "postgres",         # Cambia por tu usuario
    "password": "122173"   # Cambia por tu contraseña
}

def ejecutar_auditoria():
    try:
        conn = psycopg2.connect(**db_params)
        print("=== CONEXIÓN EXITOSA A POSTGRESQL ===\n")
        
        # 1. Productos con al menos 10 ventas
        query1 = """
            SELECT COUNT(*) FROM (
                SELECT id_producto FROM detalle_venta 
                GROUP BY id_producto HAVING COUNT(id_producto) >= 10
            ) AS sub;
        """
        df1 = pd.read_sql(query1, conn)
        print(f"1. Productos con >= 10 ventas registradas: {df1.iloc[0,0]}")
        
        # 2. Tipo de dato de la fecha
        query2 = """
            SELECT column_name, data_type 
            FROM information_schema.columns 
            WHERE table_name = 'venta' AND column_name = 'fecha';
        """
        df2 = pd.read_sql(query2, conn)
        print(f"2. Estructura de la columna fecha: {df2.to_dict(orient='records')}")
        
        # 3. Cantidades anómalas (ceros o negativas)
        query3 = "SELECT COUNT(*) FROM detalle_venta WHERE cantidad <= 0;"
        df3 = pd.read_sql(query3, conn)
        print(f"3. Registros con cantidad errónea (<= 0): {df3.iloc[0,0]}")
        
        # 4. Productos que nunca se han vendido
        query4 = """
            SELECT COUNT(*) FROM producto p 
            LEFT JOIN detalle_venta dv ON p.id_producto = dv.id_producto 
            WHERE dv.id_producto IS NULL;
        """
        df4 = pd.read_sql(query4, conn)
        print(f"4. Productos huérfanos (nunca vendidos): {df4.iloc[0,0]}\n")
        
        conn.close()
    except Exception as e:
        print(f"Error en la auditoría: {e}")

if __name__ == "__main__":
    ejecutar_auditoria()