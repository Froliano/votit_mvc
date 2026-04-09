<?php require __DIR__ . '/../header.php'; ?>

<div class="row align-items-center g-5 py-5">
  <div class="col-lg-6">
    <h1 class="display-5 fw-bold lh-1 mb-3"><?= htmlspecialchars($poll->getTitle()) ?></h1>
    <p class="lead"><?= nl2br(htmlspecialchars($poll->getDescription())) ?></p>
  </div>
  <div class="col-10 col-sm-8 col-lg-6">
    <h2>Résultats</h2>
    <div class="results">
      <?php 
      $totalVotes = array_sum(array_column($results, 'count'));
      foreach ($items as $index => $item) {
        $votes = $results[$item->getId()]['count'] ?? 0;
        $percent = $totalVotes ? ($votes / $totalVotes * 100) : 0;
      ?>
        <h3><?= htmlspecialchars($item->getName()) ?></h3>
        <div class="progress mb-2" role="progressbar" aria-label="<?= htmlspecialchars($item->getName()) ?>" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar progress-bar-striped progress-color-<?= $index ?>" style="width: <?= $percent ?>%">
            <?= htmlspecialchars($item->getName()) ?> <?= round($percent, 2) ?>%
          </div>
        </div>
      <?php } ?>
    </div>
    <div class="mt-5">
      <?php if (!empty($_SESSION['user'])) { ?>
        <form method="post" action="/poll/vote/?id=<?= $poll->getId() ?>">
          <h2>Votez pour ce sondage :</h2>
          <div class="btn-group" role="group" aria-label="Choix du sondage">
            <?php foreach ($items as $item) { ?>
              <input type="radio" class="btn-check" id="btncheck<?= $item->getId() ?>" autocomplete="off" value="<?= $item->getId() ?>" name="option" required>
              <label class="btn btn-outline-primary" for="btncheck<?= $item->getId() ?>"><?= htmlspecialchars($item->getName()) ?></label>
            <?php } ?>
          </div>
          <?php if (!empty($error)) { ?>
            <div class="alert alert-danger mt-2" role="alert">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php } ?>
          <div class="mt-2">
            <input type="submit" class="btn btn-primary" value="Voter">
          </div>
        </form>
      <?php } else { ?>
        <div class="alert alert-warning mt-3">
          Vous devez être connecté pour voter.
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<div class="row mt-5">
  <div class="col-lg-8">
    <h2 class="mb-3">Commentaires</h2>

    <?php if (!empty($comments)) { ?>
      <div class="list-group mb-4">
        <?php foreach ($comments as $comment) { ?>
          <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong><?= htmlspecialchars($comment->getUser()?->getNickname() ?? 'Utilisateur') ?></strong>
              <small class="text-body-secondary"><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($comment->getCreatedAt()))) ?></small>
            </div>
            <p class="mb-0"><?= nl2br(htmlspecialchars($comment->getContent())) ?></p>
          </div>
        <?php } ?>
      </div>
    <?php } else { ?>
      <div class="alert alert-light border mb-4">Aucun commentaire pour le moment.</div>
    <?php } ?>

    <?php if (!empty($_SESSION['user'])) { ?>
      <form method="post" action="/poll/comment/?id=<?= $poll->getId() ?>" class="card card-body">
        <h3 class="h5">Ajouter un commentaire</h3>
        <?php if (!empty($commentError)) { ?>
          <div class="alert alert-danger mt-2"><?= htmlspecialchars($commentError) ?></div>
        <?php } ?>
        <div class="mb-3">
          <label for="commentContent" class="form-label">Votre message</label>
          <textarea id="commentContent" name="content" class="form-control" rows="4" required></textarea>
        </div>
        <div>
          <input type="submit" class="btn btn-primary" value="Publier">
        </div>
      </form>
    <?php } else { ?>
      <div class="alert alert-warning">Connectez-vous pour laisser un commentaire.</div>
    <?php } ?>
  </div>
</div>
<a href="/" class="btn btn-secondary mt-4">Retour à la liste</a>
<?php require __DIR__ . '/../footer.php'; ?>
