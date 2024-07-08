<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
<hr>
<div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-light elevation-1"><i class="fas fa-th-list"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Categories</span>
                <span class="info-box-number">
                  <?php 
                    $category = $conn->query("SELECT count(id) as total FROM categories  where status = '1'")->fetch_assoc()['total'];
                    echo number_format($category);
                  ?>
                  <?php ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-copyright"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Brands</span>
                <span class="info-box-number">
                  <?php 
                    $brands = $conn->query("SELECT count(id) as total FROM `brand_list` where status = '1' ")->fetch_assoc()['total'];
                    echo number_format($brands);
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-motorcycle"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Bikes</span>
                <span class="info-box-number">
                <?php 
                    $bike = $conn->query("SELECT count(id) as total FROM `bike_list` where status = '1' ")->fetch_assoc()['total'];
                    echo number_format($bike);
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Clients</span>
                <span class="info-box-number">
                <?php 
                    $clients = $conn->query("SELECT count(id) as total FROM `clients`")->fetch_assoc()['total'];
                    echo number_format($clients);
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
        </div>
<div class="container">
  <?php 
    $files = array();
    $bikes = $conn->query("SELECT * FROM `bike_list` order by rand() ");
    while($row = $bikes->fetch_assoc()){
      if(!is_dir(base_app.'uploads/'.$row['id']))
      continue;
      $fopen = scandir(base_app.'uploads/'.$row['id']);
      foreach($fopen as $fname){
        if(in_array($fname,array('.','..')))
          continue;
        $files[]= validate_image('uploads/'.$row['id'].'/'.$fname);
      }
    }
  ?>
  <div id="tourCarousel"  class="carousel slide" data-ride="carousel" data-interval="3000">
      <div class="carousel-inner h-100">
          <?php foreach($files as $k => $img): ?>
          <div class="carousel-item  h-100 <?php echo $k == 0? 'active': '' ?>">
              <img class="d-block w-100  h-100" style="object-fit:contain" src="<?php echo $img ?>" alt="">
          </div>
          <?php endforeach; ?>
      </div>
      <a class="carousel-control-prev" href="#tourCarousel" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#tourCarousel" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
      </a>
  </div>
</div>
