
<div class="widget">
    <a href="<?= base_url('campaigns/') ?>" class="btn text-white btn-flattered">
        <i class="fas fa-chevron-left"></i> Back
    </a>
    <h3 class="d-flex justify-content-between align-items-center">
        Create campaign
    </h3>
    <form action="<?= base_url('campaigns/store') ?>" method="POST">
        <!-- Champs pour sélectionner les audiences -->
        <label class="form-label"for="audiences">Select Audiences</label>
        <small class="form-text text-muted">(use Ctrl key to select multiple audiences)</small>
        <select class="form-select" name="audiences[]" id="audiences" multiple required>
            <!-- Liste des audiences disponibles -->
            <?php foreach ($audiences as $audience): ?>
                <option value="<?= $audience['_id'] ?>"><?= $audience['name'] ?></option>
            <?php endforeach ?>
        </select>

        <br>
        <label class="form-label" for="campaign_name">Name</label>
        <small class="form-text text-muted">(should not contain spaces or special characters)</small>
        <input class="form-control" type="text" name="campaign_name" id="campaign_name" required pattern="^[a-zA-Z0-9]+$">

        <br>
        <label class="form-label" for="subject">Subject</label>
        <small class="form-text text-muted">(it is the subject of the mail)</small>
        <input class="form-control" type="text" name="subject" id="subject" required>

        <br>
        <label class="form-label" for="contentHTML">HTML</label>
        <small class="form-text text-muted">(should be a html template - unsubscribe link should be like this {% unsubscribe %})</small>
        <textarea rows="10" class="form-control" type="text" name="contentHTML" id="contentHTML" required></textarea>

        <br>
        <label class="form-label" for="contentPlainText">PlainText</label>
        <small class="form-text text-muted">(plain text version of email)</small>
        <textarea rows="10" class="form-control" type="text" name="contentPlainText" id="contentPlainText" required></textarea>

        <br>
        <!-- Bouton de soumission pour créer la campagne -->
        <button type="submit" class="btn text-white btn-flattered-triade-2">Create Campaign</button>
    </form>
</div>