from fastapi import FastAPI, HTTPException, Header
from pymongo import MongoClient
from passlib.context import CryptContext
from pydantic import BaseModel, EmailStr
from typing import Optional, List
from enum import Enum
from datetime import datetime, timedelta, timezone
import secrets
import os

app = FastAPI(title="Auth & Clients Service",
              description="Servicio de autenticacion, emision de tokens por introspecciony gestion de clientes")

# Conexion a MongoDB
MONGO_URI = os.getenv("MONGO_URI", "mongodb://localhost:27017/")
client = MongoClient(MONGO_URI)
db = client["apicola_dorada"]

# Configuracion de seguridad
pwd_context = CryptContext(schemes=["bcrypt"],  deprecated="auto")
TOKEN_LIFETIMEMINUTES = 30

AUTH_INTROSPECTION_SECRET = os.getenv(
    "AUTH_INTROSPECTION_SECRET",
    "demo-instrospection-secret"
)

SESSIONS = {}

class RoleEnum(str, Enum):
    USER = "user"
    ADMIN = "admin"

class RegisterRequest(BaseModel):
    email : str
    password: str
    nombre: Optional[str] = None
    rut: Optional[str] = None
    telefono: Optional[str] = None
    rol: Optional[RoleEnum] = RoleEnum.USER

class LoginRequest(BaseModel):
    username: str
    password: str

class IntrospectionRequest(BaseModel):
    token: str

# Funcion para validar la cabecera del gateway
def verify_gateway_secret(x_gateway_auth_secret: str):
    if not secrets.compare_digest(x_gateway_auth_secret, AUTH_INTROSPECTION_SECRET):
        raise HTTPException(
            status_code=403,
            detail="Gateway no autorizado"
        )

@app.post("/login")
def login(
    request: LoginRequest,
    x_gateway_auth_secret: str = Header(default="demo-instrospection-secret")
):
    verify_gateway_secret(x_gateway_auth_secret)

    # Busqueda en MongoDB por email o username
    user = db.clients.find_one({"$or": [{"email": request.username}, {"username": request.username}]})

    if user is None:
        raise HTTPException(status_code=401, detail="Usuario incorrecto")
    
    if not pwd_context.verify(request.password, user["password"]):
        raise HTTPException(status_code=401, detail="Credenciales inválidas")

    roles = user.get("roles", [user.get("rol", RoleEnum.USER.value)])

    # Generacion de token aleatorio seguro
    access_token = secrets.token_urlsafe(32)
    expiration = datetime.now(timezone.utc) + timedelta(minutes=TOKEN_LIFETIMEMINUTES)

    SESSIONS[access_token] = {
        "user_id": str(user["_id"]),
        "username": user.get("email", request.username),
        "roles": roles,
        "expires_at": expiration 
    }

    return {
        "access_token": access_token,
        "token_type": "bearer",
        "expires_in": TOKEN_LIFETIMEMINUTES * 60
    }

# Endpoint de introspeccion del token
@app.post("/introspect")
def introspect(
    request: IntrospectionRequest,
    x_gateway_auth_secret: str = Header(default="demo-instrospection-secret")
):
    verify_gateway_secret(x_gateway_auth_secret)

    session = SESSIONS.get(request.token)
    if session is None:
        return {"active": False}

    expires_at = session.get("expires_at")
    if expires_at and datetime.now(timezone.utc) > expires_at:
        SESSIONS.pop(request.token, None)
        return {"active": False}

    return {
        "active": True,
        "user_id": session["user_id"],
        "username": session["username"],
        "roles": session["roles"],
        "expires_at": session["expires_at"].isoformat() if session.get("expires_at") else None

    }

@app.post("/logout")
def logout(
    request: IntrospectionRequest,
    x_gateway_auth_secret: str = Header(default="")
):
    verify_gateway_secret(x_gateway_auth_secret)

    SESSIONS.pop(request.token, None)
    return {"message": "Sesión finalizada"}

@app.post("/register")
def register(
    request: RegisterRequest,
    x_gateway_auth_secret: str = Header(default="")
):
    verify_gateway_secret(x_gateway_auth_secret)

    if db.clients.find_one({"email": request.email}):
        raise HTTPException(status_code=409, detail="El correo electrónico ya se encuentra registrado")

    password_hashed = pwd_context.hash(request.password)
    new_user = {
        "email": request.email,
        "password": password_hashed,
        "nombre": request.nombre,
        "rut": request.rut,
        "telefono": request.telefono,
        "roles": [request.rol.value],
        "createdAt": datetime.now(timezone.utc).isoformat()
    }

    result = db.clients.insert_one(new_user)
    return {
        "status": 201,
        "message": "Cliente registrado exitosamente",
        "client": {
            "id": str(result.inserted_id),
            "email": request.email,
            "roles": new_user["roles"]
            }
    }

@app.get("/register-test")
def register_test(x_gateway_auth_secret: str = Header(default="demo-instrospection-secret")):
    verify_gateway_secret(x_gateway_auth_secret)

    pass_user = pwd_context.hash("123456")
    test_user = {
        "email": 'correo2.test@ejemplo.cl',
        "username": "cliente_test",
        "password": pass_user,
        "nombre": 'Cliente Test',
        "rut": '12345678-9',
        "telefono": '+56912345678',
        "roles": [RoleEnum.USER.value],
        "createdAt": datetime.now(timezone.utc).isoformat()
    }

    if not db.clients.find_one({"email": test_user["email"]}):
        db.clients.insert_one(test_user)

    # admin de prueba
    pass_admin = pwd_context.hash("admin123")
    test_admin = {
        "email": 'admin@apicoladorada.cl',
        "username": "admin_apicola",
        "password": pass_admin,
        "nombre": 'Administrador Apicola',
        "rut": '98765432-1',
        "telefono": '+56987654321',
        "roles": [RoleEnum.USER.value, RoleEnum.ADMIN.value],
        "createdAt": datetime.now(timezone.utc).isoformat()
    }
    if not db.clients.find_one({"email": test_admin["email"]}):
            db.clients.insert_one(test_admin)

    return {
        "status": 201,
        "message": "Usuarios de prueba registrados exitosamente en la base de datos",
        "usuarios": [
            {"email": test_user["email"], "roles": test_user["roles"]},
            {"email": test_admin["email"], "roles": test_admin["roles"]}
        ]
    }

@app.get("/health")
def health(x_gateway_auth_secret: str = Header(default="demo-instrospection-secret")):
    verify_gateway_secret(x_gateway_auth_secret)
    return {
        "status": "OK",
        "service": "Authentication & Clients Service"
    }

if __name__=="__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=9001)