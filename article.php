<?php
require_once 'includes/database-connection.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$id = filter_input(INPUT_GET, 'id');
if (!$id) {
    include 'error-page.php';
}

$sql = "SELECT r.id, r.titulo, r.descricao, r.data, r.tempo_preparo,
        r.unidade_tempo, r.numero_pessoas, r.ingredientes, r.ingredientes_quantidades,
        r.temperos, r.temperos_quantidades, r.passos_preparacao, r.keywords, r.imagem_file,
        r.youtube_id, r.categoria_id, r.membro_id,
        m.id, 
        CONCAT(m.primeiro_nome, ' ', m.ultimo_nome) AS nome,
        m.foto_perfil
        FROM receita AS r
        JOIN membro AS m ON r.membro_id = m.id
        WHERE r.id = :id;";

$receita = pdo($pdo, $sql, ['id' => $id])->fetch();
if (!$receita) {
    include 'error-page.php';
}

$ingredientes = explode('#', $receita['ingredientes']);
$ingredientes_quantidades = explode('#', $receita['ingredientes_quantidades']);
$temperos = explode('#', $receita['temperos']);
$temperos_quantidades = explode('#', $receita['temperos_quantidades']);
$passos_preparacao = explode('#', $receita['passos_preparacao']);
?>

<main id="article">
    <section id="topo">
        <section id="imagem">
            <img src="imagens/comida/<?= html_escape($receita['imagem_file']) ?>" alt="Foto de <?= html_escape($receita['titulo']) ?> publicada por <?= html_escape($receita['nome']) ?>">
        </section>
        <section id="info">
            <h1 id="titulo-receita"><?= html_escape($receita['titulo']) ?></h1>
            <h2 id="descricao-receita"><?= html_escape($receita['descricao']) ?></h2>
            <section id="dados-rapidos">
                <p><span>Tempo de preparo:</span> <?= html_escape($receita['tempo_preparo']) ?> <?= html_escape($receita['unidade_tempo']) ?></p>
                <p><span>Nº Pessoas</span> <?= html_escape($receita['numero_pessoas']) ?></p>
            </section>
            <section id="interacao-perfil-data">
                <div id="perfil-data">
                    <a href="#" id="user"><img src="imagens/fotos-perfil/<?= html_escape($receita['foto_perfil']) ?>" alt="Foto de perfil de <?= html_escape($receita['nome']) ?>"> <?= html_escape($receita['nome']) ?></a>
                    <p id="data-receita"><?= date('d F Y', strtotime(html_escape($receita['data']))) ?></p>
                </div>
                <div id="icones-numeros">
                    <div id="icones">
                        <div id="reacao">
                            <img src="imagens/icons/hamburger.png" alt="Ícone de Hamburger" width="45px">
                            <span class="material-symbols-outlined">comment</span>
                            <span class="material-symbols-outlined">ios_share</span>
                        </div>
                        <div id="guardar">
                            <span class="material-symbols-outlined">bookmark</span>
                        </div>
                    </div>
                    <div id="numeros">
                        <span>1M</span>
                        <span>57k</span>
                        <span> </span>
                        <span> </span>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <section id="needs-video">
        <section id="ingredientes">
            <h2>Ingredientes</h2>
            <ul>
                <?php
                    $i = 0;
                    while ($i < count($ingredientes)) {
                ?>  
                        <li><input type="checkbox" name="ingrediente<?= $i ?>" id="ingrediente<?= $i ?>"> <label for="ingrediente<?= $i ?>"><?= html_escape($ingredientes[$i]) ?> — <?= html_escape($ingredientes_quantidades[$i]) ?></label></li>
                <?php
                        $i++;
                    }
                ?>
            </ul>
        </section>
        <section id="temperos">
            <h2>Temperos</h2>
            <ul>
                <?php
                    $i = 0;
                    while ($i < count($temperos)) {
                ?>  
                        <li><input type="checkbox" name="temperos<?= $i ?>" id="temperos<?= $i ?>"> <label for="temperos<?= $i ?>"><?= html_escape($temperos[$i]) ?> — <?= html_escape($temperos_quantidades[$i]) ?></label></li>
                <?php
                    $i++;
                }
                ?>
            </ul>
        </section>
        <section id="video">
            <?php if ($receita['youtube_id']) { ?>

                <video src="<?= html_escape($receita['youtube_id']) ?>" controls></video>
            
            <?php } ?>
        </section>
    </section>
    <section id="passos">
        <h2>Passo de Prepação</h2>
        <nav>
            <ul>
                <?php
                    $i = 1;
                    foreach ($passos_preparacao as $passo) {
                ?>  
                    <li><span>Passo <?= $i ?></span> <span class="passo"><?= html_escape($passo) ?></span></li>
                <?php
                    $i++;
                }
                ?>
            </ul>
        </nav>
    </section>
    <aside id="seccao-nutricional">
        <h2>Secção Nutricional</h2>
        <ul>
            <li><span>Calorias</span> <span>600 Kcal</span> </li>
            <li><span>Proteína</span> <span>20g</span></li>
            <li><span>Hidratos Carbono</span> 30g</li>
            <li><span>Calorias</span>  <span>600 Kcal</span> </li>
            <li><span>Proteína</span>  <span>20g</span></li>
            <li><span>Hidratos Carbono</span>   30g</li>
            <li><span>Calorias</span>  <span>600 Kcal</span> </li>
            <li><span>Proteína</span>  <span>20g</span></li>
            <li><span>Hidratos Carbono</span>   <span>30g</span></li>
        </ul>
    </aside>
    <aside id="mais-conteudos">
        <h3>Mais Conteúdos que vais gostar 🧑🏻‍🍳</h3>
        <div>
            <a href="#"><img src="imagens/comida/hamburger.png" alt="Foto de Hamburger"></a>
            <a href="#"><img src="imagens/comida/sushi.png" alt="Foto de Sushi"></a>
            <a href="#"><img src="imagens/comida/salada.png" alt="Foto de Salada"></a>
            <a href="#"><img src="imagens/comida/hamburger.png" alt="Foto de Hamburger"></a>
            <a href="#"><img src="imagens/comida/sushi.png" alt="Foto de Sushi"></a>
            <a href="#"><img src="imagens/comida/salada.png" alt="Foto de Salada"></a>
            <a href="#"><img src="imagens/comida/hamburger.png" alt="Foto de Hamburger"></a>
            <a href="#"><img src="imagens/comida/sushi.png" alt="Foto de Sushi"></a>
            <a href="#"><img src="imagens/comida/salada.png" alt="Foto de Salada"></a>
        </div>
    </aside>
</main>

<?php
require_once 'includes/footer.php';
?>