<?php $__env->startSection('title'); ?>
	Cutting Schedule
<?php $__env->stopSection(); ?>

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
.th {
    text-align: center; 
    vertical-align: middle;
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
            <h3 class="center" style="font-weight:900">CUTTING SCHEDULE</h3>
        </div>
    </div>
    <br>

    <div class="row" style=" padding-bottom:5px">
        <div class="col-xs-12" style="width:100%;">
            <table class="table table-sm" style="font-size:14px;width:100%;">
                <tr>
                    <td width="5%"><strong>Date:</strong></td>
                    <td width="15%" style="border-bottom: 1px solid"><strong><?php echo e($_date_issued); ?></strong></td>
                    <td width="40%"></td>
                    <td width="15%"><strong>Withdrawal Slip:</strong></td>
                    <td width="15%" style="border-bottom: 1px solid"><strong><?php echo e($withdrawal_slip); ?></strong></td>
                </tr>
            </table>
        </div>
    </div>

   

    <div class="row">
        <div class="col-xs-12" style="width:100%;">
            <table class="table table-bordered table-sm" style="font-size:13px;width:100%;">
                <thead>
                    <tr>
                        <th class="th" width="15.88%" height="50px">JO NO.</th>
                        <th class="th" width="5.88%">ALLOY</th>
                        <th class="th" width="5.88%">SIZE</th>
                        <th class="th" width="5.88%">ITEM</th>
                        <th class="th" width="5.88%">CLASS</th>
                        <th class="th" width="5.88%">LOT #</th>
                        <th class="th" width="5.88%">S/C #</th>
                        <th class="th" width="5.88%">J.O. QTY.</th>
                        <th class="th" width="3.88%">CUT WT.</th>
                        <th class="th" width="3.88%">CUT LENGTH</th>
                        <th class="th" width="3.88%">CUT WIDTH</th>
                        <th class="th" width="10.88%">MATERIAL USED</th>
                        <th class="th" width="5.88%">RM HEAT #</th>
                        <th class="th" width="1.88%">SUPPLIER HEAT #</th>
                        <th class="th" width="5.88%">QTY. NEEDED</th>
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
                        <td height="50px"><?php echo e($rm->jo_no); ?></td>
                        <td><?php echo e($rm->p_alloy); ?></td>
                        <td><?php echo e($rm->p_size); ?></td>
                        <td><?php echo e($rm->p_item); ?></td>
                        <td><?php echo e($rm->p_class); ?></td>
                        <td><?php echo e($rm->lot_no); ?></td>
                        <td><?php echo e($rm->sc_no); ?></td>
                        <td><?php echo e($rm->jo_qty); ?></td>
                        <td><?php echo e($rm->cut_weight); ?></td>
                        <td><?php echo e($rm->cut_length); ?></td>
                        <td><?php echo e($rm->cut_width); ?></td>
                        <td><?php echo e($rm->material_used); ?></td>
                        <td><?php echo e($rm->material_heat_no); ?></td>
                        <td><?php echo e($rm->supplier_heat_no); ?></td>
                        <td>
                            <?php if($rm->qty_needed_inbox !== '' && $rm->qty_needed_inbox !== 0 && !isset($rm->qty_needed_inbox)): ?>
                                <?php echo e($rm->needed_qty); ?>

                            <?php else: ?>
                                <?php echo e($rm->needed_qty. '('.$rm->plate_qty.')'); ?>

                            <?php endif; ?>
                            
                        </td>
                    </tr>
                    <?php
						$cnt++;
						if($cnt == 6){
							$countrows += 5;
							break;
						}
					?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php while($cnt <= 5): ?> 
                        <tr>
                            <td height="50px"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php 
								$cnt++;
							?>
                        <?php endwhile; ?>

                </tbody>
                <tfoot>
                    <tr height="80px">
                        <td colspan="8">
                            <p class="left"><strong>Prepared By:</strong></p>
                            <p style="border-bottom: 1px solid; font-weight:700;font-size:16px"><?php echo e($prepared_by); ?></p>
                            <p><strong>PPC STAFF</strong></p>
                        </td>
                        <td colspan="9">
                            <p class="left"><strong>Received By:</strong></p>
                            <p style="border-bottom: 1px solid; font-weight:700;font-size:16px"><?php echo e($leader); ?></p>
                            <p><strong>LEADER</strong></p>
                        </td>

                    </tr>

                    <tr>
                        <td colspan="17" style="text-align: right;">
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