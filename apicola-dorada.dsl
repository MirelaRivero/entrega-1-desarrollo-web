workspace "Sistema Apícola Dorada" "Arquitectura de Microservicios en Python (FastAPI) y MongoDB" {

    !identifiers hierarchical

    model {
        user = person "Cliente / Frontend Web" "Usuario final autenticado o navegante anónimo." "Person"

        system = softwareSystem "Plataforma Apícola Dorada" "Sistema e-commerce basado en microservicios." {
            
            # Layer 1: API Gateway
            gateway = container "API Gateway" "Punto único de entrada. Proxy inverso, enrutamiento y verificación de seguridad." "Python / FastAPI (:9000)"

            # Layer 2: Core Microservices
            authService = container "Auth & Clients Service" "Gestión de usuarios, hashing de contraseñas (bcrypt) e introspección de tokens de sesión." "Python / FastAPI (:9001)"
            inventoryService = container "Inventory Service" "Gestión del catálogo de productos y stock de la tienda." "Python / FastAPI (:9002)"
            ordersService = container "Orders Service" "Procesamiento de compras, cálculo de subtotales y estado de pedidos." "Python / FastAPI (:9003)"

            # Layer 3: Database & Schemas
            database = container "Base de Datos MongoDB" "Almacenamiento no relacional distribuido por colecciones." "MongoDB" "Database" {
                clientsCol = component "Colección 'clients'" "Documentos de usuarios, credenciales hash y roles." "MongoDB Collection"
                productsCol = component "Colección 'products'" "Catálogo de productos, stock, precios y destacados." "MongoDB Collection"
                ordersCol = component "Colección 'orders'" "Historial de pedidos, items comprados y totales." "MongoDB Collection"
            }
        }

        # --- RELACIONES DEL SISTEMA (Flujos y Secuencias) ---
        
        # 1. Peticiones de Usuario a Gateway
        user -> system.gateway "Realiza peticiones HTTP / HTTPS (Auth, Catálogo, Pedidos)" "JSON / HTTP"

        # 2. Introspección y Autenticación del Gateway
        system.gateway -> system.authService "1. Introspecciona Bearer Token vía POST /introspect" "HTTP JSON (Header: X-Gateway-Auth-Secret)"

        # 3. Enrutamiento Proxy (Gateway a Microservicios)
        system.gateway -> system.authService "Forward POST /login" "HTTP JSON"
        system.gateway -> system.inventoryService "Forward GET /products (Lectura Pública / Protegida)" "HTTP JSON (Headers: X-Gateway-Secret, X-Authenticated-User)"
        system.gateway -> system.ordersService "Forward POST/GET /orders" "HTTP JSON (Headers: X-Gateway-Secret, X-Authenticated-User)"

        # 4. Acceso a Datos (Microservicios a Colecciones MongoDB)
        system.authService -> system.database.clientsCol "Consulta y valida usuarios (find_one)" "PyMongo"
        system.inventoryService -> system.database.productsCol "Consulta catálogo y filtra destacados (find)" "PyMongo"
        system.ordersService -> system.database.ordersCol "Registra pedidos y consulta historial (insert_one / find)" "PyMongo"
    }

    views {
        # Vista 1: Nivel de Contenedores (Componentes y Puertos)
        container system "Diagrama1_Componentes_y_Microservicios" {
            include *
            autoLayout lr
        }

        # Vista 2: Enfoque en Datos y Colecciones MongoDB
        component system.database "Diagrama3_Modelos_de_Datos_MongoDB" {
            include *
            autoLayout tb
        }

        # Estilos Visuales
        styles {
            element "Element" {
                color #ffffff
                stroke #2b2b2b
                strokeWidth 2
                shape roundedbox
            }
            element "Person" {
                background #1b365d
                color #ffffff
                shape person
            }
            element "Container" {
                background #007acc
                color #ffffff
            }
            element "Database" {
                background #47a248
                color #ffffff
                shape cylinder
            }
            element "Component" {
                background #2e7d32
                color #ffffff
                shape folder
            }
            relationship "Relationship" {
                thickness 3
                color #555555
                fontSize 12
            }
        }
    }

    configuration {
        scope softwaresystem
    }
}