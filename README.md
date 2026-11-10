# Informe de Arquitectura y Documentación Backend \- Apícola Dorada

**Caso de Negocio:** Tienda de miel “Apícola Dorada” – miel y derivados  
**Asignatura:** Desarrollo Web y Móvil  
**Backend:** FastAPI \+ MongoDB \+ API Gateway \+ GraphQL Gateway  
**Estudiante**: Mirela Rivero  
---

## Módulo 1: Servicios Derivados del Frontend

### 1.1 Matriz de Trazabilidad de Servicios

| ID Servicio | Requisito | Pantalla y Acción | Propósito / Operación | Verbo y Ruta | Entrada (Payload / Params) | Salida (JSON / Status) | Reglas de Negocio / Errores | Clasificación de Datos | Acceso |
| :---- | :---- | :---- | ----- | ----- | ----- | ----- | :---- | :---- | :---- |
| **S01** | REQ-CAT-01 | P02 productos.html \-\> Cargar catálogo | Consultar productos del catálogo con opción de filtrado | GET /api/v1/inventory/products | destacado: bool (Query optional), categoria: str (Query optional). | 200 OK \[Lista de ProductDTO\] | 400 Bad Request ante parámetros inválidos. Devuelve únicamente campos de catálogo | Público | Anónimo |
| **S02** | REQ-AUT-01 | P01 index.html / Modal Login \-\> Iniciar Sesión | Autenticar credenciales de cliente y retornar token JWT | POST /api/v1/auth/login | **Body:** { "username": str, "password": str } | 200 OK { "access\_token": str, "token\_type": "bearer" } | 401 Unauthorized si la contraseña hash no coincide o el correo no existe | Restringido / Credencial | Anónimo |
| **S03** | REQ-AUT-02 | API Gateway \-\> Introspección | Validar vigencia de token y recuperar identidad del usuario | POST /api/v1/auth/introspect | **Header:** X-Gateway-Auth-Secret\<br\>**Body:** { "token": str } | 200 OK { "active": true, "user\_id": str, "roles": list } | 401 Unauthorized si el token expiró o la firma es inválida. 403 si la firma del Gateway es errónea | Sensible (Tokens de sesión) | Privado (Gateway) |
| **S04** | REQ-ORD-01 | P03 checkout.html \-\> Procesar Compra | Registrar un nuevo pedido de compra con ítemes del carrito | POST /api/v1/orders/orders | **Header:** Authorization: Bearer \<token\>\<br\>**Body:** { "items": \[{ "product\_id": str, "cantidad": int }\] } | 201 Created { "order\_id": str, "total": float, "estado": "PENDIENTE" } | 400 Bad Request si no hay stock suficiente o el carrito está vacío. 401 si no hay sesión | Restringido (Historial / Ventas) | Autenticado (user) |
| **S05** | REQ-ORD-02 | P04 perfil.html \-\> Historial Pedidos | Consultar las compras realizadas por el usuario autenticado | GET /api/v1/orders/my-orders | **Header:** Authorization: Bearer \<token\> | 200 OK \[Lista de OrderDTO\] | 401 Unauthorized. Garantiza que un usuario no pueda ver pedidos ajenos | Restringido (Datos de Cliente) | Autenticado (user) |
| **S06** | REQ-INV-02 | Backend Admin \-\> Poblar Datos | Cargar datos semilla de productos en la base de datos | POST /api/v1/inventory/products/seed | **Header:** Authorization: Bearer \<token\> | 200 OK { "message": str, "inserted\_count": int } | 403 Forbidden si el rol del token no es admin | Interno | Autenticado (admin) |

### 1.2 Ejemplos de Contratos JSON (Ficticios)

#### Ejemplo S01: Respuesta GET /api/v1/inventory/products

  {  
    "id": "672f8a10b12e3f4a5b6c7d8e",  
    "nombre": "Miel de Ulmo Orgánica 500g",  
    "categoria": "Mieles",  
    "precio": 8500.0,  
    "stock": 42,  
    "destacado": true,  
    "imagen": "miel-ulmo.png"  
  }

#### Ejemplo S04: Petición POST /api/v1/orders/orders

{  
  "items": \[  
    {  
      "product\_id": "672f8a10b12e3f4a5b6c7d8e",  
      "cantidad": 2  
    }  
  \]  
}  
---

## Módulo 2: Clases y Cardinalidades del Dominio

### 2.1 Modelo UML de Clases y Servicios Públicos

![][image1]

### 2.2 Tabla de Relaciones y Reglas de Cardinalidad

| Clase Origen | Clase Destino | Multiplicidad | Justificación y Regla de Negocio |
| :---: | :---: | :---: | :---: |
| **Order** | **OrderItem** | 1 a 1..\* | Un pedido confirmado debe contener como mínimo un ítem de compra. No se permite crear órdenes vacías en la base de datos |
| **Client** | **Order** | 1 a 0..\* | Un cliente registrado puede haber realizado 0 pedidos (usuario nuevo) o múltiples pedidos a lo largo del tiempo. Cada pedido pertenece a un único cliente |
| **Product** | **OrderItem** | 1 a 0..\* | Un producto del catálogo puede estar presente en múltiples detalles de compra o en ninguno si aún no ha sido comercializado |

---

## Módulo 3: Sistemas e Integraciones

### 3.1 Matriz de Sistemas Mínimos Obligatorios

| Sistema / Microservicio | Responsabilidad Principal | Datos que Administra | Servicios Asociados | Dependencias e Integración | Credencial / Identidad | Respuesta ante Fallos |
| :---- | :---: | :---: | :---: | :---: | :---: | :---- |
| **inventory** | Control de stock, catálogo de productos y precios | Colección products (MongoDB) | S01, S06 | Ninguna (Microservicio Independiente). | X-Gateway-Secret | Si MongoDB cae, responde 503 Service Unavailable. |
| **orders** | Procesamiento del ciclo de vida del pedido y totales | Colección orders (MongoDB) | S04, S05 | Consulta síncrona a inventory para validar disponibilidad. | X-Gateway-Secret \+ X-Authenticated-User | Si inventory no responde, la orden rechaza el checkout con 502 Bad Gateway. |
| **clients / authclients** | Autenticación, generación de hash de contraseñas e introspección | Colección clients (MongoDB) | S02, S03 | Ninguna (Autoridad de Identidad). | X-Gateway-Auth-Secret | Si authclients cae, el Gateway deniega el acceso a rutas protegidas (502). |
| **payments** | Integración simulada con pasarela de pago externalizada | Estado de pago y transacciones | Integrado en orders | Recibe confirmación de pago. | Token de Transacción | En caso de timeout, la orden permanece en estado PENDIENTE\_PAGO. |

### 3.2 Diagrama de Contenedores de Arquitectura 

![][image2]

## Módulo 4: Stakeholders y Permisos HTTP

### 4.1 Registro de Stakeholders

| Stakeholder / Rol | Interés en el Sistema | Identidad Asociada | Alcance Operativo |
| :---: | :---: | :---: | :---: |
| **Visitante (Anónimo)** | Consultar el catálogo de productos y precios de la tienda. | Sin identidad (Guest) | Solo lectura de recursos públicos. |
| **Cliente Autenticado** | Crear órdenes de compra, gestionar su carrito y ver sus pedidos. | Token JWT valido (Rol user) | Operaciones sobre sus propios recursos. |
| **Administrador** | Gestionar catálogo, agregar productos y revisar métricas. | Token JWT valido (Rol admin) | Acceso total a rutas administrativas. |
| **API Gateway** | Validar tokens perimetralmente y reenviar contexto. | Identidad técnica perimetral | Redirección de trámites confiables. |

### 4.2 Matriz de Permisos por Verbo HTTP y Ruta

| Rol / Identidad | ID Servicio | Ruta Solicitada | Verbo HTTP | Decisión | Alcance / Condición de Seguridad | Lugar de Verificación |
| :---- | ----- | ----- | ----- | ----- | ----- | :---- |
| **Visitante** | S01 | /api/v1/inventory/products | GET | **PERMITIDO** | Solo consulta pública de productos. | API Gateway (Excepción implícita) |
| **Visitante** | S04 | /api/v1/orders/orders | POST | **DENEGADO** | Requiere autenticación de usuario (401). | API Gateway (authenticate\_client) |
| **Cliente (user)** | S04 | /api/v1/orders/orders | POST | **PERMITIDO** | Permitido. Registra orden a nombre del user\_id. | Orders Service \+ Gateway |
| **Cliente (user)** | S05 | /api/v1/orders/my-orders | GET | **PERMITIDO** | El backend filtra los pedidos donde user\_id \== token.user\_id. | Orders Service |
| **Cliente (user)** | S06 | /api/v1/inventory/products/seed | POST | **DENEGADO** | Retorna 403 Forbidden. Requiere rol admin. | Inventory Service |
| **Administrador (admin)** | S06 | /api/v1/inventory/products/seed | POST | **PERMITIDO** | Ejecución permitida para repoblar catálogo. | Inventory Service |

---

## Módulo 5: Clases del API Gateway y Políticas de Seguridad

### 5.1 Diagrama UML de Clases del API Gateway

![][image3]

### 5.2 Recorrido de Solicitudes y Fronteras de Confianza

1. **Frontera Externa (Pública):** El navegador o cliente envía peticiones HTTP a http\://localhost:9000/api/v1/....  
2. **Validación Perimetral:** El Gateway extrae la cabecera Authorization: Bearer \<token\>.  
3. **Introspección Privada:** El Gateway llama a http\://127.0.0.1:9001/introspect enviando X-Gateway-Auth-Secret. Si el token expiró, la solicitud es rechazada inmediatamente con 401 Unauthorized sin tocar los microservicios internos.  
4. **Propagación Confiable:** Si el token es válido, el Gateway adjunta las cabeceras internas sanitizadas X-Gateway-Secret, X-Authenticated-User y X-Authenticated-Roles antes de invocar los servicios backend.

---

## Módulo 6: Rutas y Secretos de Integración

### 6.1 Matriz de Rutas Proxy

| ID Gateway | Ruta / Verbo Externa | Acceso Requerido | Sistema y Ruta Destino Interna | Controles Aplicados | Credencial de Tramo Interno |
| :---- | :---: | :---: | :---: | :---: | :---- |
| **GW-01** | GET /api/v1/inventory/products | Anónimo | inventory:9002/products | Excepción pública en Gateway. | X-Gateway-Secret |
| **GW-02** | POST /api/v1/auth/login | Anónimo | authclients:9001/login | Redirección de credenciales. | X-Gateway-Secret |
| **GW-03** | POST /api/v1/orders/orders | Bearer Token | orders:9003/orders | Introspección previa en authclients. | X-Gateway-Secret \+ X-Authenticated-User |
| **GW-04** | GET /api/v1/orders/my-orders | Bearer Token | orders:9003/my-orders | Introspección y filtrado por usuario. | X-Gateway-Secret \+ X-Authenticated-User |

### 6.2 Inventario Simbólico de Secretos

| Referencia de Secreto | Tipo de Credencial | Emisor | Consumidor | Finalidad | Almacenamiento Seguro | Rotación / Revocación |
| :---- | :---: | :---: | :---: | :---: | :---: | :---- |
| AUTH\_INTROSPECTION\_SECRET | Secret Shared Key | Gestor de Secretos / Env | API Gateway (:9000) | Validar autenticidad de introspección con authclients. | .env local / HashiCorp Vault | Mensual / Modificación manual en .env |
| BACKEND\_SHARED\_SECRET | Secret Header Key | API Gateway (:9000) | Microservicios (:9002, :9003) | Garantizar que las llamadas provienen exclusivamente del Gateway. | .env local / HashiCorp Vault | Trimestral |

---

## Módulo 7: Datos Sensibles y Secretos del Cliente

### 7.1 Matriz de Clasificación y Protección de Datos

| Dato / Elemento | Finalidad del Dato | Clasificación | Sistema Custodio | Roles Autorizados | Representación en Frontend | Medida de Protección | Retención |
| :---- | :---- | :---: | :---: | :---: | :---: | :---- | :---- |
| **Contraseña** | Autenticación de usuario | Credencial Sensible | authclients | Ninguno (Solo comparación) | Oculta (\<input type="password"\>) | Hash adaptativo Bcrypt con sal automática. | Indefinida (Hasta actualización) |
| **Correo Electrónico** | Identificación de cuenta | Dato Personal | authclients | user (propio), admin | Texto plano en perfil | Validado mediante expresiones regulares. | Hasta eliminación de cuenta |
| **Historial de Compras** | Registro transaccional | Dato Restringido | orders | user (propio), admin | Tabla en dashboard de cliente | Filtrado estricto por X-Authenticated-User. | 5 años (Requisito legal) |

### 7.2 Decisión Sobre Secretos del Cliente

> **Decisión:** El sistema **NO** almacena ni custodia secretos o credenciales de pasarelas de pago del cliente en el código o base de datos. El proceso de pago se gestiona mediante tokens de transacción temporales.

---

## Módulo 8: Separación Privada y Agregaciones GraphQL

### 8.1 Schema GraphQL (backend/graphql-gateway/schema.js)

type Product {

  id: ID\!

  nombre: String\!

  precio: Float\!

  categoria: String\!

}

type OrderItem {

  product\_id: String\!

  cantidad: Int\!

  precio\_unitario: Float\!

  producto: Product

}

type OrderSummary {

  id: ID\!

  total: Float\!

  estado: String\!

  fecha: String\!

  items: \[OrderItem\!\]\!

}

type Query {

  orderSummary(order\_id: ID\!): OrderSummary

}

### 8.2 Resolvers de Agregación (backend/graphql-gateway/resolvers.js)

const resolvers \= {  
  Query: {  
    orderSummary: async (\_, { order\_id }, context) \=\> {  
      // 1\. Obtener orden desde microservicio de orders (:9003)  
      const orderRes \= await fetch(\`http\://127.0.0.1:9003/orders/\${order\_id}\`);  
      const order \= await orderRes.json();

      // 2\. Enriquecer cada item consultando el inventario (:9002)  
      const itemsEnriquecidos \= await Promise.all(  
        order.items.map(async (item) \=\> {  
          const prodRes \= await fetch(\`http\://127.0.0.1:9002/products/\${item.product\_id}\`);  
          const producto \= await prodRes.json();  
          return { ...item, producto };  
        })  
      );

      return { ...order, items: itemsEnriquecidos };  
    }  
  }  
};  
---

## Módulo 9: ADR (Architecture Decision Record) \- Analítica y gRPC

### ADR-001: Evaluación de gRPC y Servicios de Analítica en Tiempo Real

* **Estado:** Aceptado / Rechazado para esta fase.  
* **Contexto:** La pauta solicita evaluar la necesidad de incorporar gRPC y herramientas de Big Data para la tienda Apícola Dorada.  
* **Decisión:** Se determina que **NO APLICA** la implementación de gRPC ni motores de analítica distribuida (Big Data) en la fase actual del proyecto.  
* **Justificación Técnica:**  
  1. **Volumen de Carga Estimado:** La concurrencia proyectada para el comercio es inferior a 1,000 peticiones por minuto. HTTP/REST con FastAPI y ASGI (Uvicorn) ofrece tiempos de respuesta inferiores a 25ms ($p95$).  
  2. **Complejidad Innecesaria:** Implementar contratos Proto3 y canales gRPC agregaría complejidad de mantenimiento sin aportar beneficios medibles sobre JSON/HTTP en la red local.

---

## Módulo 10: Backend Completo e Integración Verificable

### 10.1 Guía de Inicio y Despliegue Local

#### Paso 1: Clonar e instalar dependencias

git clone https\://github.com/tu-usuario/entrega-1-desarrollo-web.git  
cd entrega-1-desarrollo-web

#### Paso 2: Levantar Microservicios en Terminales Separadas

**Terminal 1 \- Auth Service (Puerto 9001):**

cd backend/services/authclients  
pip install \-r requirements.txt  
python authclients.py  
**Terminal 2 \- Inventory Service (Puerto 9002):**

cd backend/services/inventory  
pip install \-r requirements.txt  
python inventory.py  
**Terminal 3 \- Orders Service (Puerto 9003):**

cd backend/services/orders  
pip install \-r requirements.txt  
python orders.py  
**Terminal 4 \- API Gateway (Puerto 9000):**

cd backend/apigateway  
pip install \-r requirements.txt  
python gateway.py  
---

### 10.2 Evidencias de Pruebas Ejecutadas (Matriz de Verificación)

| Requisito | Escenario de Prueba | Entrada de Prueba | Resultado Esperado | Resultado Obtenido | Estado |
| :---- | :---: | :---: | :---: | :---: | :---- |
| **REQ-CAT-01** | Lectura pública del catálogo | GET http\://localhost:9000/api/v1/inventory/products | 200 OK con JSON de productos. | 200 OK JSON con catálogo completo. | **APROBADO** |
| **REQ-AUT-01** | Login con credenciales válidas | POST /api/v1/auth/login con datos correctos. | Retorna Token Bearer. | 200 OK { "access\_token": "..." } | **APROBADO** |
| **REQ-AUT-02** | Acceso a orden sin Bearer token | POST /api/v1/orders/orders sin cabecera Auth. | 401 Unauthorized | 401 {"detail": "Bearer token requerido"} | **APROBADO** |
| **REQ-ORD-01** | Creación de orden con Token activo | POST /api/v1/orders/orders con Bearer Token. | 201 Created con ID de orden. | 201 Created Orden registrada en MongoDB. | **APROBADO** |

Evidencia 1:

![][image4]

Evidencia 2:

![][image5]

![][image6]

Evidencia 3: 

![][image7]

Evidencia 4:

![][image8]


