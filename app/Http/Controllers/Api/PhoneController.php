<?php

namespace App\Http\Controllers\Api;

use App\Models\Phone;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function __construct(Phone $phone) {
        $this->phone = $phone;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->phone->with('user:id,name')->get(), 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->phone->rules());

        $phone = $this->phone->create([
            'num' => $request->num,
            'user_id' => $request->user_id
        ]);

        return response()->json($phone, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $phone = $this->phone->with('user:id,name')->find($id);

        if (!$phone) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        return response()->json($phone, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Request   $request
     * @param  Integer 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $phone = $this->phone->find($id);

        if (!$phone) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        if ($request->method() === 'PATCH') {
            $rulesDynamics = array();

            foreach ($phone->rules() as $input => $rule) {
                if (array_key_exists($input, $request->all())) {
                    $rulesDynamics[$input] = $rule;
                }
            }

            $request->validate($rulesDynamics);
        } else {
            $request->validate($phone->rules());
        }

        $phone->fill($request->all());
        $phone->save();

        return response()->json($phone, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $phone = $this->phone->find($id);

        if (!$phone) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        $phone->delete();

        return response()->json(['msg' => 'Phone removed successfully.'], 200);
    }
}
