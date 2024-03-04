<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_SPECIAL_CHARS);
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
    echo '<script>alert("Por favor preencha todos os campos!"); window.location.href = "login.php";</script>';
  } else {
    // Use prepared statements para evitar injeção de SQL
    $stmt = $conexao->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
      // Verifique a senha usando password_verify
      if (password_verify($password, $row['password'])) {
        // Iniciar a sessão e armazenar informações do usuário
        session_start();
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['usuario_nome'] = $row['nome'];
        $_SESSION['usuario_contacto'] = $row['contacto'];
        $_SESSION['usuario_email'] = $row['email'];
        $_SESSION['usuario_password'] = $row['password'];
        // Redirecionar para a página de home
        header('Location: index.php');
        exit();
      } else {
        echo '<script>alert("Email ou senha incorreto!"); window.location.href = "login.php";</script>';
      }
    } else {
      echo '<script>alert("Email ou senha incorreto!"); window.location.href = "login.php";</script>';
    }

    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="style/global.css" />
  <link rel="stylesheet" href="style/login.css" />

  <title>Login</title>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <header>Login</header>
      <hr />
      <form action="" method="post">
        <div class="input-field">
          <label for="email"><i class="fas fa-envelope"></i> Email </label>
          <input type="text" name="email" id="email" placeholder="Insert your Email" required />
        </div>

        <div class="input-field">
          <label for="password"><i class="fas fa-lock"></i> Password </label>
          <input type="password" name="password" id="password" placeholder="Insert your Password" required />
        </div>
        <button class="btn-login" type="submit">Login</button>
      </form>
      <div class="links">
        Ainda não tem conta ?
        <a href="registo.php"><i class="fas fa-user-plus"></i> Faça já o registo!</a>
      </div>
    </div>
  </div>
</body>

</html>