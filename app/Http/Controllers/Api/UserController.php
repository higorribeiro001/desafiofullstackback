<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function __construct(User $user) {
        return $this->user = $user;
    }

    public function index() {
        return response()->json($this->user->select(['id', 'name', 'email', 'company', 'created_at', 'updated_at'])->with('phones:user_id,num')->get(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = $this->user->select(['id', 'name', 'image', 'email', 'company', 'created_at', 'updated_at'])->with('phones:user_id,num')->find($id);

        if (!$user) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        return response()->json($user, 200);
    }

    public function store(Request $request) {
        $user = $this->user;

        $request->validate($user->rules());
        $image = $request->file('image');
        $image_urn = $image->store('images/users', 'public');

        $user->create([
            'name'      => $request->name,
            'image'     => $image_urn,
            'email'     => $request->email,
            'company'   => $request->company,
            'password'  => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Request  $request
     * @param  Integer 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        if ($request->method() === 'PATCH') {
            $rulesDynamics = array();

            foreach ($user->rules() as $input => $rule) {
                if (array_key_exists($input, $request->all())) {
                    $rulesDynamics[$input] = $rule;
                }
            }

            $request->validate($rulesDynamics);
        } else {
            $request->validate($user->rules());
        }

        $user->fill($request->all());

        if($request->file('image')) {
            Storage::disk('public')->delete($user->image);
            $image = $request->file('image');
            $image_urn = $image->store('images/users', 'public');
            $user->image = $image_urn;
        }

        $user->save();

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        Storage::disk('public')->delete($user->image);

        $user->phones()->delete();
        $user->delete();

        return response()->json(['msg' => 'User removed successfully.'], 200);
    }
}
