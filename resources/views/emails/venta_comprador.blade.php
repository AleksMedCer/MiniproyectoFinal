<h2>Tu compra ha sido validada por el gerente</h2>
<p>Has comprado: <strong>{{ $venta->producto->nombre }}</strong></p>
<!-- REQUISITO: Correo del vendedor e instrucción de contacto -->
<p>El correo de tu vendedor es: <strong>{{ $venta->producto->vendedor->email }}</strong></p>
<p><strong>Instrucción de contacto:</strong> Por favor envía un correo a esta dirección indicando tu número de compra (#{{ $venta->id }}) para coordinar la entrega de tu producto.</p>