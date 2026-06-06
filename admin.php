<?php
require_once 'db/db.php';

$message = '';

// Handle Slide Deletion
if (isset($_GET['delete_slide'])) {
    $slide_id = (int)$_GET['delete_slide'];
    // Fetch image path to delete file from uploads folder
    $stmt = $pdo->prepare("SELECT image_path FROM slides WHERE id = ?");
    $stmt->execute([$slide_id]);
    $slide = $stmt->fetch();
    if ($slide && file_exists($slide['image_path'])) {
        unlink($slide['image_path']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM slides WHERE id = ?");
    $stmt->execute([$slide_id]);
    $message = "Slide deleted successfully.";
}

// Handle Slide Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_slide'])) {
    $tab_id = (int)$_POST['tab_id'];
    $badge_text = htmlspecialchars($_POST['badge_text']);
    $title = htmlspecialchars($_POST['title']);
    $learn_more_url = htmlspecialchars($_POST['learn_more_url']);
    $sort_order = (int)$_POST['sort_order'];
    
    // Image Upload Process
    if (isset($_FILES['slide_image']) && $_FILES['slide_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['slide_image']['tmp_name'];
        $fileName = $_FILES['slide_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $pdo->prepare("INSERT INTO slides (tab_id, badge_text, title, learn_more_url, image_path, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$tab_id, $badge_text, $title, $learn_more_url, $dest_path, $sort_order]);
                $message = "Slide uploaded and created successfully.";
            } else {
                $message = "Error moving uploaded file.";
            }
        } else {
            $message = "Upload failed. Allowed formats: JPG, PNG, WEBP.";
        }
    } else {
        $message = "Please select a slide image.";
    }
}

// Read All Tabs
$tabs = $pdo->query("SELECT * FROM tabs ORDER BY sort_order ASC")->fetchAll();

// Read All Slides with associated Tab name
$slides = $pdo->query("SELECT s.*, t.title AS tab_title FROM slides s JOIN tabs t ON s.tab_id = t.id ORDER BY s.tab_id, s.sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="files/scripts/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: system-ui, -apple-system, sans-serif; }
        .card { border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Slider Admin</a>
        <a href="index.php" class="btn btn-outline-light btn-sm" target="_blank">Home</a>
    </div>
</nav>

<div class="container my-5">
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Create Form Column -->
        <div class="col-lg-4 mb-4">
            <div class="card p-4">
                <h4 class="mb-4">Create Slide Item</h4>
                <form action="admin.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="create_slide" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Tab Placement</label>
                        <select class="form-select" name="tab_id" required>
                            <?php foreach ($tabs as $tab): ?>
                                <option value="<?= $tab['id'] ?>"><?= htmlspecialchars($tab['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category Badge</label>
                        <input type="text" class="form-control" name="badge_text" placeholder="e.g. DIGITAL LEARNING INFRASTRUCTURE" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slide Main Title</label>
                        <input type="text" class="form-control" name="title" placeholder="e.g. Usability Enhancement..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Learn More URL</label>
                        <input type="text" class="form-control" name="learn_more_url" value="#" placeholder="#">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slide Aspect 1:1 Image</label>
                        <input type="file" class="form-control" name="slide_image" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Display Order Index</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Add Slide</button>
                </form>
            </div>
        </div>

        <!-- Data List Column -->
        <div class="col-lg-8">
            <div class="card p-4">
                <h4 class="mb-4">Configured Slides</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Thumbnail</th>
                                <th>Tab Placement</th>
                                <th>Badge & Title</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($slides) > 0): ?>
                                <?php foreach ($slides as $slide): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars($slide['image_path']) ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($slide['tab_title']) ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted d-block text-uppercase"><?= htmlspecialchars($slide['badge_text']) ?></small>
                                            <strong><?= htmlspecialchars($slide['title']) ?></strong>
                                        </td>
                                        <td class="text-end">
                                            <a href="admin.php?delete_slide=<?= $slide['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this slide?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No slide configurations found. Add one on the left.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>