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
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
<?php
$page = sizeof($cut_data)/ 5;
$page = ceil($page); 
$countrows = 0;

for ($x = 1; $x <= $page; $x++) {
	$_date_issued = date("m/d/Y", strtotime($date_issued));
?>
<div class="container text-center">
    <div class="row center">
        <div class="col-xs-3" style="font-size:10px;">
            <h4 class="center" style="font-weight:900">CUTTING SCHEDULE</h4>
        </div>
    </div>
    <br>

    <div class="row" style=" padding-bottom:5px">
        <div class="col-xs-12" style="width:100%;">
            <table class="table table-sm" style="font-size:12px;width:100%;">
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
            <table class="table table-bordered table-sm" style="font-size:12px;width:100%;">
                <thead>
                    <tr>
                        <th class="th" width="15.88%">JO NO.</th>
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
                    <?php $row = 0; ?>


                    <?php $__currentLoopData = array_slice($cut_data,$countrows); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $rm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td height="50px"><?php echo e($rm->jo_no); ?></td>
                            <td><?php echo e($rm->alloy); ?></td>
                            <td><?php echo e($rm->size); ?></td>
                            <td><?php echo e($rm->item); ?></td>
                            <td><?php echo e($rm->class); ?></td>
                            <td><?php echo e($rm->lot_no); ?></td>
                            <td><?php echo e($rm->sc_no); ?></td>
                            <td><?php echo e($rm->jo_qty); ?></td>
                            <td><?php echo e($rm->cut_weight); ?></td>
                            <td><?php echo e($rm->cut_length); ?></td>
                            <td><?php echo e($rm->cut_width); ?></td>
                            <td><?php echo e($rm->material_used); ?></td>
                            <td><?php echo e($rm->material_heat_no); ?></td>
                            <td><?php echo e($rm->supplier_heat_no); ?></td>
                            <td><?php echo e($rm->qty_needed); ?></td>
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
<br />
<?php else: ?>
<br />
<hr />
<br />
<?php endif; ?>
<?php } ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>