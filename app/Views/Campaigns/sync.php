<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Synchronize Resend Events</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('campaigns/sync-events') ?>" method="POST">
                        <div class="form-group">
                            <label for="campaignId">Campaign ID</label>
                            <select name="campaignId" id="campaignId" class="form-control" required>
                                <option value="">Select a campaign</option>
                                <?php foreach ($campaigns as $campaign): ?>
                                    <option value="<?= $campaign['_id'] ?>">
                                        <?= $campaign['name'] ?> (<?= $campaign['subject'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="resendEvents">Resend Events (JSON)</label>
                            <textarea name="resendEvents" id="resendEvents" class="form-control" rows="10" required
                                    placeholder='[{
    "id": "8975b0cf-897c-4223-96d9-d9cd8b1c2b0e",
    "to": ["example@email.com"],
    "subject": "Campaign Subject",
    "created_at": "2024-11-15 09:59:58.334031+00",
    "last_event": "opened"
}]'></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Synchronize</button>
                    </form>
                </div>
            </div>

            <?php if (isset($result)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Synchronization Results</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Inserted Events</th>
                                    <td><?= $result['inserted_count'] ?? 0 ?></td>
                                </tr>
                                <tr>
                                    <th>Skipped Events (already exist)</th>
                                    <td><?= $result['skipped_count'] ?? 0 ?></td>
                                </tr>
                                <?php if (!empty($result['errors'])): ?>
                                    <tr>
                                        <th>Errors</th>
                                        <td>
                                            <ul>
                                                <?php foreach ($result['errors'] as $error): ?>
                                                    <li>
                                                        Email ID: <?= $error['email_id'] ?><br>
                                                        Error: <?= $error['error'] ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // JSON validation before submission
    document.querySelector('form').addEventListener('submit', function(e) {
        try {
            const jsonText = document.getElementById('resendEvents').value;
            JSON.parse(jsonText);
        } catch (e) {
            alert('Invalid JSON format. Please check the format.');
            event.preventDefault();
        }
    });
</script>
