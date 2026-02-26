<?php
require_once 'includes/database-connection.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$term = filter_input(INPUT_GET, 'search');
$show = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT) ?? 15;
$from = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT) ?? 0;

$count = 0;
$receitas = [];
$arguments = [];

if ($term) {
    $arguments['term1'] = '%' . $term . '%';
    $arguments['term2'] = '%' . $term . '%';
    $arguments['term3'] = '%' . $term . '%';
    $arguments['term4'] = '%' . $term . '%';
    $arguments['term5'] = '%' . $term . '%';

    $sql = "SELECT COUNT(titulo) FROM receita
            WHERE titulo LIKE :term1
            OR descricao LIKE :term2
            OR ingredientes LIKE :term3
            OR passos_preparacao LIKE :term4
            OR keywords LIKE :term5;";
    $count = pdo($pdo, $sql, $arguments)->fetchColumn();

    if ($count > 0) {
        $arguments['show'] = $show;
        $arguments['from'] = $from;
        $sql = "SELECT r.id, r.titulo, r.descricao, r.imagem_file
                FROM receita AS r
                WHERE titulo LIKE :term1
                OR descricao LIKE :term2
                OR ingredientes LIKE :term3
                OR passos_preparacao LIKE :term4
                OR keywords LIKE :term5
                LIMIT :show
                OFFSET :from;";
        $receitas = pdo($pdo, $sql, $arguments)->fetchAll();
    }
}

if ($count > $show) {
    $total_pages = ceil($count / $show);
    $current_page = ceil($from / $show) + 1;
}
?>
<main id="search">
    <?php foreach ($receitas as $receita) { ?>
        <div>
            <a href="article.php?id=<?= $receita['id'] ?>">
                <img src="imagens/comida/<?= html_escape($receita['imagem_file']) ?>" alt="">
            </a>
            <footer class="info">
                <h1><?= html_escape($receita['titulo']) ?></h1>
                <h2><?= html_escape($receita['descricao']) ?></h2>
                <div class="stats">
                    <div class="likes">
                        <span>10 mil</span>
                        <span class="material-symbols-outlined">favorite</span>
                    </div>
                    <div class="views">
                        <span>250 mil</span>
                        <span class="material-symbols-outlined">visibility</span>
                    </div>
                </div>
            </footer>
        </div>
    <?php } ?>
</main>
<aside>
    <?php if ($count > $show) { ?>
        <nav class="pagination">
            <ul>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li><a href="?search=<?= $term ?>&show=<?= $show ?>&from=<?= (($i - 1) * $show) ?>" class="<?= ($i == $current_page) ? 'ativo' : '' ?>"><?= $i ?></a></li>
                <?php } ?>
            </ul>
        </nav>
    <?php } ?>
</aside>
<?php require_once 'includes/footer.php'; ?>


