<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Ficha de reparación</title>
    <link rel="icon" type="image/jpeg" href="assets/Runa.jpg" />

  <style>
    body { 
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 700px;
      margin: 40px auto;
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    header {
      text-align: center;
      margin-bottom: 20px;
    }
    header img {
      max-height: 80px;
    }
    header h1 {
      color: #0a7373;
      font-size: 28px;
      margin: 15px 0 0;
    }
    form label {
      display: block;
      margin-top: 20px;
      font-weight: bold;
      color: #333;
    }
    form input[type="text"],
    form input[type="tel"],
    form input[type="date"],
    form textarea {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
      font-size: 14px;
    }
    form small {
      color: #555;
      display: block;
      margin-top: 5px;
      font-size: 12px;
    }
    .accesorios div {
      margin-top: 8px;
    }
    .accesorios label {
      font-weight: normal;
    }
    button {
      margin-top: 30px;
      background: #0a7373;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background: #084b4b;
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <img src="assets/DGI_LOGO_TRANSP.png" alt="Logo DGI">
      <h1>Ficha de reparación</h1>
    </header>
    <form action="procesar.php" method="post" id="formReparacion">
      <label for="nombre">Nombre del cliente:</label>
      <input type="text" id="nombre" name="nombre" required />

      <label for="email">Correo electrónico:</label>
      <input type="text" id="email" name="email" />

      <label for="telefono">Teléfono:</label>
      <input type="tel"
             id="telefono"
             name="telefono"
             required
             title="Introduce 9 dígitos"
      />

      <label for="marca">Marca y modelo:</label>
      <input type="text" id="marca" name="marca" />

      <label for="contrasena">Contraseña:</label>
      <input type="text" id="contrasena" name="contrasena" />
      <small>Si la reparación es de un PC hay que pedirle la contraseña al cliente.</small>

      <label>Accesorios entregados:</label>
      <div class="accesorios">
        <label><input type="checkbox" name="accesorios[]" value="Cargador"> Cargador</label><br />
        <label><input type="checkbox" name="accesorios[]" value="Cable cargador"> Cable cargador</label><br />
        <label><input type="checkbox" name="accesorios[]" value="Batería"> Batería</label><br />
        <label><input type="checkbox" name="accesorios[]" value="Maletín"> Maletín</label><br />
        <label>
          <input type="checkbox" id="otrosCheck" name="accesorios[]" value="Otros"
                 onclick="document.getElementById('otrosTexto').disabled = !this.checked;">
          Otros
        </label><br />
        <input type="text" id="otrosTexto" name="otrosTexto"
               placeholder="Especificar otros accesorios" disabled
               style="width: 100%; margin-top: 5px;" />
      </div>

      <label for="averia">Avería:</label>
      <textarea id="averia" name="averia" rows="3"></textarea>

      <label for="observaciones">Observaciones:</label>
      <textarea id="observaciones" name="observaciones" rows="3"></textarea>

      <label for="fecha_entrada">Fecha de entrada:</label>
      <input type="date" id="fecha_entrada" name="fecha_entrada" />

      <button type="submit">Guardar y generar PDF</button>
    </form>
  </div>

  <script>
  // Validación y confirmación de teléfono
  document.getElementById('formReparacion').addEventListener('submit', function(e) {
    const tel = document.getElementById('telefono');
    tel.value = tel.value.replace(/\s+/g, '');

    if (!/^\d{9}$/.test(tel.value)) {
      const quiereCorregir = confirm(
        'El número de teléfono no es correcto.\n\n' +
        '¿Deseas corregirlo?'
      );
      if (quiereCorregir) {
        e.preventDefault();
        tel.focus();
      }
    }
  });

  // Fecha de hoy por defecto
  document.addEventListener('DOMContentLoaded', function() {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0');
    const dd = String(hoy.getDate()).padStart(2, '0');
    document.getElementById('fecha_entrada').value = `${yyyy}-${mm}-${dd}`;
  });
  </script>
</body>
</html>
