# 📊 Gráficos CRUD Dinámicos en PHP

Una aplicación web moderna desarrollada en **PHP** que permite a los usuarios autenticarse, gestionar y visualizar gráficos dinámicos utilizando una variedad de tipos (barra, líneas, pastel, anillo, gauge, etc.), todo con un diseño responsivo y moderno.

## 🧩 Características

- ✅ Registro e inicio de sesión con validación de usuarios
- 📈 Visualización de gráficos con datos dinámicos
- 🧠 Generación de gráficos con `SVG` desde JavaScript (sin librerías externas)
- 🎨 Estilo moderno y responsivo usando `CSS` personalizado
- 🧊 Efectos 3D y diseño adaptable a dispositivos móviles
- 🔒 Protección básica mediante autenticación de usuarios
- ⚙️ Funcionalidad CRUD para gráficos

## 📁 Estructura de Carpetas

```
project/
│
├── css/
│   ├── estilo.css         # Estilos principales de la app
│   └── auth.css           # Estilos específicos para login y registro
│
├── js/
│   └── charts.js          # Renderizado dinámico de gráficos
│
├── php/
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── add_chart.php
│   ├── charts.php         # Vista principal de gráficos
│   └── data/
│       └── fetch_chart_data.php  # Devuelve datos JSON para los gráficos
│
├── img/
│   └── logo.png           # Logo de la aplicación
│
└── index.php              # Página de inicio o redirección
```

## 🔧 Tecnologías Utilizadas

- PHP puro
- HTML5 / CSS3 (con `backdrop-filter`, gradientes, y efectos 3D)
- JavaScript vanilla (ES6+)
- `fetch()` para consumo de datos en tiempo real
- Fuentes personalizadas: [Ubuntu](https://static.jocarsa.com/fuentes/ubuntu-font-family-0.83/ubuntu.css)

## 🖼️ Tipos de Gráficos Soportados

- 📊 Barra
- 📈 Línea
- 🥧 Pastel
- 🧿 Anillo
- 🧱 Barra apilada
- 🎯 Indicador radial (Gauge)

## 🚀 Instalación

1. Clona el repositorio:

```bash
git clone https://github.com/Andre1z/andrei-blue.git
```

2. Copia en tu servidor local (XAMPP, Laragon, etc.)

3. Asegúrate de que el servidor tenga activado `PHP` y acceso a sesiones.

4. Abre en el navegador:  
   `http://localhost/andrei-blue/php/login.php`

## 🔐 Usuarios

Puedes registrar un nuevo usuario desde `/register.php`. La base de datos puede estar implementada en archivos JSON o SQL, según tu necesidad.

## ✨ Personalización

- Edita los estilos en `css/estilo.css` y `css/auth.css`
- Añade nuevos tipos de gráficos en `js/charts.js`
- Ajusta diseño o layout en los archivos PHP correspondientes