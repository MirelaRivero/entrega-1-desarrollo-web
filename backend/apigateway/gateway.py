import os
import secrets
import httpx
from typing import Optional

from fastapi import (
    FastAPI,
    Depends,
    HTTPException,
    Request,
    Response
)

from fastapi.middleware.cors import CORSMiddleware

from fastapi.security import (
    HTTPBearer,
    HTTPAuthorizationCredentials
)

app = FastAPI(
    title="Local API Gateway - Apícola Dorada",
    description="Gateway de seguridad, enrutamiento proxy e introspeccion de tokens"
)

# Configuracion de cors
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"]
)

security = HTTPBearer(auto_error=False)

# urls de microservicios
AUTH_SERVICE_URL = os.getenv("AUTH_SERVICE_URL", "http://127.0.0.1:9001")
INVENTORY_SERVICE_URL = os.getenv("INVENTORY_SERVICE_URL", "http://127.0.0.1:9002")
ORDERS_SERVICE_URL = os.getenv("ORDERS_SERVICE_URL", "http://127.0.0.1:9003")

VAULT_ADDR = os.getenv("VAULT_ADDR", "http://127.0.0.1:8200")
VAULT_TOKEN = os.getenv("VAULT_TOKEN", None)

# Secretos de respaldo local
LOCAL_INTROSPECTION_SECRET = os.getenv("AUTH_INTROSPECTION_SECRET", "demo-instrospection-secret")
LOCAL_BACKEND_SECRET = os.getenv("BACKEND_SHARED_SECRET", "sc-backend-secret-2026")

# obtenemos los secretos desde hashicorp
async def get_gateway_secrets():
    if VAULT_TOKEN:
        url = f"{VAULT_ADDR}/v1/secret/data/gateway"
        headers = {"X-Vault-Token": VAULT_TOKEN}
        try:
            async with httpx.AsyncClient(timeout=3.0) as client:
                response = await client.get(url, headers=headers)
            if response.status_code == 200:
                vault_data = response.json()["data"]["data"]
                return {
                    "auth_introspection_secret": vault_data.get("auth_introspection_secret", LOCAL_INTROSPECTION_SECRET),
                    "backend_shared_secret": vault_data.get("backend_shared_secret", LOCAL_BACKEND_SECRET)
                }
        except Exception:
            pass

    # Respaldo si Vault no está activo
    return {
        "auth_introspection_secret": LOCAL_INTROSPECTION_SECRET,
        "backend_shared_secret": LOCAL_BACKEND_SECRET
    }

# Autenticacion de clientes mediante instrospeccion
async def authenticate_client(
    credentials: Optional[HTTPAuthorizationCredentials] = Depends(security)
):
    if credentials is None:
        raise HTTPException(
            status_code=401,
            detail="Bearer token requerido"
        )

    gateway_secrets = await get_gateway_secrets()
    introspection_secret = gateway_secrets["auth_introspection_secret"]

    try:
        async with httpx.AsyncClient(timeout=5.0) as client:
            response = await client.post(
                f"{AUTH_SERVICE_URL}/introspect",
                json={"token": credentials.credentials},
                headers={"X-Gateway-Auth-Secret": introspection_secret}
            )

    except httpx.RequestError:
        raise HTTPException(
            status_code=502,
            detail="Error consultando Authentication Service"
        )

    identity = response.json()
    if not identity.get("active", False):
        raise HTTPException(
            status_code=401,
            detail="Token inválido o expirado"
        )

    return {
        "user_id": identity.get("user_id"),
        "username": identity.get("username"),
        "roles": identity.get("roles", ["user"]),
        "backend_secret": gateway_secrets["backend_shared_secret"]
    }

# Rutas que no requieren autenticacion (publicas)
'''@app.get("/api/products")
async def get_products(categoria: Optional[str] = None):
    url = f"{INVENTORY_SERVICE_URL}/products"
    if categoria:
        url += f"?categoria={categoria}"
        async with httpx.AsyncClient(timeout=5.0) as client:
            response = await client.get(url)
        return response.json()'''

@app.get("/")
def read_root():
    return {"status": "OK", "service": "API Gateway (FastAPI)"}

# Enrutador proxy protegudo para el resto de rutas
@app.api_route(
    "/api/v1/{service}/{path:path}",
    methods=["GET", "POST", "PUT", "PATCH", "DELETE"],
    # operation_id="dynamic_gateway_proxy"
)
async def proxy(
    service: str,
    path: str,
    request: Request,
    credentials: Optional[HTTPAuthorizationCredentials] = Depends(security)
):
    if request.method == "GET" and service == "inventory" and path.startswith("products"):
        secrets_data = await get_gateway_secrets()
        auth = {
            "user_id": "guest",
            "username": "guest",
            "roles": ["guest"],
            "backend_secret": secrets_data["backend_shared_secret"]
        }
    else:
        auth = await authenticate_client(credentials)

    # se redirigen a los microservicios según el prefijo
    if service == "inventory":
        target_url = f"{INVENTORY_SERVICE_URL}/{path}"
    elif service == "orders":
        target_url = f"{ORDERS_SERVICE_URL}/{path}"
    elif service == "auth":
        target_url = f"{AUTH_SERVICE_URL}/{path}"
    else:
        raise HTTPException(status_code=404, detail="Servicio no encontrado")

    body = await request.body() if request.method not in ["GET", "HEAD"] else None

    roles_str = ",".join(auth["roles"]) if isinstance(auth["roles"], list) else str(auth["roles"])

    headers = {
        "X-Gateway-Secret": auth["backend_secret"],
        "X-Authenticated-User": auth["username"],
        "X-Authenticated-Roles": roles_str
    }

    if request.headers.get("content-type") and body:
        headers["content-type"] = request.headers.get("content-type")

    try:
        async with httpx.AsyncClient(timeout=10.0) as client:
            res = await client.request(
                request.method,
                target_url,
                params=request.query_params,
                content=body,
                headers=headers
            )
    except httpx.RequestError:
        raise HTTPException(
            status_code=502,
            detail="Microservicio no disponible"
        )

    return Response(
        content=res.content,
        status_code=res.status_code,
        headers={"content-type": res.headers.get("content-type", "application/json")}
    )

# rutas separadas de los métodos
'''@app.get("/api/v1/{service}/{path:path}", summary="Proxy GET")
async def proxy_get(service: str, path: str, request: Request, auth=Depends(authenticate_client)):
    return await execute_proxy(service, path, request, auth)

@app.post("/api/v1/{service}/{path:path}", summary="Proxy POST")
async def proxy_post(service: str, path: str, request: Request, auth=Depends(authenticate_client)):
    return await execute_proxy(service, path, request, auth)

@app.put("/api/v1/{service}/{path:path}", summary="Proxy PUT")
async def proxy_put(service: str, path: str, request: Request, auth=Depends(authenticate_client)):
    return await execute_proxy(service, path, request, auth)

@app.delete("/api/v1/{service}/{path:path}", summary="Proxy DELETE")
async def proxy_delete(service: str, path: str, request: Request, auth=Depends(authenticate_client)):
    return await execute_proxy(service, path, request, auth)'''

if __name__  == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=9000)

    
