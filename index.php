
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body>
      <form action="databaseClone.php" method="post">
          <div class="container">
              <div class="row justify-content-md-center">
                  <div class="col-md-8 ">
                    <h1 class="mt-4">Clone Database by PHP </h1>
                        <?php  
                            if(isset($_GET['message']) AND !empty($_GET['message'])){ 
                                $message =urldecode($_GET['message']);
                                ?>
                            <p style="background:#ff000011; border:1px solid #ff000022;padding:5px;"> <?= $message; ?></p>
                          <?php  }
                        ?>
                    <label for="">Source database</label>
                    <input type="text" class="form-control" name="source_db">

                    <label for="" class="pt-2">Destination database</label>
                    <input type="text" class="form-control" name="new_db">

                    <div class="d-flex justify-content-between">
                        <input type="submit" name="clone_db" class="form-control mt-4 btn btn-primary mr-2" value="Clone">
                        <span>&nbsp; &nbsp;</span>
                        <a href="index.php" class="form-control mt-4 btn btn-warning ml-2">
                            <button type="button" class="form-control btn btn-warning"> Reset </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>

