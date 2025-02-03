        <div class="widget">
            <h3>Logs</h3>
            <?php if (! empty($emailEvents) && is_array($emailEvents)): ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Subject</th>
                        <th>To</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emailEvents as $emailEvent): ?>
                        <tr>
                            <?php
                                $created_at = new DateTime($emailEvent['data']['created_at']);
                                $formattedDate = $created_at->format('d-m-Y H:i:s');
                            ?>
                            <td><?= esc($formattedDate) ?></td>
                            <td><?= esc($emailEvent['data']['data']['subject']) ?></td>
                            <td><?= esc($emailEvent['data']['data']['to'][0]) ?></td>
                            <td><?= esc($emailEvent['data']['type']) ?></td>
                        </tr>
                    <?php endforeach ?>
            </table>

            <?php else: ?>
            <h3>No email events has been found</h3>
            <?php endif ?>
        </div>
