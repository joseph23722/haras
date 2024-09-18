<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestionar Kardex</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Registrar entrada o salida de productos</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            Complete los datos
        </div>
        <div class="card-body">
            <form action="" id="form-kardex" autocomplete="off">
                <div class="row g-2">
                    <div class="col-md mb-2">
                        <div class="form-floating">
                            <input type="number" name="idproducto" id="idproducto" class="form-control" required>
                            <label for="idproducto">ID del Producto</label>
                        </div>
                    </div>

                    <div class="col-md mb-2">
                        <div class="form-floating">
                            <input type="number" name="cantidad" id="cantidad" class="form-control" required>
                            <label for="cantidad">Cantidad</label>
                        </div>
                    </div>

                    <div class="col-md mb-2">
                        <div class="form-floating">
                            <select name="operation" id="operation" class="form-select" required>
                                <option value="">Seleccione Operación</option>
                                <option value="registrarEntrada">Entrada</option>
                                <option value="registrarSalida">Salida</option>
                            </select>
                            <label for="operation">Operación</label>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-sm" id="registrar-movimiento">Registrar Movimiento</button>
                    <button type="reset" class="btn btn-secondary btn-sm">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Productos Registrados
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Stock</th>
                        <th>Tipo</th>
                        <th>Marca</th>
                    </tr>
                </thead>
                <tbody id="productos-table">
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.querySelector("#form-kardex");
        const productosTable = document.querySelector("#productos-table");

        const loadProducts = async () => {
            try {
                const response = await fetch('../../controllers/kardex.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getAllProducts' })
                });
                if (!response.ok) throw new Error('Error al cargar los productos');
                const products = await response.json();
                productosTable.innerHTML = products.map(product => `
                    <tr>
                        <td>${product.idproducto}</td>
                        <td>${product.nombreproducto}</td>
                        <td>${product.descripcion}</td>
                        <td>${product.stock}</td>
                        <td>${product.tipoproducto}</td>
                        <td>${product.marca}</td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Error:', error);
            }
        };

        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const data = new URLSearchParams(formData);

            const options = {
                method: "POST",
                body: data
            };

            try {
                const response = await fetch('../../controllers/kardex.controller.php', options);
                if (!response.ok) throw new Error('Error al registrar el movimiento');
                const result = await response.json();

                if (result.status === "success") {
                    alert(result.message);
                    form.reset();
                    loadProducts();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert("Error en la solicitud: " + error.message);
                console.error('Error:', error);
            }
        });

        loadProducts();
    });
</script>
