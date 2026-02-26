<?php
require_once 'includes/database-connection.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$sql = "SELECT r.id, r.titulo, r.descricao, r.data, r.imagem_file, r.membro_id,
        CONCAT(m.primeiro_nome, ' ', m.ultimo_nome) AS nome,
        m.foto_perfil
        FROM receita AS r
        JOIN membro AS m ON r.membro_id = m.id;";
$articles = pdo($pdo, $sql)->fetchAll();

?>
<main id="index">
    <nav id="menu-selecao-topo">
        <ul>
            <li><a href="#"><img src="imagens/icons/hamburger.png" alt="ícone de Hamburger">Hamburger</a></li>
            <li><a href="#"><img src="imagens/icons/banana.png" alt="ícone de Banana">Banana</a></li>
            <li><a href="#"><img src="imagens/icons/cerejas.png" alt="ícone de Cerejas">Cerejas</a></li>
            <li><a href="#"><img src="imagens/icons/sushi.png" alt="ícone de Sushi">Sushi</a></li>
            <li><a href="#"><img src="imagens/icons/hamburger.png" alt="ícone de Hamburger">Hamburger</a></li>
            <li><a href="#"><img src="imagens/icons/banana.png" alt="ícone de Banana">Banana</a></li>
            <li><a href="#"><img src="imagens/icons/cerejas.png" alt="ícone de Cerejas">Cerejas</a></li>
            <li><a href="#"><img src="imagens/icons/sushi.png" alt="ícone de Sushi">Sushi</a></li>
        </ul>
    </nav>
    
    <?php foreach ($articles as $article) { ?>
        <article>
            <header>
                <a href="#"><img src="imagens/fotos-perfil/<?= html_escape($article['foto_perfil']) ?>" alt="Foto de perfil de <?= html_escape($article['nome']) ?>"> <span><?= html_escape($article['nome']) ?></span></a>
                <span class="data"><?= date('d F Y', strtotime(html_escape($article['data']))) ?></span>
            </header>
            <section>
                <a href="#">
                    <img src="imagens/comida/<?= html_escape($article['imagem_file']) ?>" alt="Foto de <?= $article['titulo'] ?> publicada por <?= html_escape($article['nome']) ?>">
                </a>
                <aside class="dados">
                    <h2><?= html_escape($article['titulo']) ?></h2>
                    <p><?= html_escape($article['descricao']) ?></p>
                </aside>
            </section>
            <aside>
                <div class="icones">
                    <span><img src="imagens/icons/hamburger.png" alt="Ícone de Hamburger" width="45px"></span>
                    <span class="material-symbols-outlined">comment</span>
                    <span class="material-symbols-outlined">ios_share</span>
                    <span class="material-symbols-outlined">bookmark</span>
                </div>
                <div class="numeros">
                    <span>1M</span>
                    <span>57k</span>
                </div>
            </aside>
        </article>
    <?php } ?>
    
</main>

<aside class="pessoas-desktop">
        <h2>Pessoas que estão no Lykrr</h2>
        <nav>
            <ul>
                <li>
                    <a href="#"><img src="imagens/fotos-perfil/tiago-p.png" alt="Foto de perfil de Tiago Daniel"></a>
                    <h3>Tiago Daniel</h3>
                    <button class="seguir">Seguir</button>
                </li>
                <li>
                    <a href="#"><img src="imagens/fotos-perfil/tiago-p.png" alt="Foto de perfil de Tiago Daniel"></a>
                    <h3>Tiago Daniel</h3>
                    <button class="a-seguir">Seguir</button>
                </li>
                <li>
                    <a href="#"><img src="imagens/fotos-perfil/tiago-p.png" alt="Foto de perfil de Tiago Daniel"></a>
                    <h3>Tiago Daniel</h3>
                    <button class="a-seguir">Seguir</button>
                </li>
            </ul>
        </nav>
    </aside>
<?php
require_once 'includes/footer.php';
?>