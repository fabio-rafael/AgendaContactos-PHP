<?php
include('database.php');

$countriesJson = file_get_contents('Countries.json');
$countries = json_decode($countriesJson, true);

if ($countries === null) {
  // Erro ao decodificar dados JSON
  die("Erro ao decodificar dados JSON");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="style/global.css" />
  <link rel="stylesheet" href="style/registo.css" />

  <title>Registo</title>

  <script>
    <?php
    if (!$conexao) {
      echo "<script>('Error connecting to MySQL: " . mysqli_connect_error() . "');</script>";
    }
    ?>
  </script>

</head>

<body>
  <div class="container">
    <div class="form-container">
      <header>Registo</header>
      <hr />
      <form action="" method="post">

        <div class="input-field">
          <label for="nome"><i class="fas fa-user"></i> Nome </label>
          <input type="text" name="nome" id="nome" placeholder="Insira o seu nome" required />
        </div>
        <div class="input-field-contact">
          <div class="indicativo">
            <label for="indicativo"><i class="fas fa-globe"></i> Indicativo</label>
            <div class="contact-input-wrapper">
              <select name="indicativo" id="indicativo" required>
                <?php
                foreach ($countries as $country) {
                  echo "<option value='{$country['dial_code']}'>{$country['dial_code']} ({$country['name']})</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="contacto">
            <label for="contacto"><i class="fas fa-phone"></i> Contacto
            </label>
            <input type="text" name="contacto" id="contacto" placeholder="Insira o seu contacto" />
          </div>
        </div>
        <div class="input-field">
          <label for="Idade"><i class="fas fa-birthday-cake"></i> Data de nascimento
          </label>
          <input type="date" name="idade" id="idade" max="<?php echo date('Y-m-d'); ?>" />
        </div>

        <div class="input-field">
          <label for="email"><i class="fas fa-envelope"></i> Email </label>
          <input type="text" name="email" id="email" placeholder="Insira o seu email" required />
        </div>

        <div class="input-field">
          <label for="password"><i class="fas fa-lock"></i> Password </label>
          <input type="password" name="password" id="password" placeholder="Insira a password" required />
        </div>
        <div class="input-field">
          <label for="confirmar_password"><i class="fas fa-lock"></i> Confirmar Password
          </label>
          <input type="password" name="confirmar_password" id="confirmar_password" placeholder="Confirme a sua password" required />
        </div>
        <button class="btn-registo" type="submit">Registar</button>
      </form>
      <div class="links">
        Já tem conta ?
        <a href="login.php"><i class="fas fa-sign-in"></i> Login</a>
      </div>
    </div>
  </div>

  <?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
    $indicativo = $_POST['indicativo'];
    $contacto = $_POST['contacto'];
    $idade = $_POST['idade'];
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];
    if ($password != $confirmar_password) {
      echo '<script>alert("As senhas não conferem");</script>';
    }

    if (strlen($password) < 6) {
      echo '<script>alert("A senha deve conter no mínimo 6 caracteres");</script>';
    }

    if ($password == $confirmar_password && strlen($password) >= 6) {
      $numero_completo = $indicativo . $contacto; // junção do indicativo com o contacto
      $hashed_password = password_hash($password, PASSWORD_DEFAULT); // encriptar password


      // Verificar se o email já está registrado
      $stmt_check_email = $conexao->prepare("SELECT email FROM users WHERE email = ?");
      $stmt_check_email->bind_param("s", $email);
      $stmt_check_email->execute();
      $stmt_check_email->store_result();

      if ($stmt_check_email->num_rows > 0) {
        echo '<script>alert("O email já está registrado. Utilize outro email.");</script>';
        $stmt_check_email->close();
      } else {
        $stmt_check_email->close();

        if ($conexao) {
          $stmt = $conexao->prepare("INSERT INTO users (nome, contacto, idade, email, password) VALUES (?, ?, ?, ?, ?)");
          if ($stmt) {
            $stmt->bind_param("sssss", $nome, $numero_completo, $idade, $email, $hashed_password);
            $stmt->execute();
            $stmt->close();
            echo '<script>alert("Registo efetuado com sucesso!"); window.location.href = "registo.php";</script>';
            exit;
          } else {
            echo '<script>alert("Erro ao executar a consulta SQL.");</script>';
          }
        }
      }
    }
  }

  ?>
</body>

</html>