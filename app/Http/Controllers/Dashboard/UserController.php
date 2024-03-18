<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\UploadTrait;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use UploadTrait;

    // all users
    public function index(){
        $users = User::all();
        return view('dashboard.users.index',compact('users'));
    }

    // Create users
    public function create(){
        $roles = User::all();
        return view('dashboard.users.create',compact('roles'));
    }

    public function store(UserRequest $request){
        try {
            //save photo
            $file_name=$this ->saveImage($request->photo,'images/dashboard/admins');
            //fetch data
            $admins = $request->all();
            //hash password
            $hashedPassword = Hash::make($admins['password']);
            //store data
            $admins = User::create([
                'name'=> $admins['name'],
                'email'=> $admins['email'],
                'password'=> $hashedPassword ,
                'photo'=>$file_name,
            ]);
            toastr()->success('تم بنجاح');
            return redirect()->route('users.index');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error adding Admin: ' . $e->getMessage())->withInput();
            }
    }

    public function edit($id) {
        $users = User::find($id);
    
        if (!$users) {
            return redirect()->route('users.index')->with(['error' => 'This Admin Is Not Found']);
        }
    
        return view('dashboard.users.edit', compact('users'));
    }

    public function update(Request $request, $id)
    {
    try {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        toastr()->success('تم بنجاح');

        return redirect()->route('users.index');
    } catch (\Exception $e) {
        return redirect()->route('users.index')->with('error', 'Error updating User: ' . $e->getMessage())->withInput();
    }
    }

    public function destroy ($id){
        $users = User::destroy($id);

        if ($users) {
            toastr()->success('تم بنجاح');
            return redirect()->route('users.index');
        } else {
            return redirect()->route('users.index')->with('error', 'Admin not found');
        }
    }
    public function getProfile(){
        $users = User::all();
        return view('dashboard.users.profile',compact('users'));
    }

    public function profileUpdate(UserRequest $request){

        try {
            $users=User::Find(Auth::user()->id);

            if (!$users) {
                return redirect()->route('dashboard.index')->with('error', 'user not found');
            }
            // Check if the request has an image
            if ($request->hasFile('photo')) {

                // Save the new image
                $file_name = $this->saveImage($request->photo, 'images/dashboard/admins');

                // Update the movie with the new image
                $users->update([
                    'photo' => $file_name
                ]);
            }

            // Update other data
            $users->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);
            toastr()->success('تم بنجاح');
            return redirect()->route('dashboard.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating Data: ' . $e->getMessage())->withInput();
        }

    }


}
