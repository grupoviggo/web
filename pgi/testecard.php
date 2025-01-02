<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Card com Efeito Esbranquiçado</title>
  <style>
body {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
  background-color: #f5f5f5;
}

.card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 300px;
  height: 150px;
  background: linear-gradient(to bottom, #0078d7, #004ba0);
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  position: relative;
  text-align: center;
  font-family: Arial, sans-serif;
  color: white;
  font-size: 20px;
  font-weight: bold;
}

.card::before,
.card::after {
  content: "";
  position: absolute;
  width: 50px;
  height: 50px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.5), transparent);
  z-index: 1;
}

.card::before {
  top: -10px;
  left: -10px;
  border-radius: 15px 0 0 0;
}

.card::after {
  bottom: -10px;
  right: -10px;
  border-radius: 0 0 15px 0;
}

.icon img {
  width: 40px;
  height: 40px;
  margin-bottom: 10px;
  z-index: 2;
}

h2 {
  z-index: 2;
  position: relative;
}

  </style>
</head>
<body>
  <div class="card">
    <div class="icon">
      <img src="icon.png" alt="Icon">
    </div>
    <h2>Backoffice</h2>
  </div>
</body>
</html>
