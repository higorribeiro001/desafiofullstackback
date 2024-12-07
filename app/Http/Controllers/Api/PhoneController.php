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
     *
     *  @OA\GET(
     *      path="/api/phone",
     *      summary="Get all phones",
     *      description="Get all phones",
     *      tags={"Phones"},
     *      @OA\Parameter(
     *        name="page",
     *        in="query",
     *        description="number of page",
     *        required=true,
     *        @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *
     *  )
    */
    public function index()
    {
        return response()->json($this->phone->with('user:id,name')->paginate(10), 200);
    }

    /**
    *  @OA\POST(
    *      path="/api/phone",
    *      summary="Create a new phone",
    *      description="Create a new phone by providing the necessary information",
    *      tags={"Phones"},
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\JsonContent(
    *              type="object",
    *              required={"user_id", "num"},
    *              @OA\Property(property="user_id", type="integer", example=1, description="The user id"),
    *              @OA\Property(property="num", type="string", example="(99) 99999-9999", description="The number of phone"),
    *          )
    *      ),
    *      @OA\Response(
    *          response=201,
    *          description="Phone created successfully",
    *          @OA\MediaType(
    *              mediaType="application/json",
    *              @OA\Schema(
    *                  type="object",
    *                  @OA\Property(property="user_id", type="integer", example=1),
    *                  @OA\Property(property="num", type="string", example="(99) 99999-9999"),
    *              )
    *          )
    *      ),
    *  )
    */
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
     *
     *  @OA\GET(
     *      path="/api/phone/{id}",
     *      summary="Get phone",
     *      description="Get phone",
     *      tags={"Phones"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="phone id",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *
     *  )
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
    /**
    *  @OA\PATCH(
    *      path="/api/phone/{id}",
    *      summary="Update a new phone",
    *      description="Update a new phone by providing the necessary information",
    *      tags={"Phones"},
    *      @OA\Parameter(
    *          name="id",
    *          in="path",
    *          required=true,
    *          description="phone id",
    *          @OA\Schema(type="integer")
    *      ),
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="user_id", type="integer", example=1, description="The user id"),
    *              @OA\Property(property="num", type="string", example="(99) 99999-9999", description="The number of phone"),
    *          )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Phone updated successfully",
    *          @OA\MediaType(
    *              mediaType="application/json",
    *              @OA\Schema(
    *                  type="object",
    *                  @OA\Property(property="user_id", type="integer", example=1),
    *                  @OA\Property(property="num", type="string", example="(99) 99999-9999"),
    *              )
    *          )
    *      ),
    *  )
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
    /**
     *  @OA\DELETE(
     *      path="/api/phone/{id}",
     *      summary="Delete phone",
     *      description="Delete phone",
     *      tags={"Phones"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="phone id",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Phone removed successfully.",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *
     *  )
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
