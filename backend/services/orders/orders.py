from fastapi import FastAPI, HTTPException, Header
from pymongo import MongoClient
from pydantic import BaseModel, Field
from typing import List, Optional
from enum import Enum
import random
import secrets
from datetime import datetime, timezone
import os

app = FastAPI(title="Orders Service")

MONGO_URI = os.getenv("MONGO_URI", "mongodb://localhost:27017/")
BACKEND_SHARED_SECRET = os.getenv("BACKEND_SHARED_SECRET", "sc-backend-secret-2026")

client = MongoClient(MONGO_URI)
db = client["apicola_dorada"]

def verify_backend_secret(x_gateway_secret: str):
    print("[ORDERS BACKEND] Verificando secreto interno del API Gateway (X-Gateway-Secret)...", flush=True)
    if not secrets.compare_digest(x_gateway_secret, BACKEND_SHARED_SECRET):
        print("[ORDERS BACKEND] BLOQUEADO: Secreto de backend inválido o ausente (HTTP 403).", flush=True)
        raise HTTPException(status_code=403, detail="Acceso denegado: Secreto de Gateway inválido")
    print("[ORDERS BACKEND] Secreto verificado OK. Llamada autorizada.", flush=True)
class ItemSchema(BaseModel):
    productId: int
    nombre: Optional[str] = None
    precio: Optional[int] = 0
    cantidad: int = Field(gt=0, description="La cantidad debe ser mayor a 0")

class ClienteSchema(BaseModel):
    nombre: str
    rut: str
    email: str
    telefono: str

class DespachoSchema(BaseModel):
    direccion: str
    region: str
    comuna: str

class OrderCreateSchema(BaseModel):
    cliente: ClienteSchema
    despacho: DespachoSchema
    items: List[ItemSchema]

class EstadoPedidoEnum(str, Enum):
    pendiente = "Pendiente"
    enProceso = "En Proceso"
    enviado = "Enviado"
    entregado = "Entregado" 
    cancelado = "Cancelado"


# S06 Crear pedido desde Checkout con calculo del servidor
@app.post("/orders")
def create_order(
    order_data: OrderCreateSchema,
    x_gateway_secret: str = Header(default=""),
    x_authenticated_user: str = Header(default="desconocido")
):
    print(f"\n[ORDERS BACKEND] Petición POST /orders recibida desde usuario: {x_authenticated_user}", flush=True)
    verify_backend_secret(x_gateway_secret)
    
    if not order_data.items:
        raise HTTPException(status_code=400, detail="El pedido debe contener al menos un producto")

    subtotal = 0
    items_procesados = []

    for item in order_data.items:
        precio_unitario = item.precio if item.precio else 5000
        nombre_prod = item.nombre if item.nombre else f"Producto {item.productId}"
        item_subtotal = precio_unitario * item.cantidad
        subtotal += item_subtotal

        items_procesados.append({
            "productId": item.productId,
            "nombre": nombre_prod,
            "precio": precio_unitario,
            "cantidad": item.cantidad
        })

    costo_envio = 3500
    total_final = subtotal + costo_envio
    random_id = random.randint(1000, 99999)
    order_id = f"AP-2026-{random_id}"

    order_doc = {
        "orderId": order_id,
        "cliente": order_data.cliente.dict(),
        "despacho": order_data.despacho.dict(),
        "items": items_procesados,
        "subtotal": subtotal,
        "costoEnvio": costo_envio,
        "totalFinal": total_final,
        "estadoPedido": EstadoPedidoEnum.pendiente.value,
        "creadoEn": datetime.now(timezone.utc).isoformat(),
        "actualizadoEn": datetime.now(timezone.utc).isoformat()
    }

    print(f"[PERSISTENCIA MONGODB] Insertando nuevo pedido '{order_id}' en colección 'apicola_dorada.orders'...", flush=True)
    result = db.orders.insert_one(order_doc)
    print(f"[PERSISTENCIA MONGODB] Pedido guardado exitosamente con _id={result.inserted_id} | Total: ${total_final}", flush=True)

    order_doc.pop("_id", None)
    return {
        "status": 201,
        "message": "Pedido creado exitosamente",
        "order": order_doc
    }

@app.patch("/orders/{order_id}/status")
def update_order_status(order_id: str, nuevo_estado: EstadoPedidoEnum):
    order = db.orders.find_one({"orderId": order_id})
    if not order:
        raise HTTPException(status_code=404, detail="Pedido no encontrado")

    db.orders.update_one(
        {"orderId": order_id},
        {"$set": {"estadoPedido": nuevo_estado.value, "actualizadoEn": datetime.utcnow().isoformat()}}
    )
    return {"status": 200, "message": f"Estado del pedido actualizado a {nuevo_estado.value}"}

@app.get("/orders/{order_id}")
def get_order(order_id: str, x_gateway_secret: str = Header(default="")):
    verify_backend_secret(x_gateway_secret)
    print(f"[PERSISTENCIA MONGODB] Consultando pedido '{order_id}' en 'apicola_dorada.orders'...", flush=True)
    order = db.orders.find_one({"orderId": order_id}, {"_id": 0})
    if not order:
        raise HTTPException(status_code=404, detail="Pedido no encontrado")
    return {"order": order}

@app.get("/test-order")
def test_order():
    random_id = random.randint(1000, 99999)
    order_id = f"AP-2026-{random_id}"

    test_order_doc = {
        "orderId": order_id,
        "cliente": {
            "nombre": 'Cliente de prueba 2',
            "rut": '12345678-9',
            "email": 'cliente2.prueba@ejemplo.cl',
            "telefono": '+56912345678',
        },
        "despacho": {
            "direccion": 'Calle de prueba 123',
            "region": 'Región de prueba',
           "comuna": 'Comuna de prueba',
        },
        "items": [
            { "productId": 1, "nombre": 'Miel de Ulmo', "precio": 6500, "cantidad": 2 },
            { "productId": 6, "nombre": 'Propóleo en Gotas 30ml', "precio": 4500, "cantidad": 1 }
        ],
        "subtotal": 17500,
        "costoEnvio": 3500,
        "totalFinal": 21000,
        "estadoPedido": EstadoPedidoEnum.pendiente.value,
        "creadoEn": datetime.utcnow().isoformat(),
        "actualizadoEn": datetime.utcnow().isoformat()
    }

    db.orders.insert_one(test_order_doc)
    test_order_doc.pop("_id", None)

    return {
        "status": 201,
        "message": "Pedido de prueba creado exitosamente",
        "order": test_order_doc
    }

if __name__=="__main__":
    import uvicorn 
    uvicorn.run(app, host="0.0.0.0", port=9003)
