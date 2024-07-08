<?php 
 $bikes = $conn->query("SELECT  b.*,c.category, bb.name as brand from `bike_list` b inner join categories c on b.category_id = c.id inner join brand_list bb on b.brand_id = bb.id where md5(b.id) = '{$_GET['id']}' ");
 if($bikes->num_rows > 0){
     foreach($bikes->fetch_assoc() as $k => $v){
         $$k= stripslashes($v);
     }
    $upload_path = base_app.'/uploads/'.$id;
    $img = "";
    if(is_dir($upload_path)){
        $fileO = scandir($upload_path);
        if(isset($fileO[2]))
            $img = "uploads/".$id."/".$fileO[2];
        // var_dump($fileO);
    }
 }
?>
<section class="py-5">
    <div class="container px-4 px-lg-5 my-5">
        
        <div class="row gx-4 gx-lg-5 align-items-center">
            <div class="col-md-6">
                <img class="card-img-top mb-5 mb-md-0 border border-dark" loading="lazy" id="display-img" src="<?php echo validate_image($img) ?>" alt="..." />
                <div class="mt-2 row gx-2 gx-lg-3 row-cols-4 row-cols-md-3 row-cols-xl-4 justify-content-start">
                    <?php 
                        foreach($fileO as $k => $img):
                            if(in_array($img,array('.','..')))
                                continue;
                    ?>
                    <div class="col">
                        <a href="javascript:void(0)" class="view-image <?php echo $k == 2 ? "active":'' ?>"><img src="<?php echo validate_image('uploads/'.$id.'/'.$img) ?>" loading="lazy"  class="img-thumbnail" alt=""></a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6">
                <!-- <div class="small mb-1">SKU: BST-498</div> -->
                <h1 class="display-5 fw-bolder border-bottom border-primary pb-1"><?php echo $bike_model ?></h1>
                <p class="m-0"><small>Brand: <?php echo $brand ?></small> <br>
                <small><?php echo $category ?></small>
                </p>
                <div class="fs-5 mb-5">
                &#8369; <span id="price"><?php echo number_format($daily_rate) ?></span>
                <br>
                <span><small><b>Available Unit:</b> <span id="avail"><?php echo $quantity ?></span></small></span>
                </div>
                <button class="btn btn-outline-dark flex-shrink-0" type="button" id="book_bike">
                    <i class="bi-cart-fill me-1"></i>
                    Book this Bike
                </button>
                <p class="lead"><?php echo stripslashes(html_entity_decode($description)) ?></p>
                
            </div>
        </div>
    </div>
</section>
<!-- Related items section-->
<section class="py-5 bg-light">
    <div class="container px-4 px-lg-5 mt-5">
        <h2 class="fw-bolder mb-4">Related Bikes</h2>
        <div class="row gx-4 gy-2 gx-lg-5 row-cols-4 justify-content-center">
        <?php 
            $bikes = $conn->query("SELECT b.*,c.category, bb.name as brand from `bike_list` b inner join categories c on b.category_id = c.id inner join brand_list bb on b.brand_id = bb.id where b.status = 1 and (b.category_id = '{$category_id}' or b.brand_id = '{$brand_id}') and b.id !='{$id}' order by rand() limit 4 ");
            while($row = $bikes->fetch_assoc()):
        ?>
            <a class="col mb-5 text-decoration-none text-dark" href=".?p=view_bike&id=<?php echo md5($row['id']) ?>">
                <div class="card h-100 bike-item">
                    <!-- bike image-->
                    <img class="card-img-top w-100" src="<?php echo validate_image("uploads/thumbnails/".$id.".png") ?>" alt="..." />
                    <!-- bike details-->
                    <div class="card-body p-4">
                        <div class="">
                            <!-- bike name-->
                            <h5 class="fw-bolder"><?php echo $row['bike_model'] ?></h5>
                            <!-- bike price-->
                            <span><b>Price: </b><?php echo number_format($row['daily_rate']) ?></span>
                            <p class="m-0"><small>Brand: <?php echo $row['brand'] ?></small> <br>
                            <small><?php echo $row['category'] ?></small>
                            </p>
                            <p class="m-0 truncate-3"><small class="text-muted"><?php echo strip_tags(html_entity_decode(stripslashes($row['description']))) ?></small></p>
                        </div>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('.view-image').click(function(){
            var _img = $(this).find('img').attr('src');
            $('#display-img').attr('src',_img);
            $('.view-image').removeClass("active")
            $(this).addClass("active")
        })
        $('#book_bike').click(function(){
            if('<?php echo $_settings->userdata('id') ?>' <= 0){
                uni_modal("","login.php");
                return false;
            }
            uni_modal("Bike Rental Booking","book_to_rent.php?id=<?php echo isset($id) ? $id : '' ?>",'mid-large')
        })
    })
</script>