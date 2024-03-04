<?php
include('session.php');
include('nav.html');
include('database.php');

function sanitize_input($input)
{
  return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Lógica para adicionar o contacto 
if (isset($_POST['adicionar_contacto'])) {
  $nome = sanitize_input($_POST['nome']);
  $email = sanitize_input($_POST['email']);
  $contacto = sanitize_input($_POST['telefone']);
  $user_id = $_SESSION['usuario_id'];  // Obtém o ID do usuário da sessão

  // Validar os campos 
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '<script>alert("Por favor, insira um email válido."); window.location.href = "contactos.php";</script>';
    exit;
  }

  $stmt = $conexao->prepare("INSERT INTO contacts (nome, email, contacto, user_id) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("sssi", $nome, $email, $contacto, $user_id);

  if ($stmt->execute()) {
    echo '<script>alert("Contacto adicionado com sucesso!"); window.location.href = "contactos.php";</script>';
  } else {
    echo '<script>alert("Erro ao adicionar o contacto!"); window.location.href = "contactos.php";</script>';
  }

  $stmt->close();
}

// Lógica para eliminar o contacto
if (isset($_POST['eliminar_contacto'])) {
  $id = sanitize_input($_POST['id']);
  $stmt = $conexao->prepare("DELETE FROM contacts WHERE id =?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo '<script>alert("Contacto eliminado com sucesso!"); window.location.href = "contactos.php";</script>';
  } else {
    echo '<script>alert("Erro ao eliminar o contacto!"); window.location.href = "contactos.php";</script>';
  }

  $stmt->close();
}

// Lógica para atualizar o contacto
if (isset($_POST['edit_contacto'])) {
  $id = sanitize_input($_POST['id']);
  $email = sanitize_input($_POST['email']);
  $contacto = sanitize_input($_POST['telefone']);
  $user_id = $_SESSION['usuario_id'];

  $stmt = $conexao->prepare("UPDATE contacts SET email=?, contacto=? WHERE id=? AND user_id=?");
  $stmt->bind_param("ssii", $email, $contacto, $id, $user_id);

  if ($stmt->execute()) {
    echo '<script>alert("Contacto atualizado com sucesso!"); window.location.href = "contactos.php";</script>';
  } else {
    echo '<script>alert("Erro ao atualizar o contacto!"); window.location.href = "contactos.php";</script>';
  }

  $stmt->close();
}

// Lógica para filtrar por nome ou número de contacto
if (isset($_POST['submit_filtro'])) {
  $filtro_nome = sanitize_input($_POST['nome']);
  $filtro_telefone = sanitize_input($_POST['contacto']);

  // Verifica se pelo menos um dos campos está preenchido
  if (!empty($filtro_nome) || !empty($filtro_telefone)) {
    $sql = "SELECT * FROM contacts WHERE user_id = ?";
    $param_types = "i";
    $param_values = array($_SESSION['usuario_id']);

    // Se o campo de nome estiver preenchido
    if (!empty($filtro_nome)) {
      $sql .= " AND nome LIKE ?";
      $param_types .= "s";
      $param_values[] = "%$filtro_nome%";
    }

    // Se o campo de contacto estiver preenchido
    if (!empty($filtro_telefone)) {
      $sql .= " AND contacto LIKE ?";
      $param_types .= "s";
      $param_values[] = "%$filtro_telefone%";
    }

    // Adiciona o ORDER BY para ordenar por nome
    $sql .= " ORDER BY nome ASC";

    // Prepara a consulta
    $stmt = $conexao->prepare($sql);

    // Adiciona os parâmetros
    $stmt->bind_param($param_types, ...$param_values);

    // Executa a consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se há resultados
    if ($result->num_rows > 0) {
      echo '<script>';
      while ($row = $result->fetch_assoc()) {
        echo "alert('Contato encontrado:\\nNome: " . sanitize_input($row['nome']) . "\\nEmail: " . sanitize_input($row['email']) . "\\nNúmero de Contato: " . sanitize_input($row['contacto']) . "');";
      }
      echo '</script>';
    } else {
      echo '<script>alert("Nenhum contato encontrado.");</script>';
    }

    $stmt->close();
  } else {
    echo '<script>alert("Por favor, insira pelo menos um valor para filtrar.");</script>';
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
  <link rel="stylesheet" href="style/contactos.css" />
  <title>Contactos</title>
</head>

<body>
  <div class="content">
    <div class="edit-container">
      <div class="contacts-container">
        <table class="tabela-contactos">
          <thead>
            <h1>Meus contactos</h1>
            <hr>
            <tr>
              <th>Nome</th>
              <th>Email</th>
              <th>Nº Contacto</th>
              <th>Editar</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT * FROM contacts WHERE user_id = " . $_SESSION['usuario_id'];
            $result = $conexao->query($sql);

            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["nome"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["contacto"]) . "</td>";
                echo "<td>
                      <a href='edit_contacto.php?id=" . $row["id"] . "'><i class='fas fa-pen-to-square' style='color: green;'></i></a>
                      <form method='post' style='display: inline;'>
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
                        <button type='submit' name='eliminar_contacto' style='border: none; background-color: transparent; cursor: pointer;'>
                          <i class='fas fa-trash' style='color: red;'></i>
                        </button>
                      </form>
                    </td>";

                echo "</tr>";
              }
            } else {
              echo "<tr>
              <td colspan='4'>Não tem nenhum contacto registrado.</td>
            </tr>";
            }
            ?>

          </tbody>
        </table>
      </div>

      <div class="right-row">
        <div class="adicionar">
          <h1><i class="fas fa-user-plus"></i> Adicionar Novo Contacto</h1>
          <form method="post">
            <div class="input-field">
              <div class="campo">
                <label for="nome" style="margin-left:41px;">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Insira o nome do novo contacto" required>
              </div>
            </div>
            <div class="input-field">
              <div class="campo">
                <label for="email" style="margin-left:47px;">Email:</label>
                <input type="email" id="email" name="email" placeholder="Insira o email [Opcional]">
              </div>
            </div>
            <div class="input-field">
              <div class="campo">
                <label for="telefone">Nº Contacto:</label>
                <input type="tel" id="telefone" name="telefone" placeholder="Insira o número do novo contacto" required>
              </div>
              <button type="submit" name="adicionar_contacto">Adicionar contato</button>
            </div>
          </form>
        </div>
        <div class="procurar">
          <h1><i class="fa-solid fa-magnifying-glass"></i> Encontrar Contacto</h1>
          <p>Insira o nome ou número do contacto</p>
          <form method="POST">
            <div class="input-field">
              <div class="campo">
                <label for="nome" style="margin-left:41px;">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Insira o nome">
              </div>
            </div>

            <div class="input-field">
              <div class="campo">
                <label for="contacto">Nº Contacto:</label>
                <input type="tel" id="contacto" name="contacto" placeholder="Insira o contacto">
              </div>
              <button type="submit" name="submit_filtro">Filtrar contactos</button>
            </div>
          </form>
        </div>
      </div>
</body>

</html>


<?php include('footer.html'); ?>