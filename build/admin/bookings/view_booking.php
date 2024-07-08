<?php 
require_once('../../config.php');
?>
<?php 
if(!isset($_GET['id'])){
    $_settings->set_flashdata('error','No Booking ID Provided.');
    redirect('admin/?page=bookings');
}
$booking = $conn->query("SELECT r.*,concat(c.firstname,' ',c.lastname) as client,c.address,c.email,c.contact from `rent_list` r inner join clients c on c.id = r.client_id where r.id = '{$_GET['id']}' ");
if($booking->num_rows > 0){
    foreach($booking->fetch_assoc() as $k => $v){
        $$k = $v;
    }
}else{
    $_settings->set_flashdata('error','Booking ID provided is Unknown');
    redirect('admin/?page=bookings');
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
<div class="conitaner-fluid px-3 py-2">
    <div class="row">
        <div class="col-md-6">
            <p><b>Client Name:</b> <?php echo $client ?></p>
            <p><b>Client Email:</b> <?php echo $email ?></p>
            <p><b>Client Contact:</b> <?php echo $contact ?></p>
            <p><b>Client Address:</b> <?php echo $address ?></p>
            <p><b>Rent Pick up Date:</b> <?php echo date("M d,Y" ,strtotime($date_start)) ?></p>
            <p><b>Rent Return Date:</b> <?php echo date("M d,Y" ,strtotime($date_end)) ?></p>
        </div>
        <div class="col-md-6">
            <p><b>Bike Category:</b> <?php echo $bike_meta['category'] ?></p>
            <p><b>Bike Brand:</b> <?php echo $bike_meta['brand'] ?></p>
            <p><b>Bike Model:</b> <?php echo $bike_meta['bike_model'] ?></p>
            <p><b>Bike Daily Rate:</b> <?php echo number_format($amount/$rent_days,2) ?></p>
            <p><b>Day/s to Rent:</b> <?php echo $rent_days ?></p>
            <p><b>Client Payable Amount:</b> <?php echo number_format($amount,2) ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-3">Booking Status:</div>
        <div class="col-auto">
        <?php 
            switch($status){
                case '0':
                    echo '<span class="badge badge-light text-dark">Pending</span>';
                break;
                case '1':
                    echo '<span class="badge badge-primary">Confirmed</span>';
                break;
                case '2':
                    echo '<span class="badge badge-danger">Cancelled</span>';
                break;
                case '3':
                    echo '<span class="badge badge-warning">Picked Up</span>';
                break;
                case '4':
                    echo '<span class="badge badge-success">Returned</span>';
                break;
                default:
                    echo '<span class="badge badge-danger">Cancelled</span>';
                break;
            }
        ?>
        </div>
            
    </div>
</div>
<div class="modal-footer">
    <?php if(!isset($_GET['view'])): ?>
    <button type="button" id="update" class="btn btn-sm btn-flat btn-primary">Edit</button>
    <?php endif; ?>
    <button type="button" class="btn btn-secondary btn-sm btn-flat" data-dismiss="modal">Close</button>
</div>
<style>
    #uni_modal>.modal-dialog>.modal-content>.modal-footer{
        display:none;
    }
    #uni_modal .modal-body{
        padding:0;
    }
</style>
<script>
    $(function(){
        $('#update').click(function(){
            uni_modal("Edit Booking Details", "./bookings/manage_booking.php?id=<?php echo $id ?>")
        })
    })
</script>