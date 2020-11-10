<?php

namespace App\Http\Controllers\PPC\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcMaterialAssembly;
use App\PpcMaterialCode;
use App\PpcDropdownItem;
use App\PpcUpdateInventory;
use App\NotRegisteredMaterial;
use DataTables;
use DB;
use Excel;


class MaterialMasterController extends Controller
{
	protected $_helper;
	protected $_audit;
	protected $_moduleID;

	public function __construct()
	{
		$this->_helper = new HelpersController;
		$this->_audit = new AuditTrailController;

		$this->_moduleID = $this->_helper->moduleID('M0004');
	}

	public function index()
	{
		$user_accesses = $this->_helper->UserAccess();
		return view('ppc.masters.material-master',['user_accesses' => $user_accesses]);
	}

	public function material_assembly_list()
	{   
		$assembly = DB::table('ppc_material_assemblies')
						->select([
							DB::raw('id as id'),
							DB::raw('mat_type as mat_type'),
							DB::raw('character_num as character_num'),
							DB::raw('character_code as character_code'),
							DB::raw('description as description'),
							DB::raw('updated_at as updated_at')
						])
						->orderBy('id','desc');

		return DataTables::of($assembly)
						->editColumn('id', function($data) {
							return $data->id;
						})
						->addColumn('action', function($data) {
							return "<button class='btn btn-sm bg-blue btn_edit_assembly' data-id='".$data->id."' data-mat_type='".$data->mat_type."'
										data-character_num='".$data->character_num."' data-character_code='".$data->character_code."'
										data-description='".$data->description."'>
											<i class='fa fa-edit'></i>
									</button>";
						})
						->make(true);
	}

	public function save_material_assembly(Request $req)
	{

		$this->validate($req, [
			'mat_type' => 'required',
			'character_num' => 'required',
			'character_code' => 'required|max:20',
			// 'description' => 'required|max:50',
		]);

		$error = ['errors' => [
				'mat_type' => 'Material Type is already registered with same Character # and Code.',
				'character_num' => 'Character #  is already registered with same Material Type and Character code.',
				'character_code' => 'Character Code Line is already registered with same Character # and Material Type.']
		];

		if (isset($req->assembly_id)) {
			$check = PpcMaterialAssembly::where('mat_type',$req->mat_type)
										->where('character_num',$req->character_num)
										->where('character_code',$req->character_code)
										->where('id','!=',$req->assembly_id)
										->count();
			if ($check > 0) {
					return response()->json($error, 422);
			}

			$code_assembly = PpcMaterialAssembly::find($req->assembly_id);

			$code_assembly->mat_type = $req->mat_type;
			$code_assembly->character_num = $req->character_num;
			$code_assembly->character_code = strtoupper($req->character_code);
			$code_assembly->description = strtoupper($req->description);
			$code_assembly->update_user = Auth::user()->id;

			$code_assembly->update();

			$this->_audit->insert([
				'user_type' => Auth::user()->user_type,
				'module_id' => $this->_moduleID,
				'module' => 'Material Master',
				'action' => 'Edited data ID: '.$req->assembly_id.', 
							Material Type: '.$req->mat_type.', 
							Character Code: '.$req->character_code,
				'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
			]);
		} else {
			$check = PpcMaterialAssembly::where('mat_type',$req->mat_type)
										->where('character_num',$req->character_num)
										->where('character_code',$req->character_code)
										->count();
			if ($check > 0) {
					return response()->json($error, 422);
			}
			$code_assembly = new PpcMaterialAssembly();

			$code_assembly->mat_type = $req->mat_type;
			$code_assembly->character_num = $req->character_num;
			$code_assembly->character_code = strtoupper($req->character_code);
			$code_assembly->description = strtoupper($req->description);
			$code_assembly->create_user = Auth::user()->id;
			$code_assembly->update_user = Auth::user()->id;

			$code_assembly->save();

			$this->_audit->insert([
				'user_type' => Auth::user()->user_type,
				'module_id' => $this->_moduleID,
				'module' => 'Material Master',
				'action' => 'Inserted data Material Type: '.$req->mat_type.', 
							Character Code: '.$req->character_code,
				'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
			]);
		}

		return response()->json($code_assembly);
	}

	public function destroy_material_assembly(Request $req)
	{
		$data = [
			'msg' => "Deleting failed",
			'status' => "warning"
		];

		if (is_array($req->id)) {
			foreach ($req->id as $key => $id) {
				$code_assembly = PpcMaterialAssembly::find($id);
				$code_assembly->delete();

				$data = [
					'msg' => "Data was successfully deleted.",
					'status' => "success"
				];
			}
		} else {
			$code_assembly = PpcMaterialAssembly::find($id);
			$code_assembly->delete();

			$data = [
				'msg' => "Data was successfully deleted.",
				'status' => "success"
			];
		}

		$ids = implode(',', $req->id);

		$this->_audit->insert([
			'user_type' => Auth::user()->user_type,
			'module_id' => $this->_moduleID,
			'module' => 'Material Master',
			'action' => 'Deleted data Material Assembly ID: '.$ids,
			'user' => Auth::user()->id,
			'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
		]);

		return response()->json($data);
	}

	public function show_dropdowns(Request $req)
	{
		$first = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',1)
									->get();

		$second = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',2)
									->get();

		$third = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',3)
									->get();

		$forth = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',4)
									->get();

		$fifth = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',5)
									->get();

		$seventh = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',7)
									->get();

		$eighth = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',8)
									->get();

		$ninth = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',9)
									->get();

		$eleventh = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',11)
									->get();

		$forteenth = PpcMaterialAssembly::select(
                                        DB::raw("ifnull(character_num,'') as character_num"), 
                                        DB::raw("ifnull(character_code,'') as character_code"), 
                                        DB::raw("ifnull(description,'') as description")
                                    )
									->where('mat_type',$req->mat_type)
									->where('character_num',14)
									->get();
		$data = [
			'first' => $first,
			'second' => $second,
			'third' => $third,
			'forth' => $forth,
			'fifth' => $fifth,
			'seventh' => $seventh,
			'eighth' => $eighth,
			'ninth' => $ninth,
			'eleventh' => $eleventh,
			'forteenth' => $forteenth,
		];
		return response()->json($data);
	}

	public function mat_code_list()
	{
		$mat = DB::table('ppc_material_codes as pmc')
						// ->leftjoin('admin_assign_material_types as apl', 'apl.material_type', '=', 'pmc.material_type')
						->leftjoin('users as u','u.id','pmc.create_user')
						->select([
							DB::raw('pmc.id as id'),
							DB::raw('pmc.material_type as material_type'),
							DB::raw('pmc.material_code as material_code'),
							DB::raw('pmc.code_description as code_description'),
							DB::raw('u.nickname as create_user'),
							DB::raw('pmc.item as item'),
							DB::raw('pmc.alloy as alloy'),
							DB::raw('pmc.schedule as schedule'),
							DB::raw('pmc.size as size'),
							DB::raw('pmc.std_weight as std_weight'),
							DB::raw('pmc.updated_at as updated_at'),
							DB::raw('pmc.disabled as disabled')
						])
						// ->where('apl.user_id' ,Auth::user()->id)
						->orderBy('pmc.id','desc');

		return DataTables::of($mat)
						->editColumn('id', function($data) {
							return $data->id;
						})
						->addColumn('action', function($data) {
							return "<button class='btn btn-sm bg-blue btn_edit_material'
										data-id='".$data->id."'
										data-material_type='".$data->material_type."'
										data-material_code='".$data->material_code."'
										data-code_description='".$data->code_description."'
										data-item='".$data->item."'
										data-alloy='".$data->alloy."'
										data-schedule='".$data->schedule."'
										data-size='".$data->size."'
										data-std_weight='".$data->std_weight."'
										data-create_user='".$data->create_user."'
										data-disabled='".$data->disabled."'
										data-updated_at='".$data->updated_at."'>
											<i class='fa fa-edit'></i>
									</button >";
						})
						->make(true);
	}

	public function save_material_code(Request $req)
	{
		if (isset($req->material_id)) {
			$this->validate($req, [
				'material_code' => 'required|min:16|max:16',
				'material-type' => 'required',
				'code_description' => 'required',
				'item' => 'required',
				'alloy' => 'required',
				'size' => 'required',
				'std_weight' => 'required'
			]);

			$exists = PpcMaterialCode::where('material_code',$req->material_code)->where('id','!=', $req->material_id)->first();
			if (is_null($exists)) {

				$mat = PpcMaterialCode::find($req->material_id);
				$mat->material_type = strtoupper($req->material_type);
				$mat->material_code = strtoupper($req->material_code);
				$mat->code_description = strtoupper($req->code_description);
				$mat->item = strtoupper($req->item);
				$mat->alloy = strtoupper($req->alloy);
				$mat->schedule = strtoupper($req->schedule);
				$mat->size = strtoupper($req->size);
				$mat->std_weight = $req->std_weight;
				$mat->update_user = Auth::user()->id;

				$mat->update();

				$check = NotRegisteredMaterial::where('item_code',$req->material_code)
												->count();

				if ($check > 0) {
					NotRegisteredMaterial::where('item_code',$req->material_code)
												->delete();

					PpcUpdateInventory::where('item_code',$req->material_code)
										->update([
											'description' => strtoupper($req->code_description),
											'item' => strtoupper($req->item),
											'alloy' => strtoupper($req->alloy),
											'schedule' => strtoupper($req->schedule),
											'size' => strtoupper($req->size),
											// 'std_weight' => $req->std_weight,
											'update_user' => Auth::user()->id,//strtoupper($req->update_user),
											'updated_at' => date('Y-m-d H:i:S')
										]);
				}

				$this->_audit->insert([
					'user_type' => Auth::user()->user_type,
					'module_id' => $this->_moduleID,
					'module' => 'Material Master',
					'action' => 'Edited data ID: '.$req->material_id.', 
								Material Code: '.$mat->material_code,
					'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
				]);
			}else{
				return response()->json(['msg'=>"Material Code already taken",'status' => 'failed']);
			}

		} else {
			$this->validate($req, [
				'material_code' => 'required|min:16|max:16|unique:ppc_material_codes',
				'material-type' => 'required',
				'code_description' => 'required',
				'item' => 'required',
				'alloy' => 'required',
				'size' => 'required',
				'std_weight' => 'required'
			]);
			$mat = new PpcMaterialCode();

			$mat->material_type =  strtoupper($req->material_type);
			$mat->material_code = strtoupper($req->material_code);
			$mat->code_description = strtoupper($req->code_description);
			$mat->item = strtoupper($req->item);
			$mat->alloy = strtoupper($req->alloy);
			$mat->schedule = strtoupper($req->schedule);
			$mat->size = strtoupper($req->size);
			$mat->std_weight = $req->std_weight;
			$mat->create_user = Auth::user()->id;
			$mat->update_user = Auth::user()->id;

			$mat->save();

			$check = NotRegisteredMaterial::where('item_code',$req->material_code)
												->count();

			if ($check > 0) {
				NotRegisteredMaterial::where('item_code',$req->material_code)
											->delete();

				PpcUpdateInventory::where('item_code',$req->material_code)
									->update([
										'description' => strtoupper($req->code_description),
										'item' => strtoupper($req->item),
										'alloy' => strtoupper($req->alloy),
										'schedule' => strtoupper($req->schedule),
										'size' => strtoupper($req->size),
										// 'std_weight' => $req->std_weight,
										'update_user' => Auth::user()->id,//strtoupper($req->update_user),
										'updated_at' => date('Y-m-d H:i:S')
									]);
			}

			$this->_audit->insert([
				'user_type' => Auth::user()->user_type,
				'module_id' => $this->_moduleID,
				'module' => 'Material Master',
				'action' => 'Inserted data Material Code: '.$req->material_code,
				'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
			]);
		}
		return response()->json(['msg'=>"Data was successfully saved.",'status' => 'success']);
	}

	public function destroy_code(Request $req)
	{
		$data = [
			'msg' => "Deleting failed",
			'status' => "warning"
		];

		if (is_array($req->id)) {
			foreach ($req->id as $key => $id) {
				$material = PpcMaterialCode::find($id);
				$material->delete();

				$data = [
					'msg' => "Data was successfully deleted.",
					'status' => "success"
				];
			}
		} else {
			$material = PpcMaterialCode::find($id);
			$material->delete();

			$data = [
				'msg' => "Data was successfully deleted.",
				'status' => "success"
			];
		}

		$ids = implode(',', $req->id);
		$this->_audit->insert([
			'user_type' => Auth::user()->user_type,
			'module_id' => $this->_moduleID,
			'module' => 'Material Master',
			'action' => 'Deleted data Material Code ID: '.$ids,
			'user' => Auth::user()->id,
			'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
		]);

		return response()->json($data);
	}

	public function get_dropdown_material_type()
	{
		$process = DB::table('ppc_dropdown_items as pdt')
					// ->leftjoin('admin_assign_material_types as apl', 'apl.material_type', '=', 'pdt.dropdown_item')
					->select([
						'pdt.dropdown_item as material_type',
					])
					->where('pdt.dropdown_name_id', 8) // material type
					// ->where('apl.user_id' , Auth::user()->id)
					->groupBy('pdt.dropdown_item')
					->get();
		return response()->json($process);
	}

	public function enableDisabledProducts(Request $req)
	{
		$data = [
            'msg' => 'Disabling / Enabling Product code has failed.',
            'status' => 'failed'
        ];

        $updated = 0;

        if ($req->disabled == 0) {
            // to disabled
            $updated = DB::table('ppc_material_codes')
                            ->where('id',$req->id)
                            ->update([
								'disabled' => 1,
								'updated_at' => date('Y-m-d H:i:s'),
								'update_user' => Auth::user()->id
							]);
        } else {
            // tioenable
            $updated = DB::table('ppc_material_codes')
                            ->where('id',$req->id)
                            ->update([
								'disabled' => 0,
								'updated_at' => date('Y-m-d H:i:s'),
								'update_user' => Auth::user()->id
							]);
        }

        if ($updated) {
            $data = [
                'msg' => 'Disabling / Enabling Material code has successfully done.',
                'status' => 'success'
            ];
        }

        return response()->json($data);
	}

	public function downloadExcelFile(Request $req)
	{
		$data = [];
        $mat_types = explode(',',$req->mat_types);
        $query = DB::table('ppc_material_codes as pmc')
						->leftjoin('users as u','u.id','pmc.create_user')
						->select([
							DB::raw('pmc.id as id'),
							DB::raw('pmc.material_type as material_type'),
							DB::raw('pmc.material_code as material_code'),
							DB::raw('pmc.code_description as code_description'),
							DB::raw('u.nickname as create_user'),
							DB::raw('pmc.item as item'),
							DB::raw('pmc.alloy as alloy'),
							DB::raw('pmc.schedule as schedule'),
							DB::raw('pmc.size as size'),
							DB::raw('pmc.std_weight as std_weight'),
							DB::raw('pmc.updated_at as updated_at'),
							DB::raw('pmc.disabled as disabled')
						])
						->orderBy('pmc.id','desc');

        if (!is_null($req->mat_types)) {
            $query->whereIn('pmc.material_type',$mat_types);
        }

        $data = $query->orderBy('pmc.id','asc')->get();

        $date = date('Ymd');

        Excel::create('MaterialMasters_'.$date, function($excel) use($data)
        {
            $excel->sheet('Materials', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:M2');
                $sheet->cells('A2:M2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"Material Masters");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:M4', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setValue("MATERIAL TYPE");
                    $cell->setBorder('thin','thin','thin','thin');
                });
            
                $sheet->cell('B4', function($cell) {
                    $cell->setValue("MATERIAL CODE");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue("DESCRIPTION");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue("ITEM");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue("ALLOY");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue("SCHEDULE");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('G4', function($cell) {
                    $cell->setValue("SIZE");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setValue("STD. WEIGHT");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setValue("UPDATED BY");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('J4', function($cell) {
                    $cell->setValue("DATE UPDATED");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);

                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->material_type);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->material_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('c'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->code_description);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->alloy);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->schedule);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->size);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->std_weight);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->create_user);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->updated_at);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    $row++;
                }
            });
        })->download('xlsx');
	}

	public function getAllMaterialTypes()
	{
		$data = DB::table('ppc_dropdown_items')
                    ->whereIn('dropdown_name_id',[8]) // material types
                    ->select(
                        DB::raw("dropdown_item as id"),
                        DB::raw("dropdown_item as text")
                    )
                    ->orderby('dropdown_item','ASC')->get();

        return response()->json($data);
	}

}
