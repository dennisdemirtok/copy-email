
        <div class="widget">
        <h3 class="d-flex justify-content-between align-items-center">
            <span>Campaigns                 
            </span>
            <a href="<?= base_url('/campaigns/create') ?>" class="btn text-white btn-flattered">Create new campaign</a>
        </h3>
        <?php
            if (!empty($totalPerCampaign) && isset($totalPerCampaign['generated_at'])) {
                echo "Last values generated on " . $totalPerCampaign['generated_at']->toDateTime()->format('d-M-Y H:i:s');
            } else {
                echo "No data available"; // Message de secours si le tableau est vide ou l'index n'est pas défini
            }
        ?>
        <a href="<?= base_url('/campaigns/reloadAnalytics') ?>" id="reload-data" onclick="showLoadingSpinner()">
            Reload Data <i id="loading-spinner" class="fas fa-spinner fa-spin" style="display:none;"></i>
        </a>

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

        <h2>Unsent campaigns</h2>
            <?php if (!empty($allCampaigns) && is_array($allCampaigns)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Subject</th>
                            <th>Template</th>
                            <th>Actions</th>
                            <th>Created at</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allCampaigns as $campaign): ?>
                            <tr>
                                <?php if($campaign['status'] != 'sent'): ?>
                                    <td><?= ($campaign['name'] !== null) ? esc($campaign['name']) : 'Unknown Campaign' ?></td>
                                    <td><?= ($campaign['subject'] !== null) ? esc($campaign['subject']) : '' ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-template" data-toggle="modal" data-target="#templateModal" data-contentHTML="<?= htmlspecialchars($campaign['templateHTML']) ?>" data-contentPlainText="<?= htmlspecialchars($campaign['templatePlainText']) ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-info open-send-modal" data-campaign-id="<?= $campaign['_id'] ?>">Send</a>
                                        <a href="<?= base_url('campaigns/edit/' . $campaign['_id'] ) ?>" class="btn btn-secondary">Edit</a>
                                        <a href="<?= base_url('campaigns/delete/'  . $campaign['_id']) ?>" class="btn btn-danger" onclick="return confirmDelete()">Delete</a>
                                    </td>
                                    <?php
                                    $utcDateTime = $campaign['created_at'];
                                    $dateTime = $utcDateTime->toDateTime();
                                    $formattedDate = $dateTime->format('Y-m-d H:i:s');
                                    ?>
                                    <td><?= esc($formattedDate) ?></td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h3>0 unsent campaigns have been found</h3>
            <?php endif ?>
            <h2>Sent campaigns</h2>
            <?php if (!empty($totalPerCampaign) && is_array($totalPerCampaign)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Subject</th>
                            <th>Template</th>
                            <th>Total Emails</th>
                            <th>Delivery Rate (%)</th>
                            <th>Open Rate (%)</th>
                            <th>Click Rate (%)</th>
                            <th>Sent at</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($totalPerCampaign['data'] as $campaign): ?>
                            <tr>
                                <td><?= ($campaign['campaignName'] !== null) ? esc($campaign['campaignName']) : 'Unknown Campaign' ?></td>
                                <td><?= ($campaign['subject'] !== null) ? esc($campaign['subject']) : '' ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-template" data-toggle="modal" data-target="#templateModal" data-contentHTML="<?= htmlspecialchars($campaign['templateHTML']) ?>" data-contentPlainText="<?= htmlspecialchars($campaign['templatePlainText']) ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                                <td><b><?= esc($campaign['totalEmails']) ?></b></td>
                                <td><b><?= number_format(esc($campaign['deliveryRate']), 2, '.', ''); ?> %</b> (<?= $campaign['totalEmails']*$campaign['deliveryRate']/100 ?>)</td>
                                <td><b><?= number_format(esc($campaign['openRate']), 2, '.', ''); ?> % </b>(<?= $campaign['totalEmails']*$campaign['openRate']/100 ?>)</td>
                                <td><b><?= number_format(esc($campaign['clickRate']), 2, '.', ''); ?> % </b>(<?= $campaign['totalEmails']*$campaign['clickRate']/100 ?>)</td>
                                <?php
                                    $utcDateTime = $campaign['sent_at'];
                                    $dateTime = $utcDateTime->toDateTime();
                                    $dateTime->setTimezone(new DateTimeZone('Europe/Paris')); // Remplacez "VotreFuseauHoraire" par le fuseau horaire souhaité
                                    $formattedDate = $dateTime->format('d-m-Y H:i:s');
                                    ?>
                                    <td><?= esc($formattedDate) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h3>0 sent campaigns have been found</h3>
            <?php endif ?>
        </div>

<!-- Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="templateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="height: 800px;">
            <div class="modal-header">
                <h5 class="modal-title" id="templateModalLabel">Template Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="templateTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="preview-tab" data-toggle="tab" href="#preview" role="tab" aria-controls="preview" aria-selected="true">Preview HTML</a>
                    </li>                    
                    <li class="nav-item">
                        <a class="nav-link" id="plaintext-tab" data-toggle="tab" href="#plaintext" role="tab" aria-controls="plaintext" aria-selected="false">PlainText</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="source-tab" data-toggle="tab" href="#source" role="tab" aria-controls="source" aria-selected="false">Source</a>
                    </li>
                </ul>
                
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="preview" role="tabpanel" aria-labelledby="preview-tab">
                        <iframe id="templatePreview" style="border: none; position: relative; height: 650px; width: 100%;"></iframe>
                    </div>
                    <div class="tab-pane fade" id="plaintext" role="tabpanel" aria-labelledby="plaintext-tab">
                        <pre id="templatePlainText" style="position: relative; height: 650px; width: 100%; overflow: auto; margin: 8px; margin-block-start: 1em;" ></pre>
                    </div>
                    <div class="tab-pane fade" id="source" role="tabpanel" aria-labelledby="source-tab">
                        <pre id="templateSource" style="position: relative; height: 650px; width: 100%; overflow: auto; margin: 8px; margin-block-start: 1em;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.view-template').forEach(button => {
        button.addEventListener('click', function() {
            const contentHTML = this.getAttribute('data-contentHTML');
            const contentPlainText = this.getAttribute('data-contentPlainText')
            // Pour la prévisualisation
            document.getElementById('templatePreview').srcdoc = contentHTML;
            // Pour afficher le code source
            document.getElementById('templateSource').textContent = contentHTML;

            document.getElementById('templatePlainText').textContent = contentPlainText;
        });
    });
</script>


<!-- Modal -->
<div class="modal fade" id="sendModal" tabindex="-1" role="dialog" aria-labelledby="sendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="height: 800px">
            <div class="modal-header">
                <h5 class="modal-title" id="sendModalLabel">Sending progress</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="sendResultContent"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this campaign?")
    }

    document.querySelectorAll('.open-send-modal').forEach(button => {
        button.addEventListener('click', function(event) {
            if (!confirm("Are you sure you want to send this campaign?")) {
                event.preventDefault();
            }else{
            event.preventDefault(); // Empêche le comportement par défaut du lien

            const campaignId = this.getAttribute('data-campaign-id');
            const url = `<?= base_url('campaigns/sendWithGoogleCloudFunction/') ?>${campaignId}`;
            const loader = '<p> Sending in progress</p><div class="loader"><i class="fas fa-spinner fa-spin"></i></div>'; // Remplacez ceci par votre propre icône de chargement
    
            const message = "<p>Your email campaign has started</p><p>The emails will be sent in the background, you can continue to use the application</p><p>You will probably need to reload the app sometimes to refresh the tables</p>";
            // Afficher l'icône de chargement avant de récupérer le contenu
            document.getElementById('sendResultContent').innerHTML = message;
            $('#sendModal').modal('show'); // Affiche la modal

            var sendModal = document.getElementById('sendModal');
            // Ajoutez un écouteur d'événements pour l'événement hide.bs.modal (événement de fermeture de la modal)
            sendModal.addEventListener('hide.bs.modal', function () {
                // Rechargez la page lorsque la modal est fermée
                location.reload();
            });

            // Ajoutez un écouteur d'événements pour l'événement blur (perte de focus de la modal)
            sendModal.addEventListener('blur', function () {
                // Rechargez la page lorsque la modal perd le focus
                location.reload();
            });

            // Appel AJAX pour récupérer le contenu de la page et l'afficher dans la modal
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('sendResultContent').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
</script>

<!-- Script JavaScript -->
<script>
    function showLoadingSpinner() {
        // Afficher le spinner de chargement
        var spinner = document.getElementById('loading-spinner');
        spinner.style.display = 'inline-block';
    }
</script>