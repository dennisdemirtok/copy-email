<div class="widget">
    <a href="<?= base_url('audiences/') ?>" class="btn text-white btn-flattered">
        <i class="fas fa-chevron-left"></i> Back
    </a>
    <h3 class="d-flex justify-content-between align-items-center">
        <span>Create audience</span>
    </h3>
    <form action="<?= base_url('audiences/store') ?>" method="POST" enctype="multipart/form-data">
        <label class="form-label" for="name">Audience Name</label>
        <input class="form-control" type="text" name="name" id="name" required>

        <br>
        <label class="form-label" for="csvFile">Upload CSV File</label>
        <small class="form-text text-muted">(csv file with the format : Email,First Name,Last Name,Email Marketing Consent)</small>
        <input class="form-control" type="file" name="csvFile" id="csvFile" accept=".csv" required>

        <br>
        <button type="submit" class="btn text-white btn-flattered-triade-2">Create Audience</button>
    </form>
</div>

