<?php require __DIR__ . '/../header.php'; ?>
<div class="row text-center">
    <h2>Liste des sondages : <?= $category->getName() ?></h2>
    <div class="row">
        <?php foreach ($polls as $poll) {
            include __DIR__ . '/../poll/poll_part.php';
        } ?>
    </div>
</div>
<?php require __DIR__ . '/../footer.php'; ?>
