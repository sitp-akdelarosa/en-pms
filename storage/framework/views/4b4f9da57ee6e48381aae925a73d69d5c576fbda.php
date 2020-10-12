<?php $__env->startPush('css'); ?>
<style type="text/css">
.left {
    text-align: left;
}

.right {
    text-align: right;
}

.center {
    text-align: center;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
<?php
$page = sizeof($raw_materials)/ 5;
$page = ceil($page); 
$countrows = 0;

for ($x = 1; $x <= $page; $x++) {
	$_date_issued = date("m/d/Y", strtotime($date_issued));
?>
<div class="container text-center">
    <div class="row center">
        <div class="col-xs-3" style="font-size:10px;">
            <h5 class="center">CUTTING SCHEDULE</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-sm" style="font-size:10px;width:100%;">
                <tr>
                    <td width="5%">Date:</td>
                    <td width="15%" style="border-bottom: 1px solid"><?php echo e($_date_issued); ?></td>
                    <td width="60%"></td>
                    <td width="5%">Machine:</td>
                    <td width="15%" style="border-bottom: 1px solid"><?php echo e($machine_no); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-bordered table-sm" style="font-size:10px;width:100%;">
                <thead>
                    <tr>
                        <th width="12.5%">JO NO.</th>
                        <th width="5.5%">ALLOY</th>
                        <th width="5.5%">SIZE</th>
                        <th width="10.5%">ITEM</th>
                        <th width="8.5%">CLASS</th>
                        <th width="18.5%">PRODUCTION ORDER NO.</th>
                        <th width="2.5%">ORDER QTY.</th>
                        <th width="2.5%">QTY. NEEDED</th>
                        <th width="4.5%">QTY. CUT</th>
                        <th width="22%" colspan="2">MATERIAL DESCRIPTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $cnt = 1; ?>

                    <?php $__currentLoopData = array_slice($raw_materials,$countrows); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $rm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
								
						$mat_heat_no = $rm->material_heat_no;
						$sup_heat_no = " / ".$rm->supplier_heat_no;
						$needed_qty="";
						$plate_qty="";
						$dim="";

						$schedule = $rm->schedule;
						$size = $rm->size;
						$schedOrSize = "";

						if (!isset($rm->material_heat_no)) {
							$mat_heat_no ="";
						}
						if (!isset($rm->supplier_heat_no) || trim($rm->supplier_heat_no,"") == "undefined") {
							$sup_heat_no ="";
						}
						
						if (isset($rm->cut_weight)) {
							$cut = " x ".$rm->cut_weight;
							if ($rm->cut_weight == "0.00N/A" || $rm->cut_weight == "0.00KG"){
								$cut = " x ".$rm->cut_length;
							}
						}else{
							$cut = " x ".$rm->cut_length;	
						}
						if (trim($schedule) != "" && trim($size) == "") {
							$schedOrSize = $schedule;
						}else if(trim($schedule) == "" && trim($size) != ""){
							$schedOrSize = $size;
						}else if(trim($schedule) != "" && trim($size) != ""){
							$schedOrSize = $size." x ".$schedule ;
						}
						$mat_description_upper="";
						$mat_description_lower="";
						if ($rm->qty_needed_inbox) {
							if (isset($rm->plate_qty)) {
								$plate_qty = "<td rowspan='1'>".$rm->plate_qty."</td>";
								$mat_description_lower="<td rowspan='1'>". $rm->needed_qty ."</td>";
							}else {
								$needed_qty = "<td rowspan='2'>".$rm->needed_qty."</td>";
							}
							$mat_description_upper .= "<td style='border-right: none;'>". $rm->item ."</td>";
							$mat_description_upper .= "<td style='border-left: none;'>".$schedOrSize.$cut."</td>";
						} else {
							$plate_qty="";
							$needed_qty = "<td rowspan='2'>".$rm->needed_qty."</td>";
							$mat_description_upper = "<td colspan='2'>".$schedOrSize.$cut."</td>";
							$mat_description_lower="";
						}
					?>
                    <tr>
                        <td rowspan="2"><?php echo e($rm->jo_no); ?></td>
                        <td rowspan="2"><?php echo e($rm->p_alloy); ?></td>
                        <td rowspan="2"><?php echo e($rm->p_size); ?></td>
                        <td rowspan="2"><?php echo e($rm->p_item); ?></td>
                        <td rowspan="2"><?php echo e($rm->p_class); ?></td>
                        <td rowspan="2"><?php echo e($rm->sc_no); ?></td>
                        <td rowspan="2"><?php echo e($rm->order_qty); ?></td>
                        <?php
							echo $plate_qty;
							echo $needed_qty;
							echo "<td rowspan='2'></td>";
							echo $mat_description_upper ;
						?>
                    </tr>
                    <tr>
                        <?php
							echo $mat_description_lower;
						?>
                        <td width="11%" style="border-right: none;"><?php echo e($mat_heat_no.$sup_heat_no); ?></td>
                        <td width="11%" style="border-left: none;"><?php echo e($rm->lot_no); ?></td>
                    </tr>
                    <?php
						$cnt++;
						if($cnt == 6){
							$countrows += 5;
							break;
						}
					?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php while($cnt <= 5): ?> <tr>
                        <td rowspan="2" height="15"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td colspan="2" height="15"></td>
                        </tr>
                        <tr>
                            <td style="border-right: none;" height="15"></td>
                            <td style="border-left: none;" height="15"></td>
                        </tr>
                        <?php 
								$cnt++;
							?>
                        <?php endwhile; ?>

                </tbody>
                <tfoot>
                    <tr height="80px">
                        <td colspan="5">
                            <p class="left"><strong>Prepared By:</strong></p>
                            <p style="border-bottom: 1px solid"><?php echo e($prepared_by); ?></p>
                            <p><strong>PPC Staff</strong></p>
                        </td>
                        <?php

								if ($withdrawal_slip != '') {
							?>
                        <td colspan="6">
                            <p class="left"><strong>Leader:</strong></p>
                            <p style="border-bottom: 1px solid"><?php echo e($leader); ?></p>
                            <p><?php echo e($withdrawal_slip); ?></p>
                        </td>
                        <?php
								} else {
							?>
                        <td colspan="6">
                            <p class="left"><strong>Leader:</strong></p>
                            <p><?php echo e($leader); ?></p>
                        </td>
                        <?php
								}
							?>

                    </tr>

                    <tr>
                        <td colspan="11" style="text-align: right;">
                            <?php echo e($iso_control_no); ?>

                        </td>
                    </tr>

                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php if($x%2 == 0): ?>
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<?php else: ?>
<br /><br /><br /><br /><br />
<?php endif; ?>
<?php } ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>