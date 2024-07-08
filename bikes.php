<?php 
$title = "";
$sub_title = "";
if(isset($_GET['c'])){
    $cat_qry = $conn->query("SELECT * FROM categories where md5(id) = '{$_GET['c']}'");
    if($cat_qry->num_rows > 0){
        $result =$cat_qry->fetch_assoc();
        $title = $result['category'];
        $cat_description = $result['description'];
    }
}
if(isset($_GET['s'])){
    $sub_cat_qry = $conn->query("SELECT * FROM brand_list where md5(id) = '{$_GET['s']}'");
    if($sub_cat_qry->num_rows > 0){
        $result =$sub_cat_qry->fetch_assoc();
        $title = $result['name'];
    }
}
?>
<!-- Header-->
<header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder"><?php echo $title ?></h1>
            <p class="lead fw-normal text-white-50 mb-0"><?php echo $sub_title ?></p>
        </div>
    </div>
</header>
<!-- Section-->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="<?php echo (isset($_GET['c']))? 'col-md-9': 'col-md-12' ?>">
                <?php 
                    if(isset($_GET['search'])){
                        echo "<h4 class='text-center'><b>Search Result for '".$_GET['search']."'</b></h4><hr/>";
                    }
                ?>
                <div class="row gx-2 gx-lg-2 row-cols-1 row-cols-sm-1 row-cols-md-3 row-cols-xl-4">
                
                    <?php 
                        $whereData = "";
                        if(isset($_GET['search'])){
                            if(!empty($whereData) ) $whereData.= " and ";
                            $whereData .= " and (b.bike_model LIKE '%{$_GET['search']}%' or c.category LIKE '%{$_GET['search']}%' or b.description LIKE '%{$_GET['search']}%' or bb.name LIKE '%{$_GET['search']}%')";
                        }
                        if(isset($_GET['c'])){
                            if(!empty($whereData) ) $whereData.= " and ";
                            $whereData .= " and (md5(category_id) = '{$_GET['c']}')";
                        }
                        if(isset($_GET['s'])){
                            if(!empty($whereData) ) $whereData.= " and ";
                            $whereData .= " and (md5(brand_id) = '{$_GET['s']}')";
                        }
                        $sql = "SELECT b.*,c.category, bb.name as brand from `bike_list` b inner join categories c on b.category_id = c.id inner join brand_list bb on b.brand_id = bb.id  where b.status = 1 {$whereData} order by rand()";
                        $bikes = $conn->query($sql);
                        while($row = $bikes->fetch_assoc()):
                    ?>
                    <a class="col-md-12 mb-5 text-decoration-none text-dark" href=".?p=view_bike&id=<?php echo md5($row['id']) ?>">
                        <div class="card bike-item">
                            <!-- bike image-->
                            <img class="card-img-top w-100" src="<?php echo validate_image('uploads/thumbnails/'.$row['id'].'.png') ?>" loading="lazy" alt="..." />
                            <!-- bike details-->
                            <div class="card-body p-4">
                                <div class="">
                                    <!-- bike name-->
                                    <h5 class="fw-bolder"><?php echo $row['bike_model'] ?></h5>
                                    <!-- bike price-->
                                <span><b>Daily Rate: </b><?php echo number_format($row['daily_rate']) ?></span>
                                </div>
                                <p class="m-0"><small>Brand: <?php echo $row['brand'] ?></small> <br>
                                <small><?php echo $row['category'] ?></small>
                                </p>
                                <p class="m-0 truncate-3"><small><?php echo strip_tags(html_entity_decode(stripslashes($row['description']))) ?></small></p>
                            </div>
                        </div>
                    </a>
                    <?php endwhile; ?>
                    <?php 
                        if($bikes->num_rows <= 0){
                            echo "<h4 class='text-center'><b>No Bike Listed Yet.</b></h4>";
                        }
                    ?>
                </div>       
            </div>
            <?php if(isset($_GET['c'])): ?>
            <div class="col-md-3 border-left border-2">
                <h3 class="text-center"><?php echo $title. " Category" ?></h3>
                <hr>
                <div>
                    <?php echo isset($cat_description) ? stripslashes(html_entity_decode($cat_description)) : '' ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>