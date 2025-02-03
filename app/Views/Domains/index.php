<div class="widget">
    <h3 class="d-flex justify-content-between align-items-center">
        <span>Domains</span>
        <div>
            <a href="<?= base_url('/domains/import') ?>" class="btn text-white btn-flattered me-2">Import from Resend</a>
        </div>
    </h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Domain Name</th>
                <th>Sender Email</th>
                <th>Pretty name to show</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($domains as $domain): ?>
                <tr>
                    <td><?= esc($domain['domain_name']) ?></td>
                    <td><?= esc($domain['sender_email'] ?? 'N/A') ?></td>
                    <td><?= esc($domain['pretty_name'] ?? 'N/A') ?></td>
                    <td>
                        <span class="badge bg-info"><?php esc($domain['status']) ?></span>
                    </td>
                    <td>
                        <a href="<?= base_url('domains/edit/' . (string)$domain['domain_id']) ?>" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>