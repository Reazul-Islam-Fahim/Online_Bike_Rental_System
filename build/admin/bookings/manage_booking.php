<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `rent_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
if(isset($bike_id)){
    $bike = $conn->query("SELECT b.*,c.category, bb.name as brand from `bike_list` b inner join categories c on b.category_id = c.id inner join brand_list bb on b.brand_id = bb.id where b.id = '{$bike_id}' ");
    if($bike->num_rows > 0){
        foreach($bike->fetch_assoc() as $k => $v){
            $bike_meta[$k]=stripslashes($v);
        }
    }
}
?>
<div class="container-fluid">
    <form action="" id="book-form">
        <p><b>Bike Category:</b> <?php echo $bike_meta['category'] ?></p>
        <p><b>Bike Brand:</b> <?php echo $bike_meta['brand'] ?></p>
        <p><b>Bike Model:</b> <?php echo $bike_meta['bike_model'] ?></p>
        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
        <input type="hidden" name="bike_id" value="<?php echo isset($bike_id) ? $bike_id : ''  ?>">
        <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id : ''  ?>">
        <div class="form-group">
            <label for="date_start" class="control-label">Pick-up Date</label>
            <input type="date" name="date_start" id="date_start" class="form-control form-conrtrol-sm rounded-0" value="<?php echo isset($date_start) ? $date_start : ''  ?>" required>
        </div>
        <div class="form-group">
            <label for="date_end" class="control-label">Date to Return</label>
            <input type="date" name="date_end" id="date_end" class="form-control form-conrtrol-sm rounded-0" value="<?php echo isset($date_end) ? $date_end : ''  ?>" required>
        </div>
        <div id="msg" class="text-danger"></div>
        <div id="check-availability-loader" class="d-none">
            <center>
                <div class="d-flex align-items-center col-md-6">
                    <strong>Checking Availability...</strong>
                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                </div>
            </center>
        </div>
        <div class="form-group">
            <label for="rent_days" class="control-label">Days to Rent</label>
            <input type="number" name="rent_days" id="rent_days" class="form-control form-conrtrol-sm rounded-0 text-right" value="<?php echo isset($rent_days) ? $rent_days : 0  ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="daily_rate" class="control-label">Bike Daily Rate</label>
            <input type="text"  id="daily_rate" class="form-control form-conrtrol-sm rounded-0 text-right" value="<?php echo isset($amount) ? number_format($amount/$rent_days,2) : 0.00 ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="amount" class="control-label">Total Amount</label>
            <input type="number" name="amount" id="amount" class="form-control form-conrtrol-sm rounded-0 text-right" value="<?php echo isset($amount) ? $amount : 0  ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Status</label>
            <select name="status" id="" class="custom-select custol-select-sm">
                <option value="0" <?php echo $status == 0 ? "selected" : '' ?>>Pending</option>
                <option value="1" <?php echo $status == 1 ? "selected" : '' ?>>Confirmed</option>
                <option value="2" <?php echo $status == 2 ? "selected" : '' ?>>Cancelled</option>
                <option value="5" <?php echo $status == 3 ? "selected" : '' ?>>Picked Up</option>
                <option value="3" <?php echo $status == 4 ? "selected" : '' ?>>Returned</option>
            </select>
        </div>
    </form>
</div>

<script>
    function calc_rent_days(){
        var ds = new Date($('#date_start').val())
        var de = new Date($('#date_end').val())
        var diff = de- ds;
        var days = (Math.floor((diff)/(1000*60*60*24))) +1
        $('#rent_days').val(days)
        if(days > 0){
            calc_amount()
        }
    }
    function calc_amount(){
        var dr = "<?php echo isset($daily_rate) ? $daily_rate :'' ?>";
        var days = $('#rent_days').val()
        var amount  = dr * days;
        console.log(amount)
        $('#amount').val(amount)
    }
    $(function(){
        $('#date_start, #date_end').change(function(){
            $('#msg').text('')
            $('#date_start, #date_end').removeClass('border-success border-danger')
            var ds = $('#date_start').val()
            var de = $('#date_end').val()
            var bike_id = "<?php echo isset($bike_id) ? $bike_id :'' ?>";
            var max_unit = "<?php echo isset($bike_meta['quantity']) ? $bike_meta['quantity'] :'' ?>";
            var id = "<?php echo isset($id) ? $id :'' ?>";
            if(ds == '' || de == '' || bike_id == '' || max_unit == '')
            return false;
            if(de < ds){
                $('#date_start, #date_end').addClass('border-danger')
                $('#msg').text("Invalid Selected Dates")
                return false;
            }
            $('#check-availability-loader').removeClass('d-none')
            $('#uni_modal button').attr('disabled',true)
            $.ajax({
                url:'classes/Master.php?f=rent_avail',
                method:"POST",
                data:{ds:ds,de:de,bike_id:bike_id,max_unit:max_unit,id:id},
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast('An error occured while checking availability','error')
                    $('#check-availability-loader').addClass('d-none')
                    $('#uni_modal button').attr('disabled',false)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        $('#date_start, #date_end').addClass('border-success')
                    }else if(resp.status == 'not_available'){
                        $('#date_start, #date_end').addClass('border-danger')
                        $('#msg').text(resp.msg)
                    }else{
                        alert_toast('An error occured while checking availability','error')
                    }
                    $('#check-availability-loader').addClass('d-none')
                    $('#uni_modal button').attr('disabled',false)
                    calc_rent_days()
                }
            })
            
        })
        $('#book-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            if(_this.find('.border-danger').length > 0){
                alert_toast('Can\'t proceed submission due to invalid inputs in some fields.','warning')
                return false;
            }
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_booking",
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
                        location.reload()
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
    })
</script>