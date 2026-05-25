<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Compra - {{ $ticket_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0;
            color: #64748b;
        }
        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-section td {
            vertical-align: top;
            width: 50%;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .info-box h3 {
            margin: 0 0 10px;
            font-size: 14px;
            text-transform: uppercase;
            color: #475569;
        }
        .info-box p {
            margin: 0 0 5px;
            font-size: 14px;
            color: #1e293b;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .table td {
            color: #1e293b;
            font-size: 14px;
        }
        .table .text-right {
            text-align: right;
        }
        .table .text-center {
            text-align: center;
        }
        .totals {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 10px 12px;
            text-align: right;
            font-size: 14px;
        }
        .totals .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #e2e8f0;
            padding-top: 15px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-warning { background-color: #fef9c3; color: #854d0e; }
    </style>
</head>
<body>

    <div class="header">
        <h1>MarketPro</h1>
        <p>Comprobante de Transacción</p>
    </div>

    <table class="info-section">
        <tr>
            <td style="padding-right: 15px;">
                <div class="info-box">
                    <h3>Datos del Cliente</h3>
                    <p><strong>Nombre:</strong> {{ $comprador->name }}</p>
                    <p><strong>Email:</strong> {{ $comprador->email }}</p>
                </div>
            </td>
            <td style="padding-left: 15px;">
                <div class="info-box">
                    <h3>Detalles del Pedido</h3>
                    <p><strong>Ref Ticket:</strong> {{ substr($ticket_id, 0, 15) }}...</p>
                    <p><strong>Fecha:</strong> {{ $fecha->format('d/m/Y H:i:s') }}</p>
                    @php $todosValidados = $ventas->every('validada'); @endphp
                    <p><strong>Estado:</strong> 
                        @if($todosValidados)
                            <span class="badge badge-success">Validada</span>
                        @else
                            <span class="badge badge-warning">Pendiente de Validación</span>
                        @endif
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="45%">Producto</th>
                <th width="25%">Vendedor</th>
                <th width="15%" class="text-center">Estado</th>
                <th width="15%" class="text-right">Precio</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($ventas as $venta)
                @php $total += $venta->producto->precio; @endphp
                <tr>
                    <td>
                        <strong>{{ $venta->producto->nombre }}</strong>
                    </td>
                    <td>{{ $venta->producto->vendedor->name }}</td>
                    <td class="text-center">
                        @if($venta->validada)
                            <span style="color: #166534; font-size: 12px; font-weight: bold;">Validada</span>
                        @else
                            <span style="color: #854d0e; font-size: 12px; font-weight: bold;">Pendiente</span>
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($venta->producto->precio, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td width="70%">Subtotal</td>
            <td width="30%">${{ number_format($total, 2) }}</td>
        </tr>
        <tr>
            <td class="grand-total">Total</td>
            <td class="grand-total">${{ number_format($total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Gracias por tu compra en MarketPro.</p>
        <p>Si tienes alguna pregunta o problema con tu pedido, por favor contáctanos con tu referencia de ticket.</p>
    </div>

</body>
</html>
