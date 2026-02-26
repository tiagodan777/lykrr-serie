<?php 
require_once 'includes/header.php'; 
require_once 'includes/database-connection.php';
require_once 'includes/functions.php';

$id = filter_input(INPUT_GET, 'id');
$path = 'imagens/comida/';

if (!$id) {
    include 'error-page.php';
}

$sql = "SELECT r.id, r.titulo, r.imagem_file
                FROM receita AS r
                WHERE r.id = :id;";
$receita = pdo($pdo, $sql, [$id])->fetch();
if (!$receita) {
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "SELECT imagem_file FROM receita WHERE id = :id";
    $file = pdo($pdo, $sql, [$id])->fetchColumn();

    $path = 'imagens/comida/' . $file;

    if (file_exists($path)) {
        unlink($path);
    }

    $sql = "DELETE FROM receita WHERE id = :id;";
    pdo($pdo, $sql, [$id]);
}
?>
<main id="article-delete">
    <section id="apagar">
        <h2>Apagar Receita</h2>
        <section id="dados">
            <p>Confirma para apagar a receita: <?= html_escape($receita['titulo']) ?></p>
            <img src="imagens/comida/<?= html_escape($receita['imagem_file']) ?>" alt="Foto de <?= html_escape($receita['titulo']) ?>">
        </section>
        <section id="opcoes">
            <form action="article-delete.php?id=<?= $id ?>" method="post">
                <a href="article.php?id=<?= $id ?>">Cancelar</a>
                <input type="submit" value="Confirmar">
            </form>
        </section>
    </section>
</main>
<?php require_once 'includes/footer.php'; ?>