# Sistema de Gestión de Recursos Educativos

## Descripción
Este proyecto es una plataforma web para la gestión y distribución de recursos educativos digitales. Está diseñado para facilitar el acceso a materiales educativos, libros y guías pedagógicas en formato digital.

## Características Principales

- Sistema de gestión de libros y recursos educativos
- Interfaz de usuario amigable y moderna
- Sistema de categorización por áreas temáticas
- Soporte para archivos PDF y portadas
- Enlaces directos a recursos digitales
- Sistema de control de versiones
- Gestión de sesiones de usuario

## Estructura del Proyecto

```
new-fd/
├── assets/          # Archivos estáticos (imágenes, PDFs)
├── controller/      # Controladores de la aplicación
├── css/            # Archivos CSS
├── js/             # Archivos JavaScript
├── model/          # Modelos de datos
├── view/           # Vistas de la aplicación
├── db.sql          # Estructura de la base de datos
└── index.php       # Punto de entrada principal
```

## Requisitos del Sistema

- PHP 8.0 o superior
- MySQL/MariaDB 10.4 o superior
- Servidor web (Apache/Nginx)
- Base de datos configurada según db.sql

## Instalación

1. Clonar el repositorio
2. Importar la base de datos desde `db.sql`
3. Configurar las credenciales de la base de datos en `model/database.php`
4. Configurar el servidor web para apuntar al directorio raíz
5. Asegurarse de que el directorio `assets/temp_uploads` tenga permisos de escritura

## Base de Datos

La base de datos incluye una estructura para almacenar:
- Información de libros y recursos
- Metadatos (título, autor, año)
- Categorías y temas
- Enlaces a recursos digitales
- Archivos adjuntos (PDFs, portadas)

## Contribución

Para contribuir al proyecto:
1. Haz un fork del repositorio
2. Crea una rama para tu funcionalidad (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Añadir alguna funcionalidad increíble'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la licencia MIT - consulta el archivo LICENSE para más detalles.

## Contacto

Para soporte o preguntas, por favor contactar a través del repositorio o correo electrónico.
