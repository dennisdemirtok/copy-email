<div class="widget">    
    <h3 class="d-flex">
        <a href="<?= base_url('audiences') ?>" class="btn text-white btn-flattered">
            <i class="fas fa-chevron-left"></i> Back
        </a>
    </h3>
    <form action="<?= base_url('audiences/update') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $audience['_id'] ?>">

        <label class="form-label" for="name">Audience Name:</label>
        <input class="form-control" type="text" name="name" id="name" value="<?= esc($audience['name']) ?>" required>

        <label class="form-label" for="csvFile">Upload CSV File:</label>
        <input class="form-control" type="file" name="csvFile" id="csvFile" accept=".csv" required>

        <br>
        <button type="submit" class="btn btn text-white btn-flattered-triade-2">Update Audience</button>
    </form>
</div>

