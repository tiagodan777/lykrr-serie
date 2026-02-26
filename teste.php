<?php
require_once 'includes/database-connection.php';
require_once 'includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$sql = "SELECT CONCAT(primeiro_nome, ' ', ultimo_nome) AS nome, foto_perfil
        FROM membro
        WHERE id = :id;";

/*$statement = $pdo->prepare($sql);
$statement->execute(['id' => $id]);
$member = $statement->fetch();
*/

$member = pdo($pdo, $sql, ['id' => $id])->fetch();

?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste</title>
</head>
<body>
    <main>
        <?= $id ?>
        <h1><?= html_escape($member['nome']) ?></h1>
        <img src="imagens/fotos-perfil/<?= html_escape($member['foto_perfil']) ?>" alt="Foto de perfil de <?= html_escape($member['nome']) ?>">
    </main>
</body>
</html>