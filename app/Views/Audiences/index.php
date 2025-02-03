    <div class="widget">    
        <h3 class="d-flex justify-content-between align-items-center">
            <span>Audiences</span>
            <a href="<?= base_url('/audiences/create') ?>" class="btn text-white btn-flattered">Create new audience</a>
        </h3>
        <?php if (!empty($allAudiences) && is_array($allAudiences)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Total contacts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allAudiences as $audience): ?>
                        <tr>
                            <td><?= esc($audience['name']) ?></td>
                            <td><?= esc($audience['contactsCount']) ?></td>
                            <td>
                                <!-- Bouton "Delete" redirigeant vers une URL de suppression spécifique à l'audience -->
                                <a href="<?= base_url('audiences/details/' . $audience['_id']) ?>" class="btn text-white" style="background-color: #214AA2">See contacts</a>
                                <a href="<?= base_url('audiences/edit/' . $audience['_id']) ?>" class="btn btn-secondary">Edit</a>
                                <a href="<?= base_url('audiences/delete/' . $audience['_id']) ?>" class="btn btn-danger" onclick="return confirmDelete()">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php else: ?>
            <h2>No audiences found</h2>
        <?php endif ?>
        <script>
    function confirmDelete() {
        // Boîte de dialogue de confirmation
        if (confirm('Are you sure you want to delete this audience?')) {
            return true; // Confirmer la suppression si l'utilisateur clique sur "OK"
        } else {
            return false; // Annuler la suppression si l'utilisateur clique sur "Annuler"
        }
        }
    </script>
    </div>
