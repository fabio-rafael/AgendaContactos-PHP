<?php
include('nav.html');
include('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_STRING);
  $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

  // Validar o e-mail
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '<script>alert("Por favor, insira um email válido."); window.location.href = "suporte.php";</script>';
    exit;
  }

  $stmt = $conexao->prepare("INSERT INTO email (email, assunto, mensagem) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $email, $assunto, $mensagem);

  if ($stmt->execute()) {
    echo '<script>alert("Email enviado com sucesso!"); window.location.href = "suporte.php";</script>';
  } else {
    echo '<script>alert("Erro ao enviar o email, certifique-se que inseriu dados válidos!"); window.location.href = "suporte.php";</script>';
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style/global.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="style/suporte.css" />
  <title>Suporte</title>
</head>

<body>
  <!DOCTYPE html>
  <html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>

  <body>
    <div class="content">
      <div class="faq">
        <h2>F.A.Q. - Tudo Oque Deves Saber Sobre Este Web App</h2>
        <p>Clique para saber mais.</p>
        <button class="accordion-1">Como Funciona ?</button>
        <div class="panel">
          <p>Esta aplicação foi criada com o intuito de aplicar conhecimentos de PHP , MySQL, HTML e CSS. O utilizador pode consultar a sua Dashboard para alterar as suas próprias definições
            e visualizar as suas estatísticas. Tem ainda a possibilidade de adicionar ou remover contactos como seria de esperar na sua agenda e ainda contactar o suporte .Esta aplicação tem ainda
            uma distinção de users em que deve ser feito o registo de um novo membro e proceder ao devido login para que aceda a sua conta com os seus contactos.</p>
          </p>
        </div>

        <button class="accordion">Como posso adicionar ou remover contatos na minha agenda ?
        </button>
        <div class="panel">
          <p>Para adicionar um novo contato, vá para a sua lista de contactos e clique no botão de adição de contatos. Preencha as informações necessárias, como nome, número de telefone e e-mail (opcional), e salve as alterações. O mesmo se aplica caso queira eliminar algum contacto.</p>
        </div>

        <button class="accordion">Onde é possível ver mais do meu trabalho ? </button>
        <div class="panel">
          <p>Através do footer são redirecionados tanto para o meu GitHub como para o Linked In ou através da secção de Contacto onde poderão enviar uma mensagem por e-mail.</p>
        </div>
        <div class="contact-me">
          <h2>Entrar em contato</h2>
          <form method="post">
            <div class="assuntos">
              <div class="input-field">
                <label for="email">Seu Email:</label>
                <input type="email" id="email" name="email" required>
              </div>
              <div class="input-field">
                <label for="assunto">Assunto:</label>
                <input type="text" id="assunto" name="assunto" required>
              </div>
            </div>
            <div class="mensagem">
              <div class="input-field">
                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" required></textarea>
              </div>
            </div>
            <button class="btn-submit" type="submit" name="enviar_email">Enviar Mensagem</button>
          </form>
          <small class="proof">Apenas prova de conceito , os emails são guardados na base de dados.</small>
        </div>
        <script>
          // Função para adicionar eventos de clique a elementos com a classe 'accordion' ou 'accordion-1'
          function addAccordionClickEvent(className) {
            var elements = document.getElementsByClassName(className);

            for (var i = 0; i < elements.length; i++) {
              elements[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                  panel.style.maxHeight = null;
                } else {
                  panel.style.maxHeight = panel.scrollHeight + "px";
                }
              });
            }
          }
          addAccordionClickEvent("accordion");
          addAccordionClickEvent("accordion-1");
        </script>
      </div>
    </div>
  </body>

  </html>


  <?php include('footer.html'); ?>