<!DOCTYPE html>
<html>
<head>
    <title>Explorateur de fichiers</title>
    <style>
        ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
        }

        li {
            border: 1px solid #ccc;
            margin: 5px;
            padding: 10px;
            min-width: 200px;
            text-align: center;
            cursor: pointer;
        }

        #retour {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<h2>Explorateur de fichiers</h2>

<!-- Affiche le formulaire avec le champ de saisie -->
<form method="post" action="" style="margin-bottom: 10px;">
    <label for="repertoire">Chemin du répertoire :</label>
    <input type="text" name="repertoire" id="repertoire" value="<?php echo htmlspecialchars($repertoire); ?>" required>
    <button type="submit">Explorer</button>
</form>

<!-- Affiche le bouton "Retour" -->
<?php if ($repertoire !== '/') : ?>
    <form method="post" action="" style="margin-bottom: 10px;">
        <input type="hidden" name="repertoire" value="<?php echo dirname($repertoire); ?>">
        <button type="submit">Retour</button>
    </form>
<?php endif; ?>

<!-- Affiche la liste des dossiers -->
<h3>Dossiers</h3>
<?php if (isset($contenu['directories'])) : ?>
    <ul>
        <?php foreach ($contenu['directories'] as $element) : ?>
            <li onclick="updateInput('<?php echo $repertoire . '/' . $element; ?>')"><?php echo $element; ?></li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Aucun dossier trouvé.</p>
<?php endif; ?>

<!-- Affiche la liste des fichiers -->
<h3>Fichiers</h3>
<?php if (isset($contenu['files'])) : ?>
    <ul>
        <?php foreach ($contenu['files'] as $element) : ?>
            <li><?php echo $element; ?></li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Aucun fichier trouvé.</p>
<?php endif; ?>


<!-- Script JavaScript pour mettre à jour le champ de saisie -->
<script>
    function updateInput(path) {
        document.getElementById('repertoire').value = path;
    }
</script>

</body>
</html>
