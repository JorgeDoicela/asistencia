<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Asistencias - ISTPET</title>
    <style>
        :root {
            --azul: #2C356D;
            --dorado: #B79B4A;
            --gris: #f4f4f4;
            --blanco: #ffffff;
            --texto-oscuro: #333333;
            --rojo-cerrar: #c0392b;
            --verde-exito: #27ae60;
            --rojo-falta: #e74c3c;
            --amarillo-atraso: #f39c12;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: var(--gris);
            color: var(--texto-oscuro);
        }

        /* BARRA DE NAVEGACIÓN MODIFICADA */
        .navbar {
            background: var(--azul);
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            padding: 10px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .navbar .logo-container {
            display: flex;
            align-items: center;
        }

        .navbar .logo-container img {
            height: 50px;
            width: auto;
            object-fit: contain;
            background: white;
            padding: 3px;
            border-radius: 4px;
        }

        .navbar .brand {
            color: var(--blanco);
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .navbar nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .navbar nav span {
            color: var(--dorado);
            font-weight: 600;
            padding: 10px 20px;
            font-size: 15px;
        }

        .navbar nav a.salir {
            color: var(--blanco);
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            transition: .3s;
            font-size: 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .navbar nav a.salir:hover {
            background: var(--rojo-cerrar);
        }

        /* LOGO EXCLUSIVO PARA IMPRESIÓN (OCULTO EN NAVEGADOR) */
        .print-header {
            display: none;
        }

        /* CONTENEDOR PRINCIPAL */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* CARD PRINCIPAL Y FILTROS */
        .card {
            background: var(--blanco);
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-top: 4px solid var(--azul);
        }

        .card h2 {
            color: var(--azul);
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--gris);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* BARRA DE FILTROS */
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--azul);
        }

        .filter-group select, 
        .filter-group input {
            padding: 9px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
            background: var(--blanco);
        }

        .filter-group select:focus, 
        .filter-group input:focus {
            border-color: var(--azul);
        }

        .btn-filter {
            background: var(--azul);
            color: var(--blanco);
            border: none;
            padding: 9px 15px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: flex-end;
        }

        .btn-filter:hover {
            background: var(--dorado);
            color: var(--azul);
        }

        /* BOTÓN EXPORTAR PDF */
        .btn-pdf {
            background: #c0392b;
            color: var(--blanco);
            border: none;
            padding: 9px 15px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: flex-end;
        }

        .btn-pdf:hover {
            background: #a93226;
        }

        /* TABLA DE ASISTENCIAS */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th {
            background: var(--azul);
            color: var(--blanco);
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        table tr:hover {
            background: #f5f5f5;
        }

        /* BADGES / ETIQUETAS DE ESTADO */
        .badge {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }

        .badge-presente { background: #d4edda; color: #155724; }
        .badge-atraso { background: #fff3cd; color: #856404; }
        .badge-falta { background: #f8d7da; color: #721c24; }

        /* ESTILOS OPTIMIZADOS PARA IMPRESIÓN A PDF */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            /* 1. Ocultar elementos innecesarios */
            .navbar, 
            .filters-grid, 
            .salir, 
            .btn-pdf, 
            .btn-filter {
                display: none !important;
            }

            /* 2. Mostrar Encabezado con Logo en el PDF */
            .print-header {
                display: flex !important;
                align-items: center;
                justify-content: center;
                gap: 15px;
                border-bottom: 2px solid #2C356D;
                padding-bottom: 8px;
                margin-bottom: 12px;
            }

            .print-header img {
                height: 45px;
                width: auto;
            }

            .print-header-text {
                font-size: 9pt;
                font-weight: bold;
                color: #2C356D;
                white-space: nowrap;
                letter-spacing: -0.2px;
            }

            /* 3. Ajuste general de la página */
            body {
                background: #ffffff !important;
                color: #111111 !important;
                font-size: 10pt;
            }

            .container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                gap: 10px !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                background: transparent !important;
            }

            .card h2 {
                border-bottom: 1px solid #ccc;
                padding-bottom: 4px;
                font-size: 11pt;
                color: #333;
                margin-bottom: 10px;
            }

            /* 4. Tabla adaptada para impresión */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 9.5pt !important;
            }

            table th {
                background-color: #2C356D !important;
                color: #ffffff !important;
                border: 1px solid #2C356D !important;
                padding: 5px 8px !important;
            }

            table td {
                border-bottom: 1px solid #ddd !important;
                padding: 5px 8px !important;
                color: #222 !important;
            }

            /* 5. Badges legibles */
            .badge {
                border: 1px solid #999;
                background: #f0f0f0 !important;
                color: #000 !important;
                padding: 2px 5px;
                font-size: 8.5pt;
            }
        }

        @media(max-width: 768px) {
            .navbar {
                grid-template-columns: 1fr;
                gap: 10px;
                text-align: center;
            }
            .navbar .logo-container {
                justify-content: center;
            }
            .navbar nav {
                justify-content: center;
            }
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo-container">
        <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg" alt="Logo ISTPET">
    </div>
    
    <div class="brand">Asistencia ISTPET</div>
    
    <nav>
        <span>Juan Pérez (EST-2026)</span>
        <a href="#" class="salir">Salir</a>
    </nav>
</div>

<div class="container">

    <!-- ENCABEZADO PARA IMPRESIÓN (PDF) CON LOGO -->
    <div class="print-header">
        <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg" alt="Logo ISTPET">
        <div class="print-header-text">
            INSTITUTO SUPERIOR TECNOLÓGICO MAYOR PEDRO TRAVERSARI - REPORTE DE ASISTENCIAS
        </div>
    </div>

    <!-- SECCIÓN PRINCIPAL DE TABLA CON FILTROS Y BOTÓN DE PDF -->
    <div class="card">
        <h2>Historial de Asistencias</h2>

        <!-- FILTROS DE BÚSQUEDA -->
        <form class="filters-grid" onsubmit="return false;">
            <div class="filter-group">
                <label>Materia</label>
                <select>
                    <option value="">Todas las materias</option>
                    <option value="matematicas">Matemáticas III</option>
                    <option value="programacion">Programación Web</option>
                    <option value="bd">Base de Datos</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Estado</label>
                <select>
                    <option value="">Todos los estados</option>
                    <option value="presente">Presente</option>
                    <option value="atraso">Atraso</option>
                    <option value="falta">Falta</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Desde</label>
                <input type="date" value="2026-07-01">
            </div>

            <div class="filter-group">
                <label>Hasta</label>
                <input type="date" value="2026-07-14">
            </div>

            <button type="button" class="btn-filter">
                Filtrar
            </button>

            <button type="button" class="btn-pdf" onclick="window.print()">
                PDF
            </button>
        </form>

        <!-- TABLA DE RESULTADOS -->
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026-07-14</td>
                        <td>08:15 AM</td>
                        <td><strong>Matemáticas III</strong></td>
                        <td>Ing. Carlos Mendoza</td>
                        <td><span class="badge badge-presente">Presente</span></td>
                    </tr>
                    <tr>
                        <td>2026-07-13</td>
                        <td>10:18 AM</td>
                        <td><strong>Programación Web</strong></td>
                        <td>Lic. Ana Torres</td>
                        <td><span class="badge badge-atraso">Atraso</span></td>
                    </tr>
                    <tr>
                        <td>2026-07-11</td>
                        <td>08:02 AM</td>
                        <td><strong>Base de Datos</strong></td>
                        <td>Ing. Roberto Gómez</td>
                        <td><span class="badge badge-presente">Presente</span></td>
                    </tr>
                    <tr>
                        <td>2026-07-09</td>
                        <td>-- : --</td>
                        <td><strong>Matemáticas III</strong></td>
                        <td>Ing. Carlos Mendoza</td>
                        <td><span class="badge badge-falta">Falta</span></td>
                    </tr>
                    <tr>
                        <td>2026-07-08</td>
                        <td>10:04 AM</td>
                        <td><strong>Programación Web</strong></td>
                        <td>Lic. Ana Torres</td>
                        <td><span class="badge badge-presente">Presente</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>