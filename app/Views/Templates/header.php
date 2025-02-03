<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= ucfirst($currentPage)?> - Flattered Email Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="icon" href="favicon.png" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Montserrat';
        }
        body {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
        }
        .dashboard {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto; 
        }
        .widget {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .widget h2 {
            margin-top: 0;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .nav-pills .nav-link.active{
            background-color: #A23A21;
        }

        .sidebar {
            background-color: #333333;
            min-height: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .btn-flattered{
            background-color: #A23A21;
        }

        .btn-flattered-triade-2{
            background-color: #A2A221;
        }

        .domain-selector {
            margin-top: 10px;
            padding: 10px;
            background-color: #444;
            border-radius: 5px;
        }

        .domain-selector select {
            width: 100%;
            padding: 5px;
            border-radius: 3px;
            background-color: #555;
            color: white;
            border: 1px solid #666;
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar" style="width: 280px;">
        <a href="<?= base_url('/') ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Flattered Email Platform</span>
        </a>
        <div class="domain-selector">
            <select id="domainSelector" class="form-select form-select-sm" style="width: 200px;">
                <?php 
                $domains = get_all_active_domains();
                $activeDomain = get_active_domain();
                foreach ($domains as $domain): ?>
                    <option value="<?= $domain['id'] ?>" <?= ($activeDomain && $activeDomain['id'] == $domain['id']) ? 'selected' : '' ?>>
                        <?= esc($domain['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <script>
            document.getElementById('domainSelector').addEventListener('change', function() {
                const domainId = this.value;
                fetch(`/public/domains/set-active/${domainId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error changing domain: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error changing domain');
                });
            });
        </script>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="<?= base_url('/') ?>" class="nav-link text-white <?php echo ($currentPage === 'home') ? 'active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('campaigns/') ?>" class="nav-link text-white <?php echo ($currentPage === 'campaigns') ? 'active' : ''; ?>">
                    Campaigns
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('audiences/') ?>" class="nav-link text-white <?php echo ($currentPage === 'audiences') ? 'active' : ''; ?>">
                    Audiences
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('domains/') ?>" class="nav-link text-white <?php echo ($currentPage === 'domains') ? 'active' : ''; ?>">
                    Domains
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('logs/') ?>" class="nav-link text-white <?php echo ($currentPage === 'logs') ? 'active' : ''; ?>">
                    Logs
                </a>
            </li>
        </ul>
        <div class="copyrights">
        <p>
            <a href="<?= base_url('logout/') ?>" class="text-white">
                Logout
            </a>
        </p>
        <p>Page rendered in {elapsed_time} seconds</p>
        <?php 
        $activeDomain = get_active_domain();
        if ($activeDomain): ?>
            <p>Active Domain ID: <?= esc($activeDomain['id']) ?></p>
        <?php endif; ?>
        <p>Environment: <?= ENVIRONMENT ?></p>

        </div>
    </div>

    <div class="dashboard">