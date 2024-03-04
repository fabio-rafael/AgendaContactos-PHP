<?php
include('session.php');
include('database.php');
include('session.php');


$countriesJson = file_get_contents('countries.json');
$countries = json_decode($countriesJson, true);

if ($countries === null) {
  // Erro ao decodificar dados JSON
  die("Erro ao decodificar dados JSON");
}

$id_do_contato = $_GET['id'];

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recuperar os valores do formulário
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $indicativo = isset($_POST['indicativo']) ? $_POST['indicativo'] : '';
  $contacto = $_POST['contacto'];

  $numero_completo = $indicativo . $contacto; // junção do indicativo com o contacto

  $updateFields = array();
  $updateParams = array();

  if (!empty($nome)) {
    $updateFields[] = "nome = ?";
    $updateParams[] = $nome;
  }

  if (!empty($email)) {
    $updateFields[] = "email = ?";
    $updateParams[] = $email;
  }

  if (!empty($numero_completo)) {
    $updateFields[] = "contacto = ?";
    $updateParams[] = $numero_completo;
  }


  // Se houver campos para atualizar
  if (!empty($updateFields)) {
    $updateFieldsStr = implode(", ", $updateFields);

    // Adicione o ID como último parâmetro no array
    $updateParams[] = $id_do_contato;

    // Criando a string de placeholders para o bind_param
    $placeholders = str_repeat('s', count($updateParams));

    $stmt = $conexao->prepare("UPDATE contacts SET $updateFieldsStr WHERE id = ?");
    $stmt->bind_param($placeholders, ...$updateParams);

    if ($stmt->execute()) {
      //echo "Dados atualizados com sucesso!";
      header("Location: contactos.php");
      exit();
    } else {
      echo "Erro ao atualizar dados: " . $stmt->error;
    }

    $stmt->close();
  } else {
    echo "Nenhum campo preenchido para atualizar.";
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
  <link rel="stylesheet" href="style/edit_contacto.css" />

  <title>Editar Contacto</title>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <header>Editar Contacto</header>
      <hr />
      <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $id_do_contato; ?>">

        <div class="input-field">
          <label for="nome"><i class="fas fa-user"></i> Nome </label>
          <input type="text" name="nome" id="nome" placeholder="Insira o seu nome" />
        </div>
        <div class="input-field">
          <label for="email"><i class="fas fa-envelope"></i> Alterar Email </label>
          <input type="text" name="email" id="email" placeholder="Insira o seu email" />
        </div>
        <div class="input-field-contact">
          <div class="indicativo">
            <label for="indicativo"><i class="fas fa-globe"></i> Indicativo</label>
            <div class="contact-input-wrapper">
              <select name="indicativo" id="indicativo">
                <option value="" disabled selected style="display:none;">Escolha um indicativo</option>
                <?php
                foreach ($countries as $country) {
                  echo "<option value='{$country['dial_code']}'>{$country['dial_code']} ({$country['name']})</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="contacto">
            <label for="contacto"><i class="fas fa-phone"></i> Alterar Contacto
            </label>
            <input type="text" name="contacto" id="contacto" placeholder="Insira o seu contacto" />
          </div>
        </div>
        <div class="butoes">
          <button class="btn-edit" type="submit">Alterar Contacto</button></a>
          <a href="contactos.php"><button class="btn-edit" type="button">Cancelar</button></a>

        </div>
        <div class="info-text">
          <small class="info-text">Caso deixe algum espaço em branco não será considerado</small>
        </div>


      </form>

    </div>
  </div>
</body>

</html>