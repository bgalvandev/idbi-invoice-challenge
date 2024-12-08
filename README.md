# IDBI Invoice Recorder Challenge

API REST para registro y consulta de comprobantes XML, con extracción de información relevante y autenticación JWT.

## Tecnologías

-   PHP
-   Nginx (servidor web)
-   MySQL (base de datos)
-   MailHog (gestión de envío de correos)

## Instalación

El proyecto cuenta con una implementación de Docker Compose para facilitar la configuración del entorno de desarrollo.

> ⚠️ Si no estás familiarizado con Docker, puedes optar por otra configuración para preparar tu entorno. Si decides
> hacerlo, omite los pasos 1 y 2.

Instrucciones:

1. Levantar los contenedor web con Docker:

```bash
docker compose up -d
```

2. Acceder al contenedor web:

```bash
docker exec -it idbi-invoice-recorder-challenge-web-1 bash
```

3. Configurar las variables de entorno:

```bash
cp .env.example .env
JWT_SECRET=<random_string>
```

5. Instalar las dependencias del proyecto:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## Acceso

-   API: http://localhost:8080/api/v1
-   MailHog: http://localhost:8025

## Endpoints

1. **Iniciar Sesión**  
   **POST** `/api/v1/login`  
   **Request**:

    ```json
    {
        "email": "usuario@correo.com",
        "password": "contraseña"
    }
    ```

    **Response**:

    ```json
    {
        "data": {
            "token": "jwt_token",
            "user": {
                "id": "user_id",
                "name": "Nombre",
                "email": "usuario@correo.com"
            }
        }
    }
    ```

2. **Registro de Usuario**  
   **POST** `/api/v1/users`  
   **Request**:

    ```json
    {
        "name": "Nombre",
        "last_name": "Apellido",
        "email": "usuario@correo.com",
        "password": "contraseña"
    }
    ```

    **Response**:

    ```json
    {
        "data": {
            "id": "user_id",
            "name": "Nombre",
            "last_name": "Nombre",
            "email": "usuario@correo.com"
        }
    }
    ```

3. **Cerrar Sesión**  
   **POST** `/api/v1/logout`  
   **Response**:
    ```json
    {
        "message": "Cierre de sesión exitoso"
    }
    ```

### Gestión de Comprobantes

1. **Registro de Comprobantes**  
   **POST** `/api/v1/vouchers`  
   **Subida de Archivos XML**  
   **Response**:

    ```json
    {
        "message": "Comprobantes en proceso de registro. Recibirá un correo con el resumen.",
        "files_processed": 20
    }
    ```

    > ⚠️ De ser necesario ejecuta "php artisan queue:work" para forzar la cola de registros.

2. **Consulta de Comprobantes**  
   **GET** `/api/v1/vouchers`  
   Filtros Opcionales: `type`, `serie`, `number`, `currency`  
   Obligatorio: `start_date`, `end_date`  
   **Response**:

    ```json
    {
        "data": [...],
        "meta": {
            "current_page": 1,
            "per_page": 14,
            "total": 2
        }
    }
    ```

3. **Eliminación de Comprobantes**  
   **DELETE** `/api/v1/vouchers/{id}`  
   **Response**:

    ```json
    {
        "message": "Comprobante eliminado exitosamente."
    }
    ```

4. **Consulta de Totales por Moneda**  
   **GET** `/api/v1/vouchers/totalAmounts`  
   **Response**:

    ```json
    {
        "user": {
            "id": "user_id",
            "name": "Nombre"
        },
        "data": [
            { "currency": "USD", "total_amount": 14549.62 },
            { "currency": "PEN", "total_amount": 22517.33 }
        ]
    }
    ```

5. **Regularización de Comprobantes**  
   **POST** `/api/v1/vouchers/regularize`  
   **Response**:
    ```json
    {
        "data": {
            "processed": {
                "total": 5030,
                "regularized": 5000,
                "failed": 30
            }
        }
    }
    ```
