<div class="widget">        
    <a href="<?= base_url('audiences/') ?>" class="btn text-white btn-flattered">
        <i class="fas fa-chevron-left"></i> Back
    </a>
    <h3 class="d-flex justify-content-between align-items-center">
        <span>Contacts of audience "<?= $audience['name'] ?>"</span>
        <a href="<?= base_url('audiences/edit/' . $audience['_id']) ?>" class="btn text-white btn-flattered mb-3">Edit</a>
    </h3>
    <?php if (!empty($audienceContacts) && is_array($audienceContacts)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Subscribed</th>
                    <!-- Ajoutez d'autres colonnes si nécessaire -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($audienceContacts as $contact): ?>
                    <tr>
                        <td><?= esc($contact['email']) ?></td>
                        <td><?= esc($contact['firstName']) ?></td>
                        <td><?= esc($contact['lastName']) ?></td>
                        <td><?= ($contact['subscribed']) ? 'Yes' : 'No' ?></td>
                        <!-- Utilisez une autre condition si nécessaire pour le champ subscribed -->
                        <!-- Ajoutez d'autres cellules pour les autres champs -->
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <h2>No contacts found</h2>
    <?php endif ?>
</div>
