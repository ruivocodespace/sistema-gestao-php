<?php
// ============================================
// Arquivo: cadastro_produto.php
// Função: Cadastro de Produtos (área restrita)
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

// Verificar se o formulário de cadastro foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome  = $_POST["nome"];
    $descricao = $_POST["descricao"];    
    $preco = $_POST["preco"];
    $estoque = $_POST["estoque"];
    $categoria = $_POST["categoria"];
    $imagem = $_FILES["imagem"];
    $nomeImagem = "";

    
    //Tipos de imagem
    if ($imagem["error"] === 0) {

    // 3. Tipos permitidos
    $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

    // 4. Validar tipo
    if (!in_array($imagem["type"], $tiposPermitidos)) {
        $erro = "Tipo não permitido. Use JPG, PNG ou WEBP.";

    // 5. Tudo certo: gerar nome e salvar
    } else {
        // Extrair a extensao do arquivo original
        // Ex: "foto.jpg" -> pega so o "jpg"
        $extensao   = pathinfo($imagem["name"], PATHINFO_EXTENSION);
        $nomeImagem = "produto_" . time() . "." . $extensao;
        move_uploaded_file($imagem["tmp_name"], "uploads/" . $nomeImagem);

        // Verificar se o produto já existe
        $sql = "SELECT * FROM produto WHERE nome = '$nome'";
        $resultado = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($resultado) > 0) {
            $erro = "Este produto já foi cadastrado.";
        } else {
            // Inserir o novo produto
            $sql = "INSERT INTO produto (nome, descricao, preco, estoque, categoria, imagem) VALUES ('$nome', '$descricao', '$preco', '$estoque', '$categoria', '$nomeImagem')";

            if (mysqli_query($conexao, $sql)) {
                $sucesso = "Produto cadastrado com sucesso!";
            } else {
                $erro = "Erro ao cadastrar produto.";
            }
        }
    }
}

}
// Buscar todos os produtos para listar
$sql = "SELECT id, nome, imagem, criado_em FROM produto ORDER BY id DESC";
$produto = mysqli_query($conexao, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos — Projeto SENAI</title>

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
            <h2 class="text-2xl font-bold text-gray-800">Cadastrar Produto</h2>
            <p class="text-gray-500 mt-1">Preencha os dados abaixo para criar um novo produto.</p>
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
            <form method="POST" action="cadastro_produto.php" enctype="multipart/form-data">

                <!-- Campo Nome -->
                <div class="mb-4">
                    <label for="nome" class="block text-gray-700 font-medium mb-2">
                        Nome
                    </label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        required
                        placeholder="Digite o nome"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo Descricao -->
                <div class="mb-4">
                    <label for="descricao" class="block text-gray-700 font-medium mb-2">
                        Descricao
                    </label>
                    <input
                        type="text"
                        id="descricao"
                        name="descricao"
                        required
                        placeholder="Digite a descrição"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo Preço -->
                <div class="mb-4">
                    <label for="preco" class="block text-gray-700 font-medium mb-2">
                        Preço
                    </label>
                    <input
                        type="number"
                        id="preco"
                        name="preco"
                        required
                        placeholder="Digite o preço"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <!-- Campo Estoque -->
                <div class="mb-4">
                    <label for="estoque" class="block text-gray-700 font-medium mb-2">
                        Estoque
                    </label>
                    <input
                        type="number"
                        id="estoque"
                        name="estoque"
                        required
                        placeholder="Digite o estoque"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Campo categoria -->
                <div class="mb-4">
                    <label for="categoria" class="block text-gray-700 font-medium mb-2">
                        Categoria
                    </label>
                    <input
                        type="text"
                        id="categoria"
                        name="categoria"
                        required
                        placeholder="Digite a categoaria"
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
                        required
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

        <!-- Lista de Produtos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Produtos Cadastrados</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="px-4 py-3 text-left rounded-tl-lg">ID</th>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-left">Imagem</th>
                        <th class="px-4 py-3 text-left rounded-tr-lg">Criado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($produto)): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3"><?php echo $u["id"]; ?></td>
                            <td class="px-4 py-3"><?php echo $u["nome"]; ?></td>
                            <td>
                                <?php if (!empty($u["imagem"])): ?>
                                    <img src="uploads/<?= $u["imagem"] ?>"
                                        width="60">
                                <?php else: ?>
                                    Sem imagem
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-500"><?php echo $u["criado_em"]; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>