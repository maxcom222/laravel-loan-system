<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\Repair;
use App\Models\Ticket;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cartalyst\Sentinel\Roles\RoleInterface;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\eResponse
     */
    public function index()
    {
        if (!Sentinel::hasAccess('users')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = User::with('roles')->get();
        return view('user.data', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('users.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $roles = DB::table('roles')->get();
        $role = array();
        foreach ($roles as $key) {
            $role[$key->name] = $key->name;
        }
        return view('user.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('users.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $rules = array(
            'email' => 'required|unique:users',
            'password' => 'required',
            'rpassword' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'notes' => $request->notes,
                'gender' => $request->gender,
                'phone' => $request->phone,
            ];
            $user = Sentinel::registerAndActivate($credentials);
            $role = Sentinel::findRoleByName($request->role);
            $role->users()->attach($user->id);
            GeneralHelper::audit_trail("Added user with id:" . $user->id);
            Flash::success("Successfully Saved");
            return redirect('user/data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        if (!Sentinel::hasAccess('users.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $payroll = Payroll::where('user_id', $user->id)->get();
        return view('user.show', compact('user', 'payroll'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user)
    {
        if (!Sentinel::hasAccess('users.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $roles = DB::table('roles')->get();
        $role = array();
        foreach ($roles as $key) {
            $role[$key->name] = $key->name;
        }

        foreach ($user->roles as $sel) {
            $selected = $sel->name;
        }
        return view('user.edit', compact('user', 'role', 'selected'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('users.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $user = Sentinel::findById($id);
        $credentials = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'phone' => $request->phone
        ];
        if (!empty($request->password)) {
            $credentials['password'] = $request->password;
        }
        if ($request->role != $request->previous_role) {

            $role = Sentinel::findRoleByName($request->previous_role);
            $role->users()->detach($user->id);
            $role = Sentinel::findRoleByName($request->role);
            $role->users()->attach($user->id);
        }
        $user = Sentinel::update($user, $credentials);
        GeneralHelper::audit_trail("Updated user with id:" . $user->id);
        Flash::success("Successfully Saved");
        return redirect('user/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('users.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        if ( Sentinel::getUser()->id==$id) {
            Flash::warning("You cannot delete your account");
            return redirect('/');
        }
        $user = Sentinel::findById($id);
        $user->delete();
        GeneralHelper::audit_trail("Deleted user with id:" . $id);
        Flash::success("Successfully Deleted");
        return redirect('user/data');
    }

    public function profile()
    {

        $user = Sentinel::findById(Sentinel::getUser()->id);
        return view('user.profile', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function profileUpdate(Request $request)
    {
        $user = Sentinel::findById(Sentinel::getUser()->id);
        $credentials = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'phone' => $request->phone
        ];
        if (!empty($request->password)) {
            $credentials['password'] = $request->password;
        }
        $user = Sentinel::update($user, $credentials);
        Flash::success("Successfully Saved");
        return redirect('dashboard');
    }

//manage permissions
    public function indexPermission()
    {
        $data = array();
        $permissions = Permission::where('parent_id', 0)->get();
        foreach ($permissions as $permission) {
            array_push($data, $permission);
            $subs = Permission::where('parent_id', $permission->id)->get();
            foreach ($subs as $sub) {
                array_push($data, $sub);
            }
        }
        return view('user.permission.data', compact('data'));
    }

    public function createPermission()
    {
        $parents = Permission::where('parent_id', 0)->get();
        $parent = array();
        $parent['0'] = "None";
        foreach ($parents as $key) {
            $parent[$key->id] = $key->name;
        }

        return view('user.permission.create', compact('parent'));
    }

    public function storePermission(Request $request)
    {
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->parent_id = $request->parent_id;
        $permission->description = $request->description;
        if (!empty($request->slug)) {
            $permission->slug = $request->slug;
        } else {
            $permission->slug = str_slug($request->name, '_');
        }

        $permission->save();
        Flash::success("Successfully Saved");
        return redirect('user/permission/data');
    }

    public function editPermission($permission)
    {
        $parents = Permission::where('parent_id', 0)->get();
        $parent = array();
        $parent['0'] = "None";
        foreach ($parents as $key) {
            $parent[$key->id] = $key->name;
        }
        if ($permission->parent_id == 0) {
            $selected = 0;
        } else {
            $selected = 1;
        }

        return view('user.permission.edit', compact('parent', 'permission', 'selected'));
    }

    public function updatePermission(Request $request, $id)
    {
        $permission = Permission::find($id);
        $permission->name = $request->name;
        $permission->parent_id = $request->parent_id;
        $permission->description = $request->description;
        if (!empty($request->slug)) {
            $permission->slug = $request->slug;
        } else {
            $permission->slug = str_slug($request->name, '_');
        }
        $permission->save();
        Flash::success("Successfully Saved");
        return redirect('user/permission/data');
    }

//manage roles
    public function indexRole()
    {
        if (!Sentinel::hasAccess('users.roles')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = EloquentRole::all();
        return view('user.role.data', compact('data'));
    }

    public function createRole()
    {
        $data = array();
        $permissions = Permission::where('parent_id', 0)->get();
        foreach ($permissions as $permission) {
            array_push($data, $permission);
            $subs = Permission::where('parent_id', $permission->id)->get();
            foreach ($subs as $sub) {
                array_push($data, $sub);
            }
        }
        return view('user.role.create', compact('data'));
    }

    public function storeRole(Request $request)
    {
        $role = new EloquentRole();
        $role->name = $request->name;
        $role->slug = str_slug($request->name, '_');
        $role->save();
        if (!empty($request->permission)) {
            foreach ($request->permission as $key) {
                $role->updatePermission($key, true, true)->save();
            }
        }
        GeneralHelper::audit_trail("Added role with id:" . $role->id);
        Flash::success("Successfully Saved");
        return redirect('user/role/data');
    }

    public function editRole($id)
    {
        $data = array();
        $permissions = Permission::where('parent_id', 0)->get();
        foreach ($permissions as $permission) {
            array_push($data, $permission);
            $subs = Permission::where('parent_id', $permission->id)->get();
            foreach ($subs as $sub) {
                array_push($data, $sub);
            }
        }
        $role = EloquentRole::find($id);
        return view('user.role.edit', compact('data', 'role'));
    }

    public function updateRole(Request $request, $id)
    {
        //return print_r($request->permission);
        $role = Sentinel::findRoleById($id);
        $role->name = $request->name;
        $role->slug = str_slug($request->name, '_');
        $role->permissions = array();
        $role->save();
        //remove permissions which have not been ticked
        //create and/or update permissions
        if (!empty($request->permission)) {
            foreach ($request->permission as $key) {
                $role->updatePermission($key, true, true)->save();
            }
        }

        GeneralHelper::audit_trail("Updated role with id:" . $id);
        Flash::success("Successfully Saved");
        return redirect('user/role/data');
    }

    public function deletePermission($id)
    {
        Permission::destroy($id);
        Flash::success("Successfully Saved");
        return redirect('user/permission/data');
    }

    public function deleteRole($id)
    {
        EloquentRole::destroy($id);
        GeneralHelper::audit_trail("Deleted role with id:" . $id);
        Flash::success("Successfully Saved");
        return redirect('user/role/data');
    }
}
