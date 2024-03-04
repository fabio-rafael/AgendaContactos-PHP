<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
  // Se não estiver autenticado, redirecionar para a página de login
  header('Location: login.php');
  exit();
}

include('nav.html');
include('database.php');

// Dados dos países
$countriesJson = file_get_contents('countries.json');
$countries = json_decode($countriesJson, true);

if ($countries === null) {
  // Erro ao decodificar dados JSON
  die("Erro ao decodificar dados JSON");
}

// Lógica para processar as edições do contacto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['alterar_contacto'])) {
    // Processar a atualização do contacto
    $indicativo = $_POST['indicativo'];
    $contacto = $_POST['contacto'];
    $usuarioId = $_SESSION['usuario_id'];

    $numero_completo = $indicativo . $contacto; // junção do indicativo com o contacto

    $stmt = $conexao->prepare("UPDATE users SET contacto = ? WHERE id = ?");
    $stmt->bind_param("si", $numero_completo, $usuarioId);

    if ($stmt->execute()) {
      $_SESSION['usuario_contacto'] = $numero_completo;
      echo '<script>alert("Contacto atualizado com sucesso!"); window.location.href = "index.php";</script>';
    } else {
      echo '<script>alert("Erro ao atualizar o contacto!"); window.location.href = "index.php";</script>';
    }

    $stmt->close();
  }

  if (isset($_POST['alterar_email'])) {
    // Processar a atualização do email
    $email = $_POST['newEmail'];
    $usuarioId = $_SESSION['usuario_id'];
    $confirmEmail = $_POST['confirmEmail'];

    // Verificar se os e-mails coincidem
    if ($email != $confirmEmail) {
      echo '<script>alert("Os e-mails não coincidem. Por favor, verifique os dados inseridos.");</script>';
    } else {
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

        // Preparação e execução da consulta SQL dentro do bloco else
        $stmt_update_email = $conexao->prepare("UPDATE users SET email =? WHERE id =?");
        $stmt_update_email->bind_param("si", $email, $usuarioId);

        if ($stmt_update_email->execute()) {
          $_SESSION['usuario_email'] = $email;
          echo '<script>alert("Email atualizado com sucesso!"); window.location.href = "index.php";</script>';
        } else {
          echo '<script>alert("Erro ao atualizar o email!"); window.location.href = "index.php";</script>';
        }

        $stmt_update_email->close();
      }
    }
  }

  if (isset($_POST['alterar_password'])) {
    // Processar a atualização da password
    $oldPassword = $_POST['oldPassword'];
    $password = $_POST['newPassword'];
    $usuarioId = $_SESSION['usuario_id'];
    $confirmPassword = $_POST['confirmPassword'];

    // Verificar se as senhas coincidem e têm pelo menos 6 caracteres
    if ($password != $confirmPassword) {
      echo '<script>alert("As novas senhas não coincidem!");</script>';
    } elseif (strlen($password) < 6) {
      echo '<script>alert("A senha deve ter no mínimo 6 caracteres!");</script>';
    } else { // Verificar a senha atual
      $stmt_check_password = $conexao->prepare("SELECT password FROM users WHERE id = ?");
      $stmt_check_password->bind_param("i", $usuarioId);
      $stmt_check_password->execute();
      $stmt_check_password->bind_result($hashed_password);

      if ($stmt_check_password->fetch()) {
        // Verificar se a senha atual está correta
        if (password_verify($oldPassword, $hashed_password)) {
          // Senha atual está correta, então prosseguir com a atualização
          $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);

          // Fechar a verificação da senha atual
          $stmt_check_password->free_result();
          $stmt_check_password->close();

          // Preparação e execução da consulta SQL
          $stmt_update_password = $conexao->prepare("UPDATE users SET password = ? WHERE id = ?");
          $stmt_update_password->bind_param("si", $new_hashed_password, $usuarioId);

          if ($stmt_update_password->execute()) {
            $_SESSION['usuario_password'] = $password;
            echo '<script>alert("Senha atualizada com sucesso!"); window.location.href = "login.php";</script>';
          } else {
            echo '<script>alert("Erro ao atualizar a senha!"); window.location.href = "index.php";</script>';
          }

          $stmt_update_password->close();
        } else {
          // Senha atual incorreta
          echo '<script>alert("A senha atual fornecida está incorreta!");</script>';
        }
      } else {
        // Erro ao verificar a senha atual
        echo '<script>alert("Erro ao verificar a senha atual!"); window.location.href = "index.php";</script>';
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style/global.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="style/home.css" />
  <link rel="shortcut icon" href="style/contact-us.ico" type="image/x-icon">
  <title>Homepage</title>
</head>

<body>
  <div class=" content">
    <div class="edit-container">
      <div class="edit">
        <h1>Editar Informações Pessoais</h1>
        <form action="" method="post">
          <h2>Alterar Contacto</h2>
          <hr>
          <div class="input-field-contact">

            <div class="indicativo">
              <label for="indicativo"><i class="fas fa-globe"></i> Indicativo</label>
              <div class="contact-input-wrapper">
                <select name="indicativo" id="indicativo">
                  <?php
                  foreach ($countries as $country) {
                    echo "<option value='{$country['dial_code']}'>({$country['dial_code']}) <img src='{$country['flag']}' alt='{$country['name']}'/> {$country['name']}</option>";
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
          <button class="btn-update" type="submit" name="alterar_contacto">Alterar Contacto </button>
          <h2>Alterar Email</h2>
          <hr>
          <div class="input-field">
            <label for="newEmail"><i class="fas fa-envelope"></i> Novo Email </label>
            <input type="text" name="newEmail" id="newEmail" placeholder="Insira o seu email" />
          </div>

          <div class="input-field">

            <label for="confirmEmail"><i class="fas fa-envelope"></i> Confirmar Email </label>
            <input type="text" name="confirmEmail" id="confirmEmail" placeholder="Confirme o seu email" />
          </div>
          <button class="btn-update" type="submit" name="alterar_email">Alterar Email </button>
          <h2>Alterar Password</h2>
          <hr>
          <div class="input-field">
            <label for="oldPassword"><i class="fas fa-lock"></i> Password Atual </label>
            <input type="password" name="oldPassword" id="oldPassword" placeholder="Insira a sua password atual" />
          </div>
          <div class="input-field">
            <label for="newPassword"><i class="fas fa-lock"></i> Nova Password </label>
            <input type="password" name="newPassword" id="newPassword" placeholder="Insira a sua nova password" />
          </div>
          <div class="input-field">
            <label for="confirmPassword"><i class="fas fa-lock"></i> Confirmar Password </label>
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirme a sua password" />
          </div>
          <button class="btn-update" type="submit" name="alterar_password">Alterar Password</button>
        </form>
      </div>
      <div class="estatistica">
        <div class="titulo-config">
          <h1>
            <?php
            if (isset($_SESSION['usuario_nome'])) {
              echo "Bem-vindo, {$_SESSION['usuario_nome']}!";
            } else {
              echo "Bem-vindo!";
            }
            ?>
          </h1>
          <hr>
        </div>
        <div class="config">
          <div class="config-head">
            <h3>As minhas informações</h3>
          </div>
          <div class="configuracao">
            <table>
              <tr>
                <th>Email</th>
                <td>
                  <li> <?php echo isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : 'N/A'; ?></li>
                </td>
              </tr>
              <tr>
                <th>Contacto</th>
                <td>
                  <li> <?php echo isset($_SESSION['usuario_contacto']) ? $_SESSION['usuario_contacto'] : 'N/A'; ?></li>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <hr class="hr-config">
        <div class="numeros">
          <div class="numeros-head">
            <h1>Neste momento tem </h1>
          </div>
          <div class="contactos">
            <?php
            if (isset($_SESSION['usuario_nome'])) {
              $query = "SELECT COUNT(*) as total_rows FROM contacts WHERE user_id=?";
              // Preparar a consulta com um statement parametrizado
              $stmt = $conexao->prepare($query);
              // Verificar se a preparação foi bem-sucedida
              if ($stmt) {
                // Bind do parâmetro
                $stmt->bind_param("i", $_SESSION['usuario_id']);
                // Executar a consulta
                $stmt->execute();
                // Bind do resultado
                $stmt->bind_result($totalLinhas);
                // Obter o resultado
                $stmt->fetch();
                echo  $totalLinhas;
                // Fechar o statement
                $stmt->close();
              } else {
                // Tratar erro na preparação da consulta
                echo "Erro na preparação da consulta.";
              }
              $conexao->close();
            } else {
              echo "Erro de ligação!";
            }
            ?>
          </div>
          <p>Contacto(s)</p>
        </div>
      </div>
    </div>
  </div>
  </div>
</body>

</html>

<?php include('footer.html'); ?>