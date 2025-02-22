<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use stdClass;

class UserController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:user.view|user.create|user.edit|user.delete|user.approve', ['only' => ['index','store']]);
         $this->middleware('permission:user.view', ['only' => ['show']]);
         $this->middleware('permission:user.create', ['only' => ['create','store']]);
         $this->middleware('permission:user.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user.delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$data = User::latest()->paginate(20);

       // return view('admin.users.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5);

       list( $users, $filters) = $this->userSearch();
       $users = $users->paginate();
       return view('admin.users.index', compact('users', 'filters'));
    }

    public function userSearch()
    {

        $users = User::select('*');
        $filters = collect(
            ['search_by' => '']
        );

        if (request()->get('search_by') != null){
            //dd(request()->get('search_by'));
            $users->whereAny(['name','email','phone'], 'like', '%'.request()->get('search_by').'%');
            $filters->put('search_by', request()->get('search_by'));
        }

        return [
            $users,
            $filters
        ];
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();

        return view('admin.users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect('/admin/users')->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     * 
     *  @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = User::find($id);
        return view('admin.users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
    
        return view('admin.users.edit',compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return redirect('/admin/users')->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        User::find($id)->delete();
        return redirect('/admin/users')->with('success','User deleted successfully');
    }
}
