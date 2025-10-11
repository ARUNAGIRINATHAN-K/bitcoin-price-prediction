<?php
// analyse.php - receives uploaded file from index.php, validates (CSV/Excel), saves to uploads/ and displays result.
// This mirrors the validation previously in index.php.

$uploadMessage = '';
$uploadError = '';
$uploadedFilePath = '';

if (
  $_SERVER['REQUEST_METHOD'] === 'POST'
  && isset($_FILES['datafile'])
  && is_uploaded_file($_FILES['datafile']['tmp_name'])
) {
  $file = $_FILES['datafile'];

  if (!empty($file['error'])) {
    $uploadError = 'Upload error (code ' . intval($file['error']) . ').';
  } else {
    $allowedExts = ['csv', 'xls', 'xlsx'];
    $allowedMimes = [
      'text/csv',
      'text/plain',
      'application/csv',
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'application/octet-stream'
    ];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    $origName = basename($file['name']);
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExts, true)) {
      $uploadError = 'Only CSV and Excel files are allowed (extensions: csv, xls, xlsx).';
    } elseif ($file['size'] > $maxSize) {
      $uploadError = 'File exceeds maximum allowed size of 5 MB.';
    } else {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $detectedMime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
      if ($finfo) {
        finfo_close($finfo);
      }

      if (!in_array($detectedMime, $allowedMimes, true)) {
        if ($ext === 'csv' && (stripos($detectedMime, 'text/') === 0 || stripos($detectedMime, 'application/') === 0)) {
          $mimeOk = true;
        } else {
          $mimeOk = false;
        }
      } else {
        $mimeOk = true;
      }

      if (empty($mimeOk)) {
        $uploadError = 'Invalid file type detected. Please upload a CSV or Excel file.';
      } else {
        $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploadsDir)) {
          mkdir($uploadsDir, 0755, true);
        }

        $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
        $targetPath = $uploadsDir . DIRECTORY_SEPARATOR . $safeName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
          $uploadMessage = 'File uploaded successfully: ' . htmlspecialchars($origName);
          $uploadedFilePath = $targetPath;
        } else {
          $uploadError = 'Failed to move uploaded file.';
        }
      }
    }
  }
} else {
  $uploadError = 'No file received. Please upload a file from the form.';
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Analysis result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="p-4">
    <div class="container" style="max-width:800px;margin-top:40px;">
      <h3>Analysis result</h3>

      <?php if (!empty($uploadMessage)): ?>
        <div class="alert alert-success"><?php echo $uploadMessage; ?></div>
        <p>Saved to: <code><?php echo htmlspecialchars($uploadedFilePath); ?></code></p>
      <?php else: ?>
        <div class="alert alert-danger"><?php echo $uploadError; ?></div>
      <?php endif; ?>

      <a class="btn btn-secondary mt-3" href="index.php">Back to upload</a>
    </div>
  </body>
</html>
