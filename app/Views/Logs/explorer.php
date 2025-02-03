<div class="widget">
    <h1>Email Events Grouped by Unique Email</h1>
        <?php $index = 1; // Initialize index counter ?>
        <?php foreach ($emails as $email): ?>
            <h2><?= $index++; ?>. <?= esc($email->_id);?></h2> <!-- Display index with email ID -->
            <?php foreach ($email->events as $event): ?>
                <p><?= esc($event['data']['type']); ?></p>
            <?php endforeach; ?>
        <?php endforeach; ?>
</div>
