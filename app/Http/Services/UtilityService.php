<?php
namespace App\Http\Services;
use App\Models\Guardian;
use App\Models\SchoolStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use DB;
class UtilityService
{
    public function getAllRoles()
    {

        $roles = Role::where("guard_name", "api")->get();
        return response()->json(['data' => $roles]);
    }
    public function getParents(Request $request)
    {
        $parents = Guardian::query()
        ->with(['students.StudentSchoolClassStream.SchoolClassStream.SchoolClass','otherContacts'])
    ->join("users", "users.id", "=", "guardians.user_id")
    ->select(
        'users.*',
        'guardians.*',
        DB::raw("CONCAT(users.first_name, ' ', users.middle_name, ' ', users.last_name) AS full_name")
    )
    ->paginate($request->perPage ?? 10);

        return response()->json([
            'status' => 'success',
            'message' => 'Teachers fetched successfully.',
            'parents' => $parents
        ]);
    }
    public function getStaff(Request $request)
    {
        $staff = SchoolStaff::query();
        $staff->join("users","users.id","=","school_staff.user_id");
        $staff->select(
        'users.*',
        'school_staff.*',
        DB::raw("CONCAT(users.first_name, ' ', users.middle_name, ' ', users.last_name) AS full_name")
        );
        $staff = $staff ->paginate($request->perPage ?? 10);
        return response()->json([
            'status' => 'success',
            'message' => 'Teachers fetched successfully.',
            'staff' => $staff
        ]);
    }
}