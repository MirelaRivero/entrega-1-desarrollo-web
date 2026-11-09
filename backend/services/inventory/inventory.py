from fastapi import FastAPI, HTTPException
from pymongo import MongoClient
from pydantic import BaseModel
from typing import Optional
import os

app = FastAPI(title="Inventory Service")

# conexion a MongoDB
MONGO_URI = os.getenv("MONGO_URI", "mongodb://localhost:27017/")
client = MongoClient(MONGO_URI)
db = client["apicola_dorada"]

class ProductSchema(BaseModel):
    productId: int
    nombre: str
    precio: int
    stock: int
    categoria: str
    destacado: bool = False
    descripcion: Optional[str] = None
    imagen: Optional[str]= None

@app.get("/products")
def get_products(categoria: Optional[str] = None, destacado: Optional[bool] = None):
    query = {}
    if categoria:
        query["categoria"] = categoria
    if destacado is not None:
        query["destacado"] = destacado
        
    products = list(db.products.find(query, {"_id": 0}))
    return {"status": 200, "products": products}

@app.get("/products/seed")
def seed_products():
    db.products.delete_many({})
    try:
        db.products.drop_indexes()
    except Exception:
        pass

    productos_iniciales = [
        { "productId": 1, "nombre": 'Miel de Ulmo', "categoria": 'mieles', "precio": 6500, "descripcion": 'Aroma floral pronunciado y textura cremosa única.', "imagen": 'img/mieles/miel1.png', "destacado": True, "stock": 30 },
        { "productId": 2, "nombre": 'Miel de Multifloral', "categoria": 'mieles', "precio": 5900, "descripcion": 'Proveniente del bosque nativo, suave y balanceada.', "imagen": 'img/mieles/miel2.png', "destacado": True, "stock": 45 },
        { "productId": 3, "nombre": 'Miel de Quillay', "categoria": 'mieles', "precio": 6200, "descripcion": 'Sabor ambarino intenso, ideal para endulzar infusiones.', "imagen": 'img/mieles/miel3.png', "destacado": True, "stock": 25 },
        { "productId": 4, "nombre": 'Miel de Propóleo', "categoria": 'mieles', "precio": 7000, "descripcion": 'Concentrado natural con propiedades antibacterianas.', "imagen": 'img/mieles/miel4.png', "destacado": True, "stock": 15 },
        { "productId": 5, "nombre": 'Pack Degustación 3 Mieles', "categoria": 'packs', "precio": 15990, "descripcion": 'Incluye frascos de Multiforal, Quillay y Propóleo.', "imagen": 'img/packs/pack1.png', "destacado": False, "stock": 10 },
        { "productId": 6, "nombre": 'Propóleo en Gotas 30 ml', "categoria": 'derivados', "precio": 4500, "descripcion": 'Tintura de propóleo natural para reforzar defensas.', "imagen": 'img/derivados/propo1.png', "destacado": False, "stock": 60 },
        { "productId": 7, "nombre": 'Polen Granulado 250g', "categoria": 'derivados', "precio": 5200, "descripcion": 'Mezcla de mieles con propiedades antioxidantes y antibacterianas.', "imagen": 'img/derivados/polen1.png', "destacado": False, "stock": 40 }
    ]
    db.products.insert_many(productos_iniciales)
    return {"status": 201, "message": "Productos insertados exitosamente"}

@app.get("/products/{product_id}")
def get_product(product_id: int):
    product = db.products.find_one({"productId": product_id}, {"_id": 0})
    if not product:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    return {"status": 200, "product": product}

@app.post("/products")
def create_product(product: ProductSchema):
    if db.products.find_one({"productId": product.productId}):
        raise HTTPException(status_code=404, detail="El producto ya existe")

    product_dict = product.dict()
    db.products.insert_one(product_dict)
    product_dict.pop("_id", None)
    return {"status": 201, "message": "Producto creado exitosamente", "product": product_dict}
# Ejecucion en puerto 9002
if __name__=="__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=9002)