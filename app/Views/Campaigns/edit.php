<?php 
    $campaignAudienceIds = [];
    foreach ($campaign["audiences"] as $audienceId) {
        $campaignAudienceIds[] = (string)$audienceId; // Assurez-vous que les IDs sont des chaînes
    }
?>
<div class="widget">    
    <a href="<?= base_url('campaigns/') ?>" class="btn text-white btn-flattered">
        <i class="fas fa-chevron-left"></i> Back
    </a>
    <h3 class="d-flex justify-content-between align-items-center">
        Edit campaign
    </h3>
    <form action="<?= base_url('campaigns/update') ?>" method="POST">
        <label class="form-label" for="audiences">Select Audiences</label>
        <select class="form-select" name="audiences[]" id="audiences" multiple>
            <?php foreach ($audiences as $audience): ?>
                <?php 
                // Vérifier si l'audience est sélectionnée
                $selected = in_array((string)$audience['_id'], $campaignAudienceIds) ? 'selected' : ''; 
                ?>
                <option value="<?= $audience['_id'] ?>" <?= $selected ?>><?= htmlspecialchars($audience['name']) ?></option>
            <?php endforeach ?>
        </select>
        <input type="hidden" name="id" value="<?= $campaign['_id'] ?>">
        <br>
        <label class="form-label" for="campaign_name">Name</label>
        <input class="form-control" type="text" name="campaign_name" id="campaign_name" required value="<?= esc($campaign['name']) ?>">

        <br>
        <label class="form-label" for="subject">Subject</label>
        <input class="form-control" type="text" name="subject" id="subject" required value="<?= esc($campaign['subject']) ?>">

        <br>
        <label class="form-label" for="contentHTML">HTML</label>
        <small class="form-text text-muted">(should be a html template - unsubscribe link should be like this {% unsubscribe %} - it will be automatically replaced by the application)</small>
        <textarea rows="10" class="form-control" type="text" name="contentHTML" id="contentHTML" required><?= htmlspecialchars($campaign['templateHTML']) ?></textarea>

        <br>
        <label class="form-label" for="contentPlainText">PlainText</label>
        <small class="form-text text-muted">(plain text version of email)</small>
        <textarea rows="10" class="form-control" type="text" name="contentPlainText" id="contentPlainText" required><?php if (isset($campaign['templatePlainText'])): ?><?= htmlspecialchars($campaign['templatePlainText']) ?><?php endif; ?></textarea>
        
        <br>
        <button type="submit" class="btn text-white btn-flattered-triade-2">Update Campaign</button>
    </form>
</div>
