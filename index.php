<!DOCTYPE html>
<html>
  <head>
    <title><?= $title ?? 'AgentCipher' ?></title>
</head>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<style>
    
body {
    background-color:white;
}
.navbar{
    background-color : white;
    box-shadow : 0 2px 4px rgba(0, 0, 0, 0.1);
    height : 65px;
    display : flex;
}
.navbar-brand{
    margin-left : 20px;
}
h1 {
            color: #09b3e4;
            font-family: 'racing sans one';
        }
        h1 span {
            color: #f57d11; 
            font-family: 'racing sans one';
        }
        .nav-link {
            color: black;
            font-weight: bold;
            margin-right: 20px;
            font-family: 'racing sans one';
        }
</style>

<body>
    <nav class=navbar>
        <div class="container-fluid">
     <h1>Agent <span class="highlight">Cipher</span></h1>
     <ul class="nav justify-content-end">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">Home</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">About</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Features</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Suggestion</a>
  </li>
</ul>
  </div>
    </nav>
    
  <main class="container mt-5">
    <div class="d-flex justify-content-center align-items-center" style="min-height:50vh;">
      <div class="card p-4 shadow" style="width:100%; max-width:540px;">
        <h4 class="card-title mb-3 text-center">Upload file for analysis</h4>

        <?php if (!empty($uploadMessage)): ?>
          <div class="alert alert-success" role="alert"><?php echo $uploadMessage; ?></div>
        <?php elseif (!empty($uploadError)): ?>
          <div class="alert alert-danger" role="alert"><?php echo $uploadError; ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" action="analyse.php">
          <div class="mb-3">
            <input class="form-control" type="file" name="datafile" id="datafile" required accept=".csv,.xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
          </div>
          <div class="d-grid">
            <button class="btn btn-primary" type="submit">Analyse</button>
          </div>
          <small class="text-muted d-block mt-2">You'll be redirected to an analysis page after upload.</small>
        </form>
      </div>
    </div>
  </main>

</body>
