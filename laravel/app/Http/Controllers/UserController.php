<?php

namespace App\Http\Controllers;

// Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests;

// Custom
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserData;
use App\Events\UserRegistered;

class UserController extends Controller
{
    // Create a new user
    public function create(UserRequest $request)
    {
        // Create user based on post input
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        // Is this the first user?
        if($user->id == 1)
        {
            $user->role = 'admin';
        }
        else
        {
            // Otherwise assign to applicant role by default
            $user->role = 'applicant';
        }

        $user->save();
        $this->auth->loginUsingID($user->id);

        // Send notification emails
        event(new UserRegistered($user));

        $request->session()->flash('success', 'Your account has been registered, you are now logged in.');
        return redirect('/users/profile');
    }

    // Handle a user logging in
    public function login(UserRequest $request)
    {
        $credentials = array
        (
            'name' => $request->get('name'),
            'password' => $request->get('password')
        );

        if($this->auth->attempt($credentials))
        {
            $request->session()->flash('success', 'You are now logged in!');
        }

       return redirect('/');
    }

    // Log a user out
    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->flash('success', 'You are now logged out!');
        return redirect('/');
    }

    public function listUsers()
    {
        if($this->auth->check())
        {
            if(in_array($this->auth->user()->role, ['admin', 'judge', 'observer']))
            {
                $users = User::get();
                return view('pages/users/list', compact('users'));
            }
        }
    return redirect('');
    }

    public function viewUser(User $user, Request $request)
    {
        return view('pages/users/view', compact('user'));
    }

    public function editUser(User $user, Request $request)
    {
        return view('pages/users/edit', compact('user'));
    }

    public function updateUser(User $user, UserRequest $request)
    {
        $input = $request->all();

        // Remove empty inputs
        $input = array_filter($input);

        if($input['type'] == 'user')
        {
            $user->update($input);
        }
        else if($input['type'] == 'data')
        {
            // Remove empty inputs
            $input = array_filter($input);

            // Create new row in user data if none exists
            if(is_null($user->data))
            {
                $data = new UserData();
                $data->user_id = $user->id;
                $data->save();

                $data->update($input);
            }
            else
            {
                $user->data->update($input);
            }
        }

        $request->session()->flash('success', 'The user was updated.');
        return redirect('/');
    }

    public function editSelf()
    {
        $user = Auth::user();
        return view('pages/users/profile', compact('user'));
    }

    public function updateSelf(UserRequest $request)
    {
        $user = Auth::user();
        $input = $request->all();

        if($input['type'] == 'data')
        {
            // Remove empty inputs
            $input = array_filter($input);

            // Create new row in user data if none exists
            if(is_null($user->data))
            {

                $data = new UserData();
                $data->user_id = $user->id;
                $data->save();
                $data->update($input);
            }
            else
            {
                $user->data->update($input);
            }
        }

        $request->session()->flash('success', 'Your profile was updated.');
        return redirect('/');
    }
}
