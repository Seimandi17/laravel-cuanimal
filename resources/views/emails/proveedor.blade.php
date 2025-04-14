<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 40px 0;
    }
    .email-container {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      overflow: hidden;
      padding: 30px;
    }
    h2 {
      color: #333333;
      font-size: 24px;
      margin-bottom: 20px;
    }
    p {
      font-size: 15px;
      color: #555555;
      line-height: 1.6;
      margin: 0 0 15px;
    }
    strong {
      color: #222222;
    }
    .highlight {
      background: #f9f9f9;
      padding: 10px;
      border-left: 4px solid #007BFF;
      margin-top: 10px;
    }
    .footer {
      margin-top: 40px;
      font-size: 12px;
      color: #999999;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <h2>📨 Nueva solicitud para ser proveedor</h2>

    <p><strong>Nombre completo:</strong> {{ $data['nombre'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Teléfono:</strong> {{ $data['telefono'] ?? 'No especificado' }}</p>
    <p><strong>Tipo de servicio:</strong> {{ $data['servicio'] }}</p>
    <p><strong>Ubicación:</strong> {{ $data['ubicacion'] }}</p>

    <p><strong>Mensaje:</strong></p>
    <div class="highlight">
      {{ $data['mensaje'] }}
    </div>

    <div class="footer">
      Este mensaje fue enviado desde el sitio web de Cuanimal.
    </div>
  </div>
</body>
</html>
