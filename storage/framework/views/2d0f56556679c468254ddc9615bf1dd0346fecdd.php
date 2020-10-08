<?php $__env->startSection('body'); ?>

	<?php if($print_format == 'material_withdrawal'): ?>
		<div class="container">
			<h5 style="text-align: center;">RAW MATERIAL WITHDRAWAL SLIP</h5>
			<div class="row">
				<div class="col-xs-12 text-center" style="font-size:10px;text-indent: 185px;">
					<div style="float:right;text-indent: 400px;">
						Withdrawal Slip #: <?php echo e($trans_no); ?>

					</div>
					Date: <?php echo e($date); ?>

				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered table-sm" style="font-size:10px;width:100%;">
						<thead>
							<tr>
								<th width="10%" rowspan="2">ALLOY</th>
								<th width="10%" rowspan="2">DESCRIPTION</th>
								<th width="10%" rowspan="2">SIZE/SCHED</th>
								<th colspan="3">QUANTITY</th>
								<th width="10%" rowspan="2">PROD. HEAT NO.</th>
								<th width="10%" rowspan="2">MAT. HEAT NO.</th>
								<th width="20%" rowspan="2">SC NO.</th>
								<th width="15%" rowspan="2">REMARKS</th>
							</tr>
							<tr>
								<th width="5%">ISSUED</th>
								<th width="5%">NEEDED</th>
								<th width="5%">RETURNED</th>
							</tr>
						</thead>
						<tbody>
							<?php $row = 0; ?>
							<?php $__currentLoopData = $raw_materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $rm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

							<?php $row++;
								  $slash =' '; if ($rm->schedule !== '') { $slash = ' / '; } 
							?>
								<tr>
									<td width="10%"><?php echo e($rm->alloy); ?></td>
									<td width="10%"><?php echo e($rm->item); ?></td>
									<td width="10%"><?php echo e($rm->size.$slash.$rm->schedule); ?></td>
									<td width="5%"><?php echo e($rm->issued_qty); ?></td>
									<td width="5%"><?php echo e($rm->needed_qty); ?></td>
									<td width="5%"><?php echo e($rm->returned_qty); ?></td>
									<td width="10%"><?php echo e($rm->lot_no); ?></td>
									<td width="10%"><?php echo e($rm->material_heat_no); ?></td>
									<td width="20%"><?php echo e($rm->sc_no); ?></td>
									<td width="15%"><?php echo e($rm->remarks); ?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php while($row <= 20): ?>
								<tr>
									<td width="10%" height="15"></td>
									<td width="10%"></td>
									<td width="10%"></td>
									<td width="5%"></td>
									<td width="5%"></td>
									<td width="5%"></td>
									<td width="10%"></td>
									<td width="10%"></td>
									<td width="20%"></td>
									<td width="15%"></td>
								</tr>
							<?php $row++; ?>
							<?php endwhile; ?>
						</tbody>
						<tfoot>
							<tr height="80px">
								<td colspan="5">
									<p class="left"><strong>Prepared By:</strong></p>
									<p style="border-bottom: 1px solid" align="center"><?php echo e($prepared_by); ?></p>
									<p align="center"><strong>PPC Staff</strong></p>
								</td>
								<td colspan="5">
									<p class="left"><strong>Received By:</strong></p>
									<p style="border-bottom: 1px solid" align="center"><?php echo e($received_by); ?></p>
									<p align="center"><strong>LINE LEADER/SUPERVISOR</strong></p>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	<?php else: ?>

		<div class="container">
			<h5 style="text-align: center;">RAW MATERIAL WITHDRAWAL SLIP</h5>
			<div class="row">
				<div class="col-xs-12 text-center" style="font-size:10px;text-indent: 185px;">
					<div style="float:right;text-indent: 400px;">
						Withdrawal Slip #: <?php echo e($trans_no); ?>

					</div>
					Date: <?php echo e($date); ?>

				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered table-sm" style="font-size:10px;width:100%;">
						<thead>
							<tr>
								<th width="10%" rowspan="2">ALLOY</th>
								<th width="10%" rowspan="2">DESCRIPTION</th>
								<th width="10%" rowspan="2">SIZE/SCHED</th>
								<th colspan="3">QUANTITY</th>
								<th width="10%" rowspan="2">PROD. HEAT NO.</th>
								<th width="10%" rowspan="2">MAT. HEAT NO.</th>
								<th width="20%" rowspan="2">SC NO.</th>
								<th width="15%" rowspan="2">REMARKS</th>
							</tr>
							<tr>
								<th width="5%">ISSUED</th>
								<th width="5%">NEEDED</th>
								<th width="5%">RETURNED</th>
							</tr>
						</thead>
						<tbody>
							<?php $row = 0; ?>
							<?php $__currentLoopData = $raw_materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $rm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td width="10%"><?php echo e($rm->alloy); ?></td>
									<td width="10%"><?php echo e($rm->item); ?></td>
									<td width="10%"><?php echo e($rm->size); ?></td>
									<td width="5%"></td>
									<td width="5%"><?php echo e('('.$rm->issued_qty.') '.$rm->needed_qty); ?></td>
									<td width="5%"></td>
									<td width="10%"><?php echo e($rm->lot_no); ?></td>
									<td width="10%"><?php echo e($rm->material_heat_no); ?></td>
									<td width="20%"><?php echo e($rm->sc_no); ?></td>
									<td width="15%"><?php echo e($rm->remarks); ?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php while($row <= 20): ?>
								<tr>
									<td width="10%" height="15"></td>
									<td width="10%"></td>
									<td width="10%"></td>
									<td width="5%"></td>
									<td width="5%"></td>
									<td width="5%"></td>
									<td width="10%"></td>
									<td width="10%"></td>
									<td width="20%"></td>
									<td width="15%"></td>
								</tr>
							<?php $row++; ?>
							<?php endwhile; ?>
						</tbody>
						<tfoot>
							<tr height="80px">
								<td colspan="5">
									<p class="left"><strong>Prepared By:</strong></p>
									<p style="border-bottom: 1px solid" align="center"><?php echo e($prepared_by); ?></p>
									<p align="center"><strong>PPC Staff</strong></p>
								</td>
								<td colspan="5">
									<p class="left"><strong>Received By:</strong></p>
									<p style="border-bottom: 1px solid" align="center"><?php echo e($received_by); ?></p>
									<p align="center"><strong>LINE LEADER/SUPERVISOR</strong></p>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	<?php endif; ?>
	
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>