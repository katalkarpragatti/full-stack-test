<?php
require_once 'db/db.php';

// Fetch Tabs
$tabs = $pdo->query("SELECT * FROM tabs ORDER BY sort_order ASC")->fetchAll();

// Fetch Slides for each tab group
$dataset = [];
foreach ($tabs as $tab) {
    $stmt = $pdo->prepare("SELECT * FROM slides WHERE tab_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$tab['id']]);
    $dataset[$tab['id']] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DelphianLogic Home</title>

    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="files/styles/bootstrap.min.css" >
    <!-- Slick Slider Core CSS -->
    <link rel="stylesheet" type="text/css" href="files/styles/slick.css"/>
    <link rel="stylesheet" type="text/css" href="files/styles/slick-theme.css"/>
    <link rel="stylesheet" type="text/css" href="files/styles/styles.css"/>
    
</head>
<body>

<div class="showcase-wrapper">
    <div class="container">
        
        <!-- Header -->
        <header class="header-section">
            <h1>DelphianLogic in Action</h1>
            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo</p>
        </header>

        <!-- WEB / TABLET VIEW: Horizontal 3-Column layout -->
        <div class="d-none d-lg-block">
            <div class="showcase-container">
                <div class="row g-0">
                    
                    <!-- Column 1: Tabs -->
                    <div class="col-lg-3 tab-navigation-pane">
                        <?php foreach ($tabs as $index => $tab): ?>
                            <div class="tab-nav-item <?= $index === 0 ? 'active' : '' ?>" data-tab-id="<?= $tab['id'] ?>">
                                <?= $tab['icon_svg'] ?>
                                <span><?= htmlspecialchars($tab['title']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Column 2: Content Sliders -->
                    <div class="col-lg-5 content-slider-column">
                        <?php foreach ($tabs as $index => $tab): ?>
                            <div class="content-slider" id="text-slider-<?= $tab['id'] ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                                <?php if (!empty($dataset[$tab['id']])): ?>
                                    <?php foreach ($dataset[$tab['id']] as $slide): ?>
                                        <div class="slide-text-item">
                                            <span class="badge-pill"><?= htmlspecialchars($slide['badge_text']) ?></span>
                                            <h3 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h3>
                                            <a href="<?= htmlspecialchars($slide['learn_more_url']) ?>" class="learn-more-btn">
                                                Learn More 
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="slide-text-item">
                                        <h3 class="slide-title">No content initialized.</h3>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Column 3: Visual Sliders (Synced) -->
                    <div class="col-lg-4 image-slider-column">
                        <?php foreach ($tabs as $index => $tab): ?>
                            <div class="image-slider" id="image-slider-<?= $tab['id'] ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                                <?php if (!empty($dataset[$tab['id']])): ?>
                                    <?php foreach ($dataset[$tab['id']] as $slide): ?>
                                        <div>
                                            <img src="<?= htmlspecialchars($slide['image_path']) ?>" class="slide-image-item" alt="Slide Visual Content">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div>
                                        <div class="slide-image-item d-flex align-items-center justify-content-center bg-dark text-muted">1:1 Mockup</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- MOBILE VIEW: Column 1 converts to Accordion -->
        <div class="d-lg-none">
            <div class="accordion mobile-accordion-section" id="mobileShowcaseAccordion">
                <?php foreach ($tabs as $index => $tab): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?= $tab['id'] ?>">
                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $tab['id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse-<?= $tab['id'] ?>">
                                <?= $tab['icon_svg'] ?>
                                <span><?= htmlspecialchars($tab['title']) ?></span>
                                <span class="accordion-indicator"><?= $index === 0 ? '<img src="files/images/minus-01.svg" alt="Minus">' : '<img src="files/images/plus-01.svg" alt="Plus">' ?></span>
                            </button>
                        </h2>
                        <div id="collapse-<?= $tab['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading-<?= $tab['id'] ?>" data-bs-parent="#mobileShowcaseAccordion">
                            <div class="accordion-body">
                                <div class="mobile-slider-wrap" id="mobile-slider-<?= $tab['id'] ?>">
                                    <?php if (!empty($dataset[$tab['id']])): ?>
                                        <?php foreach ($dataset[$tab['id']] as $slide): ?>
                                            <div class="mobile-slide-item" style="background: url('<?= htmlspecialchars($slide['image_path']) ?>');">
                                                <div>
                                                    <span class="badge-pill"><?= htmlspecialchars($slide['badge_text']) ?></span>
                                                    <h3 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h3>
                                                    <a href="<?= htmlspecialchars($slide['learn_more_url']) ?>" target="_blank" class="learn-more-btn">
                                                        Learn More 
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" fill="#ffffff" id="Layer_1" x="0px" y="0px" width="18px" height="18px" viewBox="0 0 18 18" enable-background="new 0 0 18 18" xml:space="preserve"><path d="M11,4l-0.883,0.883l3.492,3.492H2v1.25h11.608l-3.492,3.492L11,14l5-5L11,4z"/></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="mobile-slide-item">
                                            <h3 class="slide-title">No content initialized.</h3>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="files/scripts/jquery-3.6.0.min.js"></script>
<script src="files/scripts/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="files/scripts/slick.min.js"></script>
<script type="text/javascript" src="files/scripts/init.js"></script>

</body>
</html>