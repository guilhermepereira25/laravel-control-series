<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * @param \App\Repositories\UserRepository
     */
    public function __construct(protected UserRepository $users)
    {

    }

    public function index()
    {
        return view('authenticates.register');
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $validator = $this->validator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors('Por favor, preencha os campos corretamente');
        }

        $this->users->create($data);

        return to_route('login');
    }

    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
    }
}
