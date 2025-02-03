<div class="dashboard">
    <h2>Contacts</h2>
    <div class="widget">
        <?php if (!empty($allContacts) && is_array($allContacts)): ?>
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
                    <?php foreach ($allContacts as $contact): ?>
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
</div>
