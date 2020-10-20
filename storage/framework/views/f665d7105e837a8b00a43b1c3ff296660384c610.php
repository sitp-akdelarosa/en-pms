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
		.remarks {
			padding-top: -50px;
		}
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
							<div class="col-xs-6" style="padding-right: 20px">
								<img src="<?php echo e(asset($header->iso_photo)); ?>" alt="" height="80px" width="200px" >
							</div>
							<div class="col-xs-5 text-center" style="width: 500px;">
								<h2 style="padding-top:40px;">TRAVEL SHEET</h2>
							</div>
							<div class="col-xs-1" style="width: 217px;">
								<p style="padding-top:50px; padding-bottom: 0px;font-size: 13px; text-align:right">Date Released: <?php echo e($pages['date']); ?></p>
							</div>
							    
						</div>
						<div class="row">
							<div class="col-xs-12" style="width:100%;">
								<table class="table table-bordered table-sm" style="font-size:13px;width:100%;">
									<thead>
										<tr>
											<td class="text-center" rowspan="2" colspan="3"><img src="<?php echo e(asset('/barcode.php').'?codetype=CODE39&size=30&text='.$header->jo_sequence.'&print=true'); ?>" class="img-fluid" alt="<?php echo e($header->jo_sequence); ?>"></td> 
											<td class="text-center" colspan="2">Type:</td>
											<td class="text-center" colspan="3"><?php echo e($header->type); ?></td>
											<td class="text-center" colspan="2">Material Used:</td>
											<td class="text-center"><?php echo e($header->bar_size); ?></td>
											
										</tr>
										<tr>
											
											<td class="text-center" colspan="2">Part Code:</td>
											<td class="text-center" colspan="3"><?php echo e($header->prod_code); ?></td>

											<td class="text-center" colspan="2">Material Heat #:</td>
											<td class="text-center" ><?php echo e($header->material_heat_no); ?></td>
										</tr>
										<tr>
											<td>Job Order Qty.</td>
											<td class="text-center" colspan="2"><?php echo e($header->sched_qty); ?></td>
											<td class="text-center" colspan="2" rowspan="2">Description</td>  
											<td class="text-center" colspan="3" rowspan="2"><?php echo e($header->description); ?></td>
											
											
											<td class="text-center" colspan="2">Supplier Heat #:</td>
											<td class="text-center"><?php echo e($header->supplier_heat_no); ?></td>
										</tr>
										<tr>
											<td>Product Line</td>
											<td class="text-center" colspan="2"><?php echo e($header->product_line); ?></td>
											<td class="text-center">Cut Wt.</td>
											<td class="text-center">Cut Length</td>
											<td class="text-center">Cut Width</td>
										</tr>
										<tr>
											<td>S/C #</td>
											<td class="text-center" colspan="2"><?php echo e($header->sc_no); ?></td>
											<td class="text-center">Issued Qty.</td>
											<td class="text-center"><?php echo e($header->issued_qty); ?></td>
											<td class="text-center">Lot No.</td>
											<td class="text-center" colspan="2"><?php echo e($header->lot_no); ?></td>

											<td class="text-center"><?php echo e($header->cut_weight); ?></td> 
											<td class="text-center"><?php echo e($header->cut_length); ?></td> 
											<td class="text-center"><?php echo e($header->cut_width); ?></td> 
										</tr>
										<tr>
											<td class="text-center" rowspan="2">Process</td>
											<td class="text-center" rowspan="2">Division No.</td>
											<td class="text-center" rowspan="2">Date Process</td>
											<td class="text-center" rowspan="2">Input Qty.</td>
											<td class="text-center" rowspan="2">Output Qty.</td>
											<td class="text-center" rowspan="2">Qty. NC.</td>
											<td class="text-center" colspan="2">Disposition</td>
											<td class="text-center" rowspan="2">Date Returned</td>
											<td class="text-center" rowspan="2">Operator's Name</td>
											<td class="text-center" rowspan="2">Signature</td>
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
											<td colspan="11">
												<b>Legend:</b> A - for Rework | B - for Scrap | C - Alloy Mix | NC - Non-Conformance
											</td>
											
										</tr>
										<tr>
											<td colspan="11" style="height: 100px">
												<strong>REMARKS</strong>
											</td>
										</tr>
										
									</tfoot>
								</table>
								
							</div>
						</div>

						<div class="row">
							<div class="col-xs-1" style="width:100%;text-align:right">
								<strong class="small">ISO Control No.: <?php echo e($header->iso_code); ?></strong>
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