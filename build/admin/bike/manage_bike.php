<style>
    #thumbnail-img{
        max-height:35vh
    }
    
    .img-display{
        width:calc(100%);
        height: 20vh;
        object-fit:scale-down;
        object-position:center center
    }
</style>
<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `bike_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Create New " ?> Bike</h3>
	</div>
	<div class="card-body">
		<form action="" id="bike-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="form-group">
				<label for="category_id" class="control-label">Category</label>
                <select name="category_id" id="category_id" class="custom-select select2" required>
                <option value=""></option>
                <?php
                    $qry = $conn->query("SELECT * FROM `categories` where status = 1 ".(isset($category_id) ? "or id = '{$category_id}'" : "")." order by category asc");
                    while($row= $qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['category'] ?></option>
                <?php endwhile; ?>
                </select>
			</div>
            <div class="form-group">
				<label for="brand_id" class="control-label">Brand Category</label>
                <select name="brand_id" id="brand_id" class="custom-select select2" required>
                <option value="" selected="" disabled="">Select Category First</option>
                <?php
                    $qry = $conn->query("SELECT * FROM `brand_list` where status = 1 ".(isset($brand_id) ? "or id = '{$brand_id}'" : "")." order by `name` asc");
                    while($row= $qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($brand_id) && $brand_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php
                    endwhile; 
                ?>
                </select>
			</div>
			<div class="form-group">
				<label for="bike_model" class="control-label">Model</label>
                <textarea name="bike_model" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($bike_model) ? $bike_model : ''; ?></textarea>
			</div>
            <div class="form-group">
                <label for="quantity" class="control-label">Available Unit</label>
                <input type="text" pattern="[0-9]+" name="quantity" id="quantity" class="form-control form no-resize text-right" value="<?php echo isset($quantity) ? $quantity : 0; ?>" required>
            </div>
            <div class="form-group">
                <label for="daily_rate" class="control-label">Daily Rate</label>
                <input type="text" pattern="[0-9]+" name="daily_rate" id="daily_rate" class="form-control form no-resize text-right" value="<?php echo isset($daily_rate) ? $daily_rate : 0; ?>" required>
            </div>
            <div class="form-group">
				<label for="description" class="control-label">Description</label>
                <textarea name="description" id="" cols="30" rows="2" class="form-control form no-resize summernote"><?php echo isset($description) ? $description : ''; ?></textarea>
			</div>
            <div class="form-group">
				<label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="custom-select selevt">
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
			</div>
            <div class="form-group">
				<label for="" class="control-label">Thumbnail</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="thumbnail" accept="image/png,image/jpeg" onchange="displaythmb(this,$(this))">
	              <label class="custom-file-label" for="customFile">Choose file</label>
	            </div>
			</div>
            <div class="form-group text-center mt-2 col-md-8">
                <img src="<?php echo validate_image(isset($id) ? 'uploads/thumbnails/'.$id.'.png':'') ?>" class="bg-dark bg-gradient img-fluid border border-dark" id="thumbnail-img" alt="Thumbnail">
            </div>
            <div class="form-group">
				<label for="" class="control-label">Images</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="images[]" accept="image/png,image/jpeg" onchange="displayImg(this,$(this))" multiple="multiple">
	              <label class="custom-file-label" for="customFile">Choose file</label>
	            </div>
			</div>
            <div class="form-group my-1 row mx-0 row row-cols-4 row cols-sm-1 gx-2 gy-2" id="image-holder"></div>
            <?php 
            if(isset($id)):
            $upload_path = "uploads/".$id;
            if(is_dir(base_app.$upload_path)): 
            ?>
            <?php 
            
                $file= scandir(base_app.$upload_path);
                foreach($file as $img):
                    if(in_array($img,array('.','..')))
                        continue;
                    
                
            ?>
                <div class="d-flex w-100 align-items-center img-item">
                    <span><img src="<?php echo base_url.$upload_path.'/'.$img ?>" width="150px" height="100px" style="object-fit:cover;" class="img-thumbnail" alt=""></span>
                    <span class="ml-4"><button class="btn btn-sm btn-default text-danger rem_img" type="button" data-path="<?php echo base_app.$upload_path.'/'.$img ?>"><i class="fa fa-trash"></i></button></span>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php endif; ?>
			
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="bike-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=bike">Cancel</a>
	</div>
</div>
<script>
    function displaythmb(input,_this) {
        var fnames = []
        Object.keys(input.files).map(k=>{
            fnames.push(input.files[k].name)
        })
        if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#thumbnail-img').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
        _this.siblings('.custom-file-label').html(JSON.stringify(fnames))
	    
	}
    function displayImg(input,_this) {
        var fnames = []
        Object.keys(input.files).map(k=>{
            fnames.push(input.files[k].name)
        })
        _this.siblings('.custom-file-label').html(JSON.stringify(fnames))
        $('#image-holder').html('')
            for(var i = 0; i< input.files.length;i++){
                var reader = new FileReader();
                reader.onload = function (e) {
                    var el = $('<div class="col">')
                        el.append('<img class="lazyload img-display bg-dark bg-gradient" src="'+e.target.result+'">')
                        $('#image-holder').append(el)
                }
        
                reader.readAsDataURL(input.files[i]);
	        }
	    
	}
    function delete_img($path){
        start_loader()
        
        $.ajax({
            url: _base_url_+'classes/Master.php?f=delete_img',
            data:{path:$path},
            method:'POST',
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured while deleting an Image","error");
                end_loader()
            },
            success:function(resp){
                $('.modal').modal('hide')
                if(typeof resp =='object' && resp.status == 'success'){
                    $('[data-path="'+$path+'"]').closest('.img-item').hide('slow',function(){
                        $('[data-path="'+$path+'"]').closest('.img-item').remove()
                    })
                    alert_toast("Image Successfully Deleted","success");
                }else{
                    console.log(resp)
                    alert_toast("An error occured while deleting an Image","error");
                }
                end_loader()
            }
        })
    }
	$(document).ready(function(){
        $('.rem_img').click(function(){
            _conf("Are sure to delete this image permanently?",'delete_img',["'"+$(this).attr('data-path')+"'"])
        })
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
		$('#bike-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_bike",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.href = "./?page=bike";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

        $('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
	})
</script>