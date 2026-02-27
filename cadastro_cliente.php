<?php
// ============================================
// Arquivo: cadastro_cliente.php
// Função: Cadastro de clientes (área restrita)
// ============================================

// Iniciar a sessão
session_start();

// Verificar se o usuario está logado
require_once "logado.php";

// Incluir o arquivo de conexão com o banco
require_once "conexao.php";

// Variáveis para mensagens
$sucesso = "";
$erro = "";

$editando = NULL;

// Editar cliente

if(isset($_GET["editar"])){
    $id = $_GET["editar"];
    $sql = "SELECT * FROM cliente WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}
if (isset($_GET["excluir"])) {
    $id = $_GET["excluir"];
    $sql = "DELETE FROM cliente WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
}

// Verificar se o formulário de cadastro foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id  = $_POST["id"];
    $nome  = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $endereco = $_POST["endereco"];
    // Files para arquivos
    $imagem = $_FILES["imagem"];

// Processamento da imagem
   
    // Formatos permitidos
    $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];
    
    // Validar formatos
    if ($imagem["size"] > 0 && !in_array($imagem["type"], $tiposPermitidos)) {
        $erro = "Tipo não permitido. Use JPG, PNG ou WEBP.";

    // Formato correto segue:
    } else {
        $nomeImagem = '';
        if($imagem["size"] > 0){
            $extensao   = pathinfo($imagem["name"], PATHINFO_EXTENSION);
            $nomeImagem = "cliente_" . time() . "." . $extensao;
            move_uploaded_file($imagem["tmp_name"], "uploads/" . $nomeImagem);
        }

        // Verificar se o email já existe
        $sql = "SELECT * FROM cliente WHERE email = '$email'";
        $resultado = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($resultado) > 0 && $editando !== NULL) {
            $erro = "Este email já está cadastrado.";
        } else {
            if($id){
                if($imagem["size"] > 0){
                    $sql = "UPDATE cliente SET 
                    nome = '$nome',
                    telefone = '$telefone',
                    email = '$email',
                    cpf = '$cpf',
                    imagem = '$nomeImagem',
                    endereco = '$endereco'
                    WHERE id = $id
                    ";
                }else{
                    $sql = "UPDATE cliente SET 
                    nome = '$nome',
                    telefone = '$telefone',
                    email = '$email',
                    cpf = '$cpf',
                    endereco = '$endereco'
                    WHERE id = $id
                    ";
                }

                $sucesso = "Cliente atualizado com sucesso!";
            }else{
                if($imagem["size"] > 0){
                    $sql = "INSERT INTO cliente (nome, telefone, email, cpf, endereco, imagem) VALUES ('$nome', '$telefone', '$email', '$cpf', '$endereco', '$nomeImagem')";
                }else{
                    $sql = "INSERT INTO cliente (nome, telefone, email, cpf, endereco) VALUES ('$nome', '$telefone', '$email', '$cpf', '$endereco')";
                }
                    
                $sucesso = "Cliente cadastrado com sucesso!";

            }

            if(!mysqli_query($conexao, $sql)) {
                $erro = "Erro ao cadastrar cliente.";
            }
        }
    }
}


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente — Projeto SENAI</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-100 min-h-screen flex">

     <!-- ========== MENU LATERAL (Sidebar) ========== -->
    <?php 
        require_once "menu.php";
    ?>
    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="ml-64 flex-1 p-8">

        <!-- Cabeçalho da página -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Cadastrar Cliente</h2>
            <p class="text-gray-500 mt-1">Preencha os dados abaixo para criar um novo cliente.</p>
        </div>

        <!-- Mensagem de sucesso -->
        <?php if (!empty($sucesso)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $sucesso; ?>
            </div>
        <?php endif; ?>

        <!-- Mensagem de erro -->
        <?php if (!empty($erro)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Cadastro -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 max-w-xl">
            <form method="POST" action="cadastro_cliente.php" enctype="multipart/form-data">
            <input type="hidden" value="<?=$editando['id'] ?? "" ?>" name="id"/>

                <!-- Campo Nome -->
                <div class="mb-4">
                    <label for="nome" class="block text-gray-700 font-medium mb-2">
                        Nome
                    </label>
                    <input
                        value="<?= $editando['nome'] ?? "" ?>"
                        type="text"
                        id="nome"
                        name="nome"
                        required
                        placeholder="Digite o nome"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <!-- Campo telefone -->
                <div class="mb-4">
                    <label for="telefone" class="block text-gray-700 font-medium mb-2">
                    Telefone
                    </label>
                    <input
                        value="<?=$editando['telefone'] ?? "" ?>"
                        type="text"
                        id="telefone"
                        name="telefone"
                        required
                        placeholder="Digite o telefone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">
                        Email
                    </label>
                    <input
                        value="<?= $editando['email'] ?? "" ?>"
                        type="email"
                        id="email"
                        name="email"
                        required
                        placeholder="Digite o email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo CPF -->
                <div class="mb-4">
                    <label for="cpf" class="block text-gray-700 font-medium mb-2">
                        CPF
                    </label>
                    <input
                        value="<?= $editando['cpf'] ?? "" ?>"
                        type="number"
                        id="cpf"
                        name="cpf"
                        required
                        placeholder="Digite seu CPF"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo endereço -->
                <div class="mb-4">
                    <label for="endereco" class="block text-gray-700 font-medium mb-2">
                        Endereço completo
                    </label>
                    <input
                        value="<?= $editando['endereco'] ?? "" ?>"
                        type="text"
                        id="endereco"
                        name="endereco"
                        required
                        placeholder="Digite seu endereço completo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo imagem -->
                <div class="mb-4">
                    <label for="imagem" class="block text-gray-700 font-medium mb-2">
                        Imagem
                    </label>
                    <input
                        type="file"
                        accept=".jpg,.png,.webp"
                        id="imagem"
                        name="imagem"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Botão Cadastrar -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200"
                >
                    Cadastrar
                </button>

            </form>
        </div>

        <!-- Lista de Clientes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Clientes Cadastrados</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="px-4 py-3 text-left rounded-tl-lg">Imagem</th>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // echo '<pre>';
                        // print_r($cliente);
                        // die;

                        // Buscar todos os clientes para listar
                        $sql = "SELECT id, nome, imagem, email FROM cliente ORDER BY id DESC";
                        $cliente = mysqli_query($conexao, $sql);   


                        while ($u = mysqli_fetch_assoc($cliente)): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3">
                                <?php if (!empty($u["imagem"])): ?>
                                    <img src="uploads/<?= $u["imagem"] ?>"
                                        width="60">
                            <?php else: ?>
                                Sem imagem
                            <?php endif; ?>
                            </td>
                                <td class="px-4 py-3"><?php echo $u["nome"]; ?></td>
                                <td class="px-4 py-3"><?php echo $u["email"]; ?></td>
                                <td class="px-4 py-3">
                                <a class="editar" href="?editar=<?=$u["id"]; ?>">Editar</a><br>
                                <a onclick="return confirm('Tem certeza disso?')" class="excluir" href="?excluir=<?=$u["id"]; ?>">Excluir</a>
                            </td>
                            </tr>
                        <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>