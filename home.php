<?php
include('nav.html');


// Dados dos países
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
  <link rel="stylesheet" href="style/global.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="style/home.css" />
  <title>Homepage</title>
</head>

<body>
  <div class="content">
    <div class="edit-container">
      <div class="edit">
        <h1>Editar Informações</h1>
        <form action="" method="post">
          <h2>Alterar Contacto</h2>
          <hr>
          <div class="input-field-contact">

            <div class="indicativo">
              <label for="indicativo"><i class="fas fa-globe"></i> Indicativo</label>
              <div class="contact-input-wrapper">
                <select name="indicativo" id="indicativo" required>
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
          <button class="btn-update" type="submit">Alterar Contacto </button>
          <h2>Alterar Email</h2>
          <hr>
          <div class="input-field">
            <label for="newEmail"><i class="fas fa-envelope"></i> Novo Email </label>
            <input type="text" name="newEmail" id="newEmail" placeholder="Insira o seu email" required />
          </div>

          <div class="input-field">

            <label for="confirmEmail"><i class="fas fa-envelope"></i> Confirmar Email </label>
            <input type="text" name="confirmEmail" id="confirmEmail" placeholder="Confirme o seu email" required />
          </div>
          <button class="btn-update" type="submit">Alterar Email </button>
          <h2>Alterar Password</h2>
          <hr>
          <div class="input-field">
            <label for="newPassword"><i class="fas fa-lock"></i> Nova Password </label>
            <input type="password" name="newPassword" id="newPassword" placeholder="Insira a password" required />
          </div>
          <div class="input-field">
            <label for="confirmPassword"><i class="fas fa-lock"></i> Confirmar Password </label>
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirme a sua password" required />
          </div>
          <button class="btn-update" type="submit">Alterar Password</button>
        </form>
      </div>
      <div class="estatistica">
        Colocar aqui as minhas definições +
        Estatisticas
      </div>
    </div>
  </div>
</body>

</html>

<?php include('footer.html'); ?>