<?php $__env->startPush('css'); ?>
	<style>
        .header, .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }
        .header {
            top: 0px;
        }
        .footer {
            bottom: 0px;
        }
        .pagenum:before {
            content: counter(page);
        }
        .fontArial {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }
        thead{display: table-header-group;}
		tfoot {display: table-row-group;}
		tr {page-break-inside: avoid;}
    </style>
    <style type="text/css">
	    .page
	    {
	    	overflow: hidden;
	        page-break-after: always;
	        page-break-inside: avoid;
	    }
	</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
	<?php
		foreach ($data as $key => $pages) {
			foreach ($pages['header'] as $key => $header) {
				if ($pages['id'] == $header->id) {
	?>
					<div class="container page">
						<div class="row">
							<div class="col-xs-2">
								<img src="<?php echo e(asset($header->iso_photo)); ?>" alt="" height="80px" width="200px">
							</div>
							<div class="col-xs-10 text-center">
								<h3 style="margin-left:55px">TRAVEL SHEET</h3>
							</div>
							    
						</div>
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-bordered text-center table-sm" style="font-size:11px;width:100%;">
									<thead>
										<tr>
											<td rowspan="2" colspan="4"><img src="<?php echo e(asset('/barcode.php').'?codetype=CODE39&size=30&text='.$header->jo_no.'&print=true'); ?>" class="img-fluid" alt="<?php echo e($header->jo_no); ?>"></td> 
											<td colspan="2">Type:</td>
											<td colspan="2"><?php echo e($header->type); ?></td>
											<td rowspan="2">Material Heat #:</td>
											<td colspan="2" rowspan="2"><?php echo e($header->material_heat_no); ?></td>
										</tr>
										<tr>
											<td colspan="2">Material Used:</td>
											<td><?php echo e($header->material_used); ?></td>
											<td><?php echo e($header->bar_size.' x '.$header->cut_weight); ?></td>
										</tr>
										<tr>
											<td colspan="4">Production Order Number</td>
											<td colspan="2">Issued Qty:</td>
											<td colspan="2"><?php echo e($header->issued_qty); ?></td>
											
											<td>Part Code:</td>
											<td colspan="2"><?php echo e($header->prod_code); ?></td>
										</tr>
										<tr>
											<td colspan="4"><?php echo e($header->sc_no); ?></td>
											<td colspan="2" rowspan="2">Lot No.</td>
											<td colspan="2" rowspan="2"><?php echo e($header->prod_heat_no); ?></td>
											<td rowspan="2">Description</td>
											<td colspan="2" rowspan="2"><?php echo e($header->description); ?></td>
										</tr>
										<tr>
											<td>Job Order Qty.</td>
											<!-- $header->order_qty -->
											<td colspan="2"><?php echo e($header->sched_qty); ?></td>
											<td>PCS.</td>
										</tr>
										<tr>
											<td rowspan="2">Process</td>
											<td rowspan="2">MC/ No.</td>
											<td rowspan="2">Date Process</td>
											<td rowspan="2">Input Qty.</td>
											<td rowspan="2">Output Qty.</td>
											<td rowspan="2">Qty. NC.</td>
											<td colspan="2">Disposition</td>
											<td rowspan="2">Date Returned</td>
											<td rowspan="2">Operator's Name</td>
											<td rowspan="2">Signature</td>
										</tr>
										<tr>
											<td>A/B/C</td>
											<td>Process Rework</td>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach ($pages['process'] as $key => $proc) {
												if ($pages['id'] == $proc->pre_travel_sheet_id) {
										?>
													<tr>
														<td><?php echo e($proc->process_name); ?></td>
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
												}
											}
										?>
										
									</tbody>
									<tfoot>
										<tr>
											<td>Remarks</td>
											<td colspan="10"></td>
										</tr>
										<tr>
											<td colspan="8">
												<b>Legend:</b> A - for Rework | B - for Scrap | C - Alloy Mix | NC - Non-Conformance
											</td>
											<td colspan="3">
												Date Released: <?php echo e($pages['date']); ?>

											</td>
										</tr>
									</tfoot>
								</table>
								
							</div>
						</div>

						<div class="row">
							<div class="col-xs-1" style="text-align:right">
								<span class="small"><?php echo e($header->iso_code); ?></span>
							</div>
						</div>
					</div>
	<?php
				}
			}
		}
	?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>