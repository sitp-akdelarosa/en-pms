<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\PpcDropdownName;
use App\PpcDropdownItem;
use App\AdminModuleAccess;
use App\AdminUserType;
use App\AdminTransactionNo;
use App\PpcDivision;
use App\AdminSettingIso;
use App\PpcOperator;
use App\AdminModule;
use App\ProcessStatus;
use App\ItemClassification;
use DB;

class HelpersController extends Controller
{
    public function moduleID($code)
    {
        $query = AdminModule::select('id')->where('code',$code)->first();

        if (!is_null($query)) {
            return $query->id;
        }

        return '';
    }

    public function UserAccess()
    {
        $user_category = [];

        if (Auth::user()->user_category == 'ALL') {
            $user_category = ['PRODUCTION','OFFICE','ALL']; //Administrator
        } else {
            $user_category = [Auth::user()->user_category,'ALL']; //Administrator
        }

        $user_accesses = AdminModuleAccess::select('code','title','category','user_category')
                            ->where('user_id',Auth::user()->id)
                            ->whereIn('user_category',$user_category)
                            ->get();
        return $user_accesses;
    }

    public static function setActive($path)
    {
        return Request::is($path . '*') ? ' active' :  '';
    }

    public function uploadProfilePhoto($id,$photo)
    {
        if (isset($photo)) {
            $dbPath = 'images/user_photo/';
            $destinationPath = public_path($dbPath);
            $fileName = 'img_'.$id.'.'.$photo->getClientOriginalExtension();

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            if (File::exists($destinationPath.'/'.$fileName)) {
                File::delete($destinationPath.'/'.$fileName);
            }

            $photo->move($destinationPath, $fileName);

            $user = User::find($id);
            $user->photo = $dbPath.$fileName;
            $user->update();
        } else {
            // $user = User::find($id);
            // $user->photo = 'images/default-profile.png';
        }
    }

    public function getDropdownItemByID(Request $req)
    {
        $items = PpcDropdownItem::selectRaw('dropdown_item')
                                ->where('dropdown_name_id',$req->id)
                                ->orderby('dropdown_item','ASC')
                                ->groupBy('dropdown_item')
                                ->get();

        return response()->json($items);
    }

    public function getDropdownItemByName(Request $req)
    {
        $items = PpcDropdownItem::where('dropdown_name',$req->name)->get();
        return response()->json($items);
    }

    public function getUserType()
    {
        $type = AdminUserType::all();
        return response()->json($type);
    }

    public function getLeader()
    {
        $leader = User::select(DB::raw("CONCAT(firstname,' ',lastname) as name"))->get();
        return response()->json($leader);
    }

    public function getDivisionCode()
    {
        $div_code = PpcDivision::select('id', 'div_code', 'plant')
                                ->get();
        return response()->json($div_code);
    }

    public function NextTransactionNo($id)
    {
        $result = '';
        $new_code = 'ERROR';

        try
        {
            $result = AdminTransactionNo::select(
                                            DB::raw("CONCAT(prefix, LPAD(IFNULL(nextno, 0), nextnolength, '0')) AS new_code"),
                                            'nextno',
                                            'month'
                                        )
                                        ->where('id', '=', $id)
                                        ->first();

            if(count((array)$result) <= 0)
            {
                $result->new_code = 'ERROR';
                $result->nextno = 0;
            }

            if ($result->month == date('m')) {
                AdminTransactionNo::where('id', '=', $id) ->update(['nextno' => $result->nextno + 1]);
            } else {
                AdminTransactionNo::where('id', '=', $id)->update(['nextno' => 1, 'month' => date('m')]);

                $result = AdminTransactionNo::select(
                                                DB::raw("CONCAT(prefix, LPAD(IFNULL(nextno, 0), nextnolength, '0')) AS new_code"),
                                                'nextno',
                                                'month'
                                            )
                                            ->where('id', '=', $id)
                                            ->first();

                if(count((array)$result) <= 0)
                {
                    $result->new_code = 'ERROR';
                    $result->nextno = 0;
                }
                AdminTransactionNo::where('id', '=', $id)->update(['nextno' => $result->nextno + 1]);
            }


        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        // $trans_code = 

        return $result->new_code;
    }

    public function TransactionNo($module, $warehouse)
    {
        $check = AdminTransactionNo::where('module',$module)->where('warehouse',$warehouse)->count();
        if ($check > 0) {
            $trans = AdminTransactionNo::select('id')
                                    ->where([
                                        ['module','=',$module],
                                        ['warehouse','=',$warehouse]
                                    ])->first();
            $code = $this->NextTransactionNo($trans->id);
            $transno = str_replace('YYMM',date("ym"),$code);

            return $transno;
        } else {
            return 0;
            // $desc = '';
            // if (strpos($transcode, 'MW') !== false) {
            //     $desc = 'Raw Material Withdrawal';
            // }

            // if (strpos($transcode, 'JO') !== false) {
            //     $desc = 'Job Order No.';
            // }
            
            // if (strpos($transcode, 'PW') !== false) {
            //     $desc = 'Product Withdrawal';
            // }

            // AdminTransactionNo::create([
            //     'code' => $transcode,
            //     'description' => $desc,
            //     'prefix' => $transcode.'-YYMM-',
            //     'prefixformat' => $transcode.'-%y%m-',
            //     'nextno' => 1,
            //     'nextnolength' => 4,
            //     'month' => date('m'),
            //     'warehouse' => $warehouse,
            //     'create_user' => Auth::user()->id,
            //     'update_user' => Auth::user()->id,
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s'),
            // ]);

            // $code = $this->NextTransactionNo($transcode);
            // $transno = str_replace('YYMM',date("ym"),$code);

            // return $transno;
        }
    }

    // public function checkTransactionProdLine()
    // {
    //     $prodLine = DB::table('admin_assign_production_lines')
    //                     ->where('user_id', Auth::user()->id)
    //                     ->select('main_prod')
    //                     ->first();
    // }

    public function PrevTransactionNo($code)
    {
        $result = '';
        $new_code = 'ERROR';

        try
        {
            $result = AdminTransactionNo::select(
                                        DB::raw("CONCAT(prefix, LPAD(IFNULL(nextno, 0), nextnolength, '0')) AS new_code"),
                                        'nextno'
                                    )
                                    ->where('code', '=', $code)
                                    ->first();

            if(count((array)$result) <= 0)
            {
                $result->new_code = 'ERROR';
                $result->nextno = 0;
            }

            AdminTransactionNo::where('code', '=', $code)->update(['nextno' => $result->nextno - 1]);
        }
        catch (Exception $e)
        {
            Log::error($e->getMessage());
        }

        return $result->new_code;
    }

    public function convertDate($date,$format)
    {
        $time = strtotime($date);
        $newdate = date($format,$time);
        return $newdate;
    }

    public function check_if_exists($object)
    {
        if (is_array($object)) {
            return count($object);
        }
        return count((array)$object);
    }

    public function check_permission($code)
    {
        if (isset(Auth::user()->id)) {
            $permission = AdminModuleAccess::where('user_id',Auth::user()->id)
                            ->where('code',$code)
                            ->select('access')->first();

            if (count((array)$permission) > 0) {
                return $permission->access;
            }

            return 2;
        }
        
    }

    public function getDivCodeByID($id)
    {
        $div = PpcDivision::find($id);
        return $div->div_code;
    }

    public function currentProcessID($id)
    {
        $proc = DB::table('prod_travel_sheet_processes')
                    ->where('id',$id)
                    ->select('process')
                    ->first();
        return $proc->process;
    }

    public function currentDivCodeID($id)
    {
        $proc = DB::table('prod_travel_sheet_processes')
                    ->where('id',$id)
                    ->select('div_code')
                    ->first();
        return $proc->div_code;
    }

    public function getLeaderByDivCodeID($div_code_id)
    {
        $div = PpcDivision::find($div_code_id);
        return $div->user_id;
    }

    public function getISO($iso_no = '')
    {
        if ($iso_no == '') {
            $iso = AdminSettingIso::all();
        } else {
            $iso = AdminSettingIso::where('iso_code',$iso_no)->first();
        }
        
        return response()->json($iso);
    }
    
    public function getAllOperators(Request $term)
    {
        $res = PpcOperator::selectRaw("CONCAT(firstname,' ',lastname) AS fullname")->get();
        return response()->json($res);
    }

    public function getItemClassification($id = null)
    {
        $classes = ItemClassification::select('id','description');

        if (!is_null($id)) {
            $classes->where('id',$id);
            return $classes->first();
        }

        return $classes->get();
    }

    public function getProcessStatus($id = null)
    {
        $statuses = ProcessStatus::select('id','description');

        if (!is_null($id)) {
            $statuses->where('id',$id);
            return $statuses->first();
        }

        return $statuses->get();
    }
}
