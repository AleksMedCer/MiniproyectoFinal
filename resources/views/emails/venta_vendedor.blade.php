<h2>Felicidades, se validó tu venta</h2>
<!-- REQUISITO: Producto vendido y datos del comprador -->
<p>Has vendido el producto: <strong>{{ $venta->producto->nombre }}</strong></p>
<p>Datos del comprador:</p>
<ul>
    <li>Nombre: {{ $venta->comprador->name }}</li>
    <li>Correo: {{ $venta->comprador->email }}</li>
</ul>
<p>Por favor ponte en contacto con él para la entrega.</p>