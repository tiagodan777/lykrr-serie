<?php
require_once 'includes/database-connection.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

define('MEDIA_TYPES', ['image/jpeg', 'image/png', 'image/gif',]);
define('FILE_EXTENSIONS', ['jpeg', 'jpg', 'png', 'gif',]);
define('MAX_SIZE', '5248800');
define('UPLOADS', dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'lykrr-serie' . DIRECTORY_SEPARATOR . 'imagens' . DIRECTORY_SEPARATOR . 'comida' . DIRECTORY_SEPARATOR);







$destination = '';
$autor = '';

$ingredientes = [];
$ingredientes_quantidades = [];
$temperos = [];
$temperos_quantidades = [];
$passos_preparacao = [];
$keywords = [];

$unidades_tempo = ['min', 'hr'];

$id = filter_input(INPUT_GET, 'id');
$temp = $_FILES['imagem']['tmp_name'] ?? '';
$erro_com_a_imagem = $_FILES['imagem']['error'] ?? '';










$receita = [
    'id' => $id,
    'titulo' => '',
    'descricao' => '',
    'tempo_preparo' => '',
    'unidade_tempo' => '',
    'numero_pessoas' => '',
    'ingredientes' => '',
    'ingredientes_quantidades' => '',
    'temperos' => '',
    'temperos_quantidades' => '',
    'passos_preparacao' => '',
    'keywords' => '',
    'imagem_file' => '',
    'youtube_id' => '',
    'categoria_id' => 0,
    'membro_id' => 0,
    'seo_title' => '', 
];

$erros = [
    'titulo' => '',
    'descricao' => '',
    'tempo_preparo' => '',
    'unidade_tempo' => '',
    'numero_pessoas' => '',
    'ingredientes' => '',
    'ingredientes_quantidades' => '',
    'temperos' => '',
    'temperos_quantidades' => '',
    'passos_preparacao' => '',
    'keywords' => '',
    'imagem_file' => '',
    'youtube_id' => '',
    'categoria_id' => '',
    'membro_id' => '',
    'seo_title' => '',
];











if ($id) {
     $sql = "SELECT r.id, r.titulo, r.descricao, r.data, r.tempo_preparo, r.unidade_tempo, 
                r.numero_pessoas, r.ingredientes, r.ingredientes_quantidades, r.temperos, r.temperos_quantidades, 
                r.passos_preparacao, r.keywords, r.imagem_file, r.youtube_id, r.categoria_id, 
                r.membro_id, r.seo_title, c.nome, CONCAT(m.primeiro_nome, ' ', m.ultimo_nome) AS autor
                FROM receita AS r
                JOIN categoria AS c ON r.categoria_id = c.id
                JOIN membro AS m ON r.membro_id = m.id
                WHERE r.id = :id;";
    $receita = pdo($pdo, $sql, ['id' => $id])->fetch();
    if (!$receita) {
        include 'error-page.php';
    }
}

$sql = "SELECT id, nome, descricao FROM categoria;";
$categorias = pdo($pdo, $sql)->fetchAll();

$sql = "SELECT id, CONCAT(primeiro_nome, ' ', ultimo_nome) AS nome FROM membro;";
$autores = pdo($pdo, $sql)->fetchAll();


$saved_image = $receita['imagem_file'] ? true : false;









if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $erros['imagem_file'] = ($temp === '' && $erro_com_a_imagem === 1) ? 'Ficheiro demasiado grande' : '';

    if ($temp && $_FILES['imagem']['error'] === 0) {
        $erros['imagem_file'] .= in_array(mime_content_type($temp), MEDIA_TYPES) ? '' : 'Tipo de ficheiro não permitido';
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $erros['imagem_file'] .= in_array($ext, FILE_EXTENSIONS) ? '' : 'Tipo de extensão não permitida';
        $erros['imagem_file'] .= ($_FILES['imagem']['size'] <= MAX_SIZE) ? '' : 'Ficheiro demasiado grande';
        if ($erros['imagem_file'] === '') {
            $receita['imagem_file'] = create_filename($_FILES['imagem']['name']);
            $destination = UPLOADS . $receita['imagem_file'];
        }
    }

    $receita['titulo'] = $_POST['titulo'] ?? '';
    $receita['descricao'] = $_POST['descricao'] ?? '';
    $receita['tempo_preparo'] = $_POST['tempo_preparo'] ?? '';
    $receita['unidade_tempo'] = $_POST['unidade_tempo'] ?? '';
    $receita['numero_pessoas'] = $_POST['numero_pessoas'] ?? '';
    
    $quant_ingredientes = $_POST['quant_ingredientes'] ?? 0;
    $quant_temperos = $_POST['quant_temperos'] ?? 0;
    $quant_passos_preparacao = $_POST['quant_passos_preparacao'] ?? 0;
    $quant_keywords = $_POST['quant_keywords'] ?? 0;

    for ($c = 1; $c <= $quant_ingredientes; $c++) {
        if (!empty($_POST["ingrediente_nome$c"])) {
            $ingredientes[] = $_POST["ingrediente_nome$c"];
            $ingredientes_quantidades[] = $_POST["ingrediente_qtd$c"] ?? '';
        }
    }
    for ($c = 1; $c <= $quant_temperos; $c++) {
        if (!empty($_POST["tempero_nome$c"])) {
            $temperos[] = $_POST["tempero_nome$c"];
            $temperos_quantidades[] = $_POST["tempero_qtd$c"] ?? '';
        }
    }
    for ($c = 1; $c <= $quant_passos_preparacao; $c++) {
        if (!empty($_POST["passo$c"])) {
            $passos_preparacao[] = $_POST["passo$c"];
        }
    }
    for ($c = 1; $c <= $quant_keywords; $c++) {
        if (!empty($_POST["keyword$c"])) {
            $keywords[] = preg_replace('/#/', '', $_POST["keyword$c"]);
        }
    }

    $receita['ingredientes'] = implode('#', $ingredientes);
    $receita['ingredientes_quantidades'] = implode('#', $ingredientes_quantidades);
    $receita['temperos'] = implode('#', $temperos);
    $receita['temperos_quantidades'] = implode('#', $temperos_quantidades);
    $receita['passos_preparacao'] = implode('#', $passos_preparacao);
    $receita['keywords'] = implode('#', $keywords);
    $receita['membro_id'] = $_POST['membro_id'] ?? 0;
    $receita['categoria_id'] = $_POST['categoria_id'] ?? 0;
    
    $receita['seo_title'] = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $receita['titulo']));
    $receita['seo_title'] = trim($receita['seo_title'], '-');







    $erros['titulo'] = is_text($receita['titulo'], 1, 64) ? '' : 'O título deve ter entre 1 e 64 caracteres';
    $erros['descricao'] = is_text($receita['descricao'], 1, 256) ? '' : 'A descrição deve ter entre 1 e 256 caracteres';
    $erros['tempo_preparo'] = is_number($receita['tempo_preparo'], 0, 360) ? '' : 'O tempo de preparo deve ser entre 1 e 360';
    $erros['unidade_tempo'] = in_array($receita['unidade_tempo'], $unidades_tempo) ? '' : 'A unidade de tempo deve ser minutos ou horas';
    $erros['numero_pessoas'] = is_number($receita['numero_pessoas'], 0, 16) ? '' : 'O número de pessoas deve ser entre 1 e 16';
    $erros['ingredientes'] = is_text($receita['ingredientes'], 0, 1024) ? '' : 'A soma de todos os caracteres de todos os ingredientes não deve ser maior que 1024';
    $erros['ingredientes_quantidades'] = is_text($receita['ingredientes_quantidades'], 0, 1024) ? '' : 'A soma de todos os caracteres de todas as quantiades não deve ser maior que 1024';
    
    $erros['temperos'] = is_text($receita['temperos'], 0, 1024) ? '' : 'A soma de todos os caracteres de todos os temperos não deve ser maior que 1024';
    $erros['temperos_quantidades'] = is_text($receita['temperos_quantidades'], 0, 1024) ? '' : 'A soma de todos os caracteres de todas as quantiades não deve ser maior que 1024';
    
    $erros['passos_preparacao'] = is_text($receita['passos_preparacao'], 0, 65244) ? '' : 'A soma de todos oscaracteres de todos os passos de preparação não deve ser maior que 65244';
    $erros['keywords'] = is_text($receita['keywords'], 0, 1024) ? '' : 'A soma de todos os caracteres de todas as keywords não deve ser maior que 1024';
    $erros['categoria_id'] = is_category_id($receita['categoria_id'], $categorias) ? '' : 'A categoria selectionada não é válida';

    $invalid = implode($erros);









    if ($invalid) {
        $erros['warning'] = 'Por favor corrige os error seguintes';
    } else {
        $arguments = $receita;
        if ($id) {
            unset($arguments['data'], $arguments['nome'], $arguments['autor'], $arguments['foto_perfil'], $arguments['youtube_id']);

            try {
                if ($temp) {
                    $imagick = new Imagick($temp);
                    $imagick->cropThumbnailImage(500, 500);
                    $imagick->writeImage($destination);
                }
                $sql = "UPDATE receita SET titulo = :titulo, descricao = :descricao, tempo_preparo = :tempo_preparo, unidade_tempo = :unidade_tempo, numero_pessoas = :numero_pessoas, ingredientes = :ingredientes,
                        ingredientes_quantidades = :ingredientes_quantidades, temperos = :temperos, temperos_quantidades = :temperos_quantidades, passos_preparacao = :passos_preparacao, keywords = :keywords, categoria_id = :categoria_id, membro_id = :membro_id, imagem_file = :imagem_file, seo_title = :seo_title
                        WHERE id = :id;";
                        
                $guardada = pdo($pdo, $sql, $arguments);

            } catch (Exception $e) {
                if (file_exists($destination)) {
                     unlink($destination);
                }
                throw $e;
            }
        } else {
            unset($arguments['id'], $arguments['data'], $arguments['nome'], $arguments['autor'], $arguments['foto_perfil'], $arguments['youtube_id']);
            try {
                if ($temp) {
                    $imagick = new Imagick($temp);
                    $imagick->cropThumbnailImage(500, 500);
                    $imagick->writeImage($destination);
                }
                
                $sql = "INSERT INTO receita (id, titulo, descricao, tempo_preparo, unidade_tempo, numero_pessoas, ingredientes, ingredientes_quantidades, temperos, temperos_quantidades, passos_preparacao, keywords, categoria_id, membro_id, imagem_file, seo_title) VALUES
                        (UUID(), :titulo, :descricao, :tempo_preparo, :unidade_tempo, :numero_pessoas, :ingredientes, :ingredientes_quantidades, :temperos, :temperos_quantidades, :passos_preparacao, :keywords, :categoria_id, :membro_id, :imagem_file, :seo_title);";
                
                $guardada = pdo($pdo, $sql, $arguments);


            } catch (Exception $e) {
                if (file_exists($destination)) {
                     unlink($destination);
                }
                throw $e;
            }
        }
    }
    $receita['imagem_file'] = $saved_image ? $receita['imagem_file'] : '';
}
?>







<main id="create-edit-article">
    <h1>Criar e Editar Receita 🧑🏻‍🍳</h1>
    <div id="opcoes-publicacao">
        <a href="#" class="pagina-atual">Receita</a>
        <a href="#">Foto</a>
        <a href="#">Quik</a>
        <a href="#">Vídeo Longo</a>
    </div>
    <form action="create-edit-article.php?id=<?= $id ?>" id="form" method="post" enctype="multipart/form-data">
        
        <input type="hidden" name="quant_ingredientes" id="quant_ingredientes" value="0">
        <input type="hidden" name="quant_temperos" id="quant_temperos" value="0">
        <input type="hidden" name="quant_passos_preparacao" id="quant_passos_preparacao" value="0">
        <input type="hidden" name="quant_keywords" id="quant_keywords" value="0">

        <section id="imagem-video">
            <div id="div-imagem">
                <?php if (!$receita['imagem_file']) { ?>
                    <div>
                        <label for="imagem">Upload de Foto</label>
                        <input type="file" name="imagem" id="imagem">
                    </div>
                <?php } else { ?>
                    <img src="imagens/comida/<?= html_escape($receita['imagem_file']) ?>" alt="<?= html_escape($receita['titulo']) ?> publicada por <?= html_escape($receita['autor']) ?>">
                <?php } ?>
            </div>
            <div id="div-video">
                <div>
                    <?php if (!$receita['youtube_id']) { ?>
                        <label for="video">Upload de Vídeo</label>
                        <input type="file" name="video" id="video">
                    <?php } else { ?>
                        <video src=""></video>
                    <?php } ?>
                </div>
            </div>
        </section>

        <section id="info-texto">
            <div id="div-titulo">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" required placeholder="Ex: Sushi, Bacalhau de Natas..." maxlength="64" value="<?= html_escape($receita['titulo']) ?>">
            </div>
            <div id="div-descricao">
                <label for="descricao">Descrição</label>
                <input type="text" name="descricao" id="descricao" placeholder="Como fazer o melhor..." maxlength="256" value="<?= html_escape($receita['descricao']) ?>">
            </div>
            <div id="div-tempo-preparo">
                <label for="tempo_preparo">Tempo de Preparo</label>
                <div id="tempo-unidade">
                    <input type="number" name="tempo_preparo" id="tempo_preparo" min="0" placeholder="Ex: 45" value="<?= html_escape($receita['tempo_preparo']) ?>">
                    <select name="unidade_tempo" id="unidade-tempo">
                        <option value="min" <?php if ($receita['unidade_tempo'] == 'min') { ?> selected <?php } ?>>Min</option>
                        <option value="hr" <?php if ($receita['unidade_tempo'] == 'hr') { ?> selected <?php } ?>>Hora</option>
                    </select>
                </div>
            </div>
            <div id="div-numero-pessoas">
                <label for="numero_pessoas">Nº de Pessoas</label>
                <select name="numero_pessoas" id="numero_pessoas">
                    <?php for($i = 1; $i <= 16; $i++) { ?>
                        <option value="<?= $i ?>" <?= ($i == $receita['numero_pessoas']) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php } ?>
                </select>
            </div>
            <div id="div-categoria">
                <label for="categoria_id">Categoria</label>
                <select name="categoria_id" id="categoria-id">
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>" <?= ($categoria['id'] == $receita['categoria_id']) ? 'selected' : '' ?>><?= html_escape($categoria['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="div-autor">
                <label for="membro_id">Autor</label>
                <select name="membro_id" id="membro_id" required>
                    <?php foreach ($autores as $autor) { ?>
                        <option value="<?= $autor['id'] ?>" <?= ($autor['id'] == $receita['membro_id']) ? 'selected' : '' ?>><?= html_escape($autor['nome']) ?></option>
                    <?php } ?>
                </select>
            </div>

            <div id="div-ingredientes">
                <div id="labels-ingredientes-quantidades">
                    <span>Ingredientes</span>
                    <span>Quantidades</span>
                </div>
                <div class="ingredientes-quantidades">
                    <?php
                        $c = 1;
                        $ingredientes = explode('#', $receita['ingredientes']);
                        $ingredientes_quantidades = explode('#', $receita['ingredientes_quantidades']);
                    ?>
                    <?php foreach ($ingredientes as $ingrediente) { ?>
                        <div class="inputs-ingredientes-quantidades">
                            <input type="text" name="ingrediente_nome<?= $c ?>" id="ingrediente_nome<?= $c ?>" placeholder="Ex: Salmão" maxlength="32" value="<?= $ingrediente ?>">
                            <input type="text" name="ingrediente_qtd<?= $c ?>" id="ingrediente_qtd<?= $c ?>" placeholder="Ex: 200g, 500ml..." maxlength="32" value="<?= $ingredientes_quantidades[$c-1] ?>">
                        </div>
                    <?php $c++ ?>
                    <?php } ?>
                </div>
                <input type="button" value="Adicionar Ingrediente" onclick="adicionaIngredienteEQuantidade()">
            </div>

            <div id="div-temperos">
                <div id="labels-temperos-quantidades">
                    <span>Temperos</span>
                    <span>Quantidades</span>
                </div>
                <div class="temperos-quantidades">
                    <?php
                        $t = 1;
                        $temperos = explode('#', $receita['temperos']);
                        $temperos_quantidades = explode('#', $receita['temperos_quantidades']);
                    ?>
                    <?php foreach ($temperos as $tempero) { ?>
                        <div class="inputs-temperos-quantidades">
                            <input type="text" name="tempero_nome<?= $t ?>" id="tempero_nome<?= $t ?>" placeholder="Ex: Sal, Louro..." maxlength="32" value="<?= htmlspecialchars($tempero) ?>">
                            <input type="text" name="tempero_qtd<?= $t ?>" id="tempero_qtd<?= $t ?>" placeholder="Ex: 20g, 2 colheres de chá..." value="<?= htmlspecialchars($temperos_quantidades[$t-1] ?? '') ?>">
                        </div>
                        <?php $t++; ?>
                    <?php } ?>
                </div>
                <input type="button" value="Adicionar Tempero" onclick="adicionaTempero()">
            </div>

            <div id="div-passos-preparacao">
                <div id="label-passos-preparacao">
                    <span>Passos de Preparação</span>
                </div>
                
                <?php
                    $p = 1;
                    $passos = explode('#', $receita['passos_preparacao']);
                ?>
                <?php foreach ($passos as $passo) { ?>
                    <div class="textareas-passos-preparacao">
                        <label for="passo<?= $p ?>">Passo <?= $p ?></label>
                        <textarea name="passo<?= $p ?>" id="passo<?= $p ?>" cols="40" rows="7"><?= htmlspecialchars($passo) ?></textarea>
                    </div>
                    <?php $p++; ?>
                <?php } ?>
                
                <input type="button" value="Adicionar Passo" id="btn-add-passo" onclick="adicionaPasso()">
            </div>

            <div id="div-tags">
                <div id="labels-tags">
                    <span>Keywords / Tags</span>
                </div>
                
                <?php
                    $k = 1;
                    $keywords = explode('#', $receita['keywords']);
                ?>
                <?php foreach ($keywords as $keyword) { ?>
                    <div class="inputs-tags">
                        <label for="keyword<?= $k ?>">Tag</label>
                        <input type="text" name="keyword<?= $k ?>" id="keyword<?= $k ?>" placeholder="Ex: #sushi" value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                    <?php $k++; ?>
                <?php } ?>
                
                <input type="button" value="Adicionar Tag" id="btn-add-tag" onclick="adicionaTag()">
            </div>

            <section id="acoes">
                <a href="#">Apagar</a>
                <input type="submit" value="Publicar">
            </section>
        </section>
    </form>
</main>

<script>
    let idNameIngrediente = 1;
    let idNameTempero = 1;
    let idNamePasso = 1;
    let idNameTag = 1;

    function adicionaIngredienteEQuantidade() {
        idNameIngrediente++;
        let div = document.createElement('div');
        div.className = 'inputs-ingredientes-quantidades';
        div.innerHTML = `
            <input type="text" name="ingrediente_nome${idNameIngrediente}" id="ingrediente_nome${idNameIngrediente}" placeholder="Ex: Salmão" maxlength="32">
            <input type="text" name="ingrediente_qtd${idNameIngrediente}" id="ingrediente_qtd${idNameIngrediente}" placeholder="Ex: 200g, 500ml..." maxlength="32">`;
        document.querySelector('.ingredientes-quantidades').appendChild(div);
    }

    function adicionaTempero() {
        idNameTempero++;
        let div = document.createElement('div');
        div.className = 'inputs-temperos-quantidades';
        div.innerHTML = `
            <input type="text" name="tempero_nome${idNameTempero}" id="tempero_nome${idNameTempero}" placeholder="Ex: Sal, Louro..." maxlength="32">
            <input type="text" name="tempero_qtd${idNameTempero}" id="tempero_qtd${idNameTempero}" placeholder="Ex: 20g, 2 colheres...">
        `;
        document.querySelector('.temperos-quantidades').appendChild(div);
    }

    function adicionaPasso() {
        idNamePasso++;
        let div = document.createElement('div');
        div.className = 'textareas-passos-preparacao';
        div.innerHTML = `
            <label for="passo${idNamePasso}">Passo ${idNamePasso}</label>
            <textarea name="passo${idNamePasso}" id="passo${idNamePasso}" cols="40" rows="7"></textarea>`;
        document.getElementById('div-passos-preparacao').insertBefore(div, document.getElementById('btn-add-passo'));
    }

    function adicionaTag() {
        idNameTag++;
        let div = document.createElement('div');
        div.className = 'inputs-tags';
        div.innerHTML = `
            <label for="keyword${idNameTag}">Tag</label>
            <input type="text" name="keyword${idNameTag}" id="keyword${idNameTag}" placeholder="Ex: #sushi">`;
        document.getElementById('div-tags').insertBefore(div, document.getElementById('btn-add-tag'));
    }

    function encontrarMaiorId(prefixo) {
        let elementos = document.querySelectorAll(`[id^="${prefixo}"]`);
        let maiorId = 0;

        elementos.forEach(elemento => {
            let idNumerico = parseInt(elemento.id.replace(prefixo, ''), 10);
            if (idNumerico > maiorId) {
                maiorId = idNumerico;
            }
        });

        return maiorId;
    }

    document.getElementById("form").addEventListener("submit", function(event) {
        document.getElementById('quant_ingredientes').value = encontrarMaiorId("ingrediente_nome");
        document.getElementById('quant_temperos').value = encontrarMaiorId("tempero_nome");
        document.getElementById('quant_passos_preparacao').value = encontrarMaiorId("passo");
        document.getElementById('quant_keywords').value = encontrarMaiorId("keyword");
    });
</script>

<?php require_once 'includes/footer.php'; ?>