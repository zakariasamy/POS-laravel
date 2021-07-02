<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Dashboard\CreateUserRequest;
use App\Http\Requests\Dashboard\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct()
    {
        //make the function create only avalible for user has (user-create) permission handled by
        // laratrust package
        $this->middleware(['permission:users-create'])->only('create');
        $this->middleware(['permission:users-read'])->only('index');
        $this->middleware(['permission:users-update'])->only('edit');
        $this->middleware(['permission:users-delete'])->only('destroy');

    }

    public function index(Request $request)
    {
        $users = User::whereRoleIs('admin')->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');

            });

        })->latest()->paginate(5);

        return view('dashboard.users.index', compact('users'));

    }

    public function create()
    {
        return view('dashboard.users.create');

    }


    public function store(CreateUserRequest $request)
    {

        $request_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);

        if ($request->image) {

            // Save image with width 300 and height relative to the width using intervention Package
            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('assets/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();

        }//end of if

        $user = User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.users.index');

    }


    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));

    }


    public function update(UpdateUserRequest $request, User $user)
    {

        $request_data = $request->except(['permissions', 'image']);

        if ($request->image) {

            if ($user->image != 'default.png') {

            $image = Str::after($user->image, 'user_images/');
            Storage::disk('user_images')->delete('/' . $image);

            }

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('assets/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();

        }

        $user->update($request_data);

        $user->syncPermissions($request->permissions);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.users.index');

    }

    public function destroy(User $user)
    {
        if ($user->image != 'default.png') {
            $image = Str::after($user->image, 'user_images/');
            Storage::disk('user_images')->delete('/' . $image);

        }

        // the package handles that when we delete the user it deletes its permissions
        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');

    }

}
