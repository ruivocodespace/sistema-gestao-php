<?php
// ============================================
// Arquivo: cadastro_usuario.php
// Função: Cadastro de usuários (área restrita)
// ============================================

// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
require_once "logado.php";

// Incluir o arquivo de conexão com o banco
require_once "conexao.php";


// Variáveis para mensagens
$sucesso = "";
$erro = "";
$editando = NULL;


if (isset($_GET["editar"])) {
    $id = $_GET["editar"];
    $sql = "SELECT * FROM usuario WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

if (isset($_GET["excluir"])) {
    $id = $_GET["excluir"];
    $sql = "DELETE FROM usuario WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
}

// Verificar se o formulário de cadastro foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST["id"];
    $nome  = $_POST["nome"];
    $email = $_POST["email"];
    $senha = !$id ? $_POST["senha"] : '';

    // Verificar se o email já existe
    $sql = "SELECT * FROM usuario WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($resultado) > 0 && !$editando) {
        $erro = "Este email já está cadastrado.";
    } else {
        // Criptografar a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserir o novo usuário
        if($id){
            $sql = "UPDATE usuario SET  nome = '$nome', email = '$email' WHERE id = $id";
        }else{
            $sql = "INSERT INTO usuario (nome, email, senha) VALUES ('$nome', '$email', '$senhaHash')";
        }

        if (mysqli_query($conexao, $sql)) {
            $sucesso = "Usuário cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar usuário.";
        }
    }
}

// Buscar todos os usuários para listar
$sql = "SELECT id, nome, email, criado_em FROM usuario ORDER BY id DESC";
$usuarios = mysqli_query($conexao, $sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário — Projeto SENAI</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- ========== MENU LATERAL (Sidebar) ========== -->
        <?php 
            require_once "menu.php"
        ?>
    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="ml-64 flex-1 p-8">

        <!-- Cabeçalho da página -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Cadastrar Usuário</h2>
            <p class="text-gray-500 mt-1">Preencha os dados abaixo para criar um novo usuário.</p>
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
            <form method="POST" action="cadastro_usuario.php">
                <input type="hidden" value="<?=$editando['id'] ?? "" ?>" name="id"/>

                <!-- Campo Nome -->
                <div class="mb-4">
                    <label for="nome" class="block text-gray-700 font-medium mb-2">
                        Nome
                    </label>
                    <input
                        value="<?=$editando['nome'] ?? "" ?>"
                        type="text"
                        id="nome"
                        name="nome"
                        required
                        placeholder="Digite o nome"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">
                        Email
                    </label>
                    <input
                        value="<?=$editando['email'] ?? "" ?>"
                        type="email"
                        id="email"
                        name="email"
                        required
                        placeholder="Digite o email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo Senha -->
                <?php if(!$editando){ ?>
                    <div class="mb-6">
                        <label for="senha" class="block text-gray-700 font-medium mb-2">
                            Senha
                        </label>
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            required
                            placeholder="Digite a senha"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                <?php } ?>

                <!-- Botão Cadastrar -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200"
                >
                    Cadastrar
                </button>

            </form>
        </div>

        <!-- Lista de Usuários -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Usuários Cadastrados</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="px-4 py-3 text-left rounded-tl-lg">ID</th>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left rounded-tr-lg">Criado em</th>
                        <th class="px-4 py-3 text-left">Ações</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($u = mysqli_fetch_assoc($usuarios)): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3"><?php echo $u["id"]; ?></td>
                            <td class="px-4 py-3"><?php echo $u["nome"]; ?></td>
                            <td class="px-4 py-3"><?php echo $u["email"]; ?></td>
                            <td class="px-4 py-3 text-gray-500"><?php echo $u["criado_em"]; ?></td>
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
