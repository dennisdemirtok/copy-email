<div class="widget">
    <h3>Edit Domain</h3>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('domains/edit/' . $domain['domain_id']) ?>" method="post">
        <div class="mb-3">
            <label for="domain_name" class="form-label">Domain Name</label>
            <input type="text" class="form-control" id="domain_name" value="<?= esc($domain['domain_name']) ?>" disabled>
            <small class="text-muted">Domain name cannot be changed as it is managed by Resend</small>
        </div>

        <div class="mb-3">
            <label for="sender_email" class="form-label">Sender Email *</label>
            <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('sender_email')) ? 'is-invalid' : '' ?>" 
                   id="sender_email" name="sender_email" 
                   value="<?= old('sender_email', $domain['sender_email'] ?? '') ?>" required>
            <?php if (isset($validation) && $validation->hasError('sender_email')): ?>
                <div class="invalid-feedback">
                    <?= $validation->getError('sender_email') ?>
                </div>
            <?php endif; ?>
            <small class="text-muted">This email will be used as the sender for this domain</small>
        </div>

        <div class="mb-3">
            <label for="pretty_name" class="form-label">Pretty Name *</label>
            <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('pretty_name')) ? 'is-invalid' : '' ?>" 
                   id="pretty_name" name="pretty_name" 
                   value="<?= old('pretty_name', $domain['pretty_name'] ?? '') ?>" required>
            <?php if (isset($validation) && $validation->hasError('pretty_name')): ?>
                <div class="invalid-feedback">
                    <?= $validation->getError('pretty_name') ?>
                </div>
            <?php endif; ?>
            <small class="text-muted">A friendly name to identify this domain in the interface</small>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <input type="text" class="form-control" id="status" value="<?= esc($domain['status']) ?>" disabled>
            <small class="text-muted">Domain status is managed by Resend</small>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= base_url('domains') ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-flattered text-white">Update Domain</button>
        </div>
    </form>
</div>