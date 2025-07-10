<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Solo para carga inicial
$sql = "SELECT p.id_producto, p.nombre, p.precio, p.stock, c.nombre_categoria 
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria";
$resultado = $conn->query($sql);

$categorias = $conn->query("SELECT * FROM categorias");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Productos</title>
    <link rel="stylesheet" href="/WebR/css/crudProductos.css">
    <!-- Solo añadimos estas librerías para AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
    <div class="contenedor">
        <!-- MENU (Mismo diseño que me pasaste) -->
        <div class="menu">
            <div class="menuIzquierda">
                <span class="title">Tienda TITISHOP</span> 
            </div>
            <div class="menuDerecha">
                <div class="menus">
                    <a href="listar.php">Productos</a>
                    <a href="../Clientes/listar.php">Clientes</a>
                    <a href="../Usuarios/listar.php">Usuarios</a>
                    <a href="../Usuarios/registroventas.php">Ventas</a>
                    <a href="../Usuarios/historial.php">Reportes</a>
                    <a href="../Usuarios/adminpag.php">Volver</a>
                </div>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL (Mismo diseño) -->
        <div class="contenido">
            <!-- Formulario Crear (Mismo diseño) -->
            <h2>Agregar Producto</h2>
            <div class="form-container">
                <form id="form-crear">
                    <input type="text" name="nombre" placeholder="Nombre del Producto" required>
                    <input type="number" step="0.01" name="precio" placeholder="Precio (S/.)" required>
                    <input type="number" name="stock" placeholder="Stock" required>
                    <select name="id_categoria" required>
                        <option value="">Seleccione Categoría</option>
                        <?php while ($categoria = $categorias->fetch_assoc()): ?>
                            <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['nombre_categoria'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit">Agregar Producto</button>
                </form>
            </div>

            <!-- Formulario Editar (oculto inicialmente) -->
            <div class="form-container" id="form-editar" style="display:none;">
                <h2>Editar Producto</h2>
                <form id="form-actualizar">
                    <input type="hidden" name="id_producto" id="edit-id">
                    <input type="text" name="nombre" id="edit-nombre" required>
                    <input type="number" step="0.01" name="precio" id="edit-precio" required>
                    <input type="number" name="stock" id="edit-stock" required>
                    <select name="id_categoria" id="edit-categoria" required>
                        <option value="">Seleccione Categoría</option>
                        <?php 
                            $categorias->data_seek(0); // Reiniciamos el puntero
                            while ($categoria = $categorias->fetch_assoc()): 
                        ?>
                            <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['nombre_categoria'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit">Actualizar Producto</button>
                    <button type="button" id="cancelar-edicion">Cancelar</button>
                </form>
            </div>

            <!-- Tabla de Productos (Mismo diseño) -->
            <h2>Listado de Productos</h2>
            <div class="tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio (S/.)</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr id="prod-<?= $fila['id_producto'] ?>">
                                    <td><?= $fila['id_producto'] ?></td>
                                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                    <td><?= number_format($fila['precio'], 2) ?></td>
                                    <td><?= $fila['stock'] ?></td>
                                    <td><?= $fila['nombre_categoria'] ?></td>
                                    <td>
                                        <button class="btn-editar" data-id="<?= $fila['id_producto'] ?>">Editar</button>
                                        <button class="btn-eliminar" data-id="<?= $fila['id_producto'] ?>">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No hay productos registrados aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript para AJAX (Lo único añadido) -->
    <script>
    $(document).ready(function() {
        const apiUrl = 'productos_ajax.php';
        
        // Crear Producto
        $('#form-crear').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: apiUrl,
                type: 'POST',
                data: $(this).serialize() + '&crear=1',
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        toastr.success(res.message);
                        $('#form-crear')[0].reset();
                        location.reload(); // Recargar para ver cambios
                    } else {
                        toastr.error(res.message);
                    }
                }
            });
        });

        // Cargar datos para editar
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            $.getJSON(apiUrl + '?obtener=' + id, function(res) {
                if(res.error) {
                    toastr.error(res.error);
                    return;
                }
                
                $('#edit-id').val(res.id_producto);
                $('#edit-nombre').val(res.nombre);
                $('#edit-precio').val(res.precio);
                $('#edit-stock').val(res.stock);
                $('#edit-categoria').val(res.id_categoria);
                
                $('#form-crear').hide();
                $('#form-editar').show();
            });
        });

        // Cancelar edición
        $('#cancelar-edicion').click(function() {
            $('#form-editar').hide();
            $('#form-crear').show();
        });

        // Actualizar Producto
        $('#form-actualizar').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: apiUrl,
                type: 'POST',
                data: $(this).serialize() + '&actualizar=1',
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        toastr.success(res.message);
                        $('#form-editar').hide();
                        $('#form-crear').show();
                        location.reload();
                    } else {
                        toastr.error(res.message);
                    }
                }
            });
        });

        // Eliminar Producto
        $(document).on('click', '.btn-eliminar', function() {
            if(!confirm('¿Estás seguro de eliminar este producto?')) return;
            
            const id = $(this).data('id');
            $.ajax({
                url: apiUrl + '?eliminar=' + id,
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        toastr.success(res.message);
                        $('#prod-' + id).remove();
                        // Si no quedan productos
                        if($('tbody tr').length === 1) {
                            $('tbody').html('<tr><td colspan="6">No hay productos registrados aún.</td></tr>');
                        }
                    } else {
                        toastr.error(res.message);
                    }
                }
            });
        });
    });
    </script>
</body>
</html>