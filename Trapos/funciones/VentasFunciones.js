
  
        let carrito = [];

        function agregarAlCarrito() {
            const select = document.getElementById("select-producto");
            const inputCant = document.getElementById("input-cantidad");
            
            const id_producto = select.value;
            if(!id_producto) return alert("Por favor, selecciona un producto.");

            const option = select.options[select.selectedIndex];
            const nombre = option.getAttribute("data-nombre");
            const precio = parseFloat(option.getAttribute("data-precio"));
            const stockMax = parseInt(option.getAttribute("data-stock"));
            const cantidad = parseInt(inputCant.value);

            if(cantidad <= 0) return alert("La cantidad debe ser mayor a 0.");
            
            // Verificar si el producto ya está en el carrito para sumar la cantidad
            const existe = carrito.find(item => item.id_producto === id_producto);
            const cantidadTotal = existe ? (existe.cantidad + cantidad) : cantidad;

            if(cantidadTotal > stockMax) {
                return alert(`No puedes agregar esa cantidad. El stock disponible es de ${stockMax} piezas.`);
            }

            if(existe) {
                existe.cantidad = cantidadTotal;
                existe.subtotal = existe.cantidad * existe.precio;
            } else {
                carrito.push({
                    id_producto: id_producto,
                    nombre: nombre,
                    precio: precio,
                    cantidad: cantidad,
                    subtotal: precio * cantidad
                });
            }

            // Resetear cantidad a 1
            inputCant.value = "1";
            renderCarrito();
        }

        function eliminarDelCarrito(id_producto) {
            carrito = carrito.filter(item => item.id_producto !== id_producto);
            renderCarrito();
        }

        function renderCarrito() {
            const tbody = document.querySelector("#tabla-carrito tbody");
            tbody.innerHTML = "";
            let total = 0;

            carrito.forEach(item => {
                total += item.subtotal;
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${item.nombre}</td>
                    <td>$${item.precio.toFixed(2)}</td>
                    <td>${item.cantidad}</td>
                    <td>$${item.subtotal.toFixed(2)}</td>
                    <td><button type="button" class="btn-eliminar" onclick="eliminarDelCarrito('${item.id_producto}')"><i class="ri-delete-bin-6-line"></i></button></td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById("txt-total").textContent = `$${total.toFixed(2)}`;
            document.getElementById("total_venta").value = total;
        }

        function prepararEnvio() {
            if(carrito.length === 0) {
                alert("El carrito está vacío. Añade al menos una prenda para cobrar.");
                return false;
            }
            // Guardamos el array como texto JSON en el input hidden para enviárselo completo a PHP
            document.getElementById("carrito_json").value = JSON.stringify(carrito);
            return true;
        }
    