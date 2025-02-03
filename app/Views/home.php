<style>
    .sub-widget-organiser {
        flex: 1;
        border-radius: 10px;
        padding: 15px;
        box-sizing: border-box;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sub-widget {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-sizing: border-box;
    }

    .sub-widget p {
        margin: 0;
        padding: 10px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        color: #ffffff;
        background-color: #A23A21; /* Couleur de fond pour la partie supérieure */
    }

    .sub-widget span {
        background-color: #ffffff; /* Couleur de fond pour la partie inférieure */
        padding: 10px;
        font-size: 28px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        display: block;/* Couleur de fond pour la partie supérieure */
    }
</style>

    <div class="widget">
        <!-- Emplacement pour les statistiques générales -->
        <h3>General Statistics</h3>
        <div class="sub-widget-organiser">
            <div class="sub-widget">
                <p><b>Subscribed contacts</b></p>
                <span id="total-subscribers"><?= $totalSubscribedContacts?></span>        
            </div>
            <div class="sub-widget">
                <p><b>Mails delivered</b></p>
                <span id="total-delivered"><?= ($totalDelivered = getCountByEventType($totalPerEventType, 'email.delivered')) ?? 'N/A' ?></span>
            </div>
            <div class="sub-widget">
                <p><b>Mails opened</b></p>
                <span id="total-opened"><?= ($totalOpened = getCountByEventType($totalPerEventType, 'email.opened')) ?? 'N/A' ?></span>
            </div>
            <div class="sub-widget">
                <p><b>Mails clicked</b></p>
                <span id="total-clicked"><?= ($totalClicked = getCountByEventType($totalPerEventType, 'email.clicked')) ?? 'N/A' ?></span>
            </div>
        </div>
        <!-- Ajoutez d'autres statistiques générales -->
    </div>
    <?php 
    function getCountByEventType($array, $eventType)
    {
        foreach ($array as $document) {
            if (isset($document->eventType) && $document->eventType === $eventType) {
                return $document->count;
            }
        }
        return null; // Retourne null si aucun objet correspondant n'est trouvé
    }
    ?>
</section>
