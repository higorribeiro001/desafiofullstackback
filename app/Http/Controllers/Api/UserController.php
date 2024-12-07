<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Jobs\MailRegisterJob;

class UserController extends Controller
{
    public function __construct(User $user) {
        return $this->user = $user;
    }

    /**
    *  @OA\GET(
    *      path="/api/user",
    *      summary="Get all users",
    *      description="Get all users",
    *      tags={"Users"},
    *      @OA\Parameter(
    *         name="page",
    *         in="query",
    *         description="number of page",
    *         required=true,
    *         @OA\Schema(type="integer")
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
    public function index() {
        return response()->json($this->user->select(['id', 'name', 'email', 'company', 'created_at', 'updated_at'])->with('phones:user_id,num')->paginate(10), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     *
     *  @OA\GET(
     *      path="/api/user/{id}",
     *      summary="Get user",
     *      description="Get user",
     *      tags={"Users"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
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
    public function show($id) {
        $user = $this->user->select(['id', 'name', 'image', 'email', 'company', 'created_at', 'updated_at'])->with('phones:user_id,num')->find($id);

        if (!$user) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        return response()->json($user, 200);
    }

    /**
    *  @OA\POST(
    *      path="/api/user",
    *      summary="Create a new user",
    *      description="Create a new user by providing the necessary information",
    *      tags={"Users"},
    *      @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"name", "email", "company","password", "image"},
    *                 @OA\Property(
    *                     property="name",
    *                     type="string",
    *                     description="User's name"
    *                 ),
    *                 @OA\Property(
    *                     property="email",
    *                     type="string",
    *                     format="email",
    *                     description="User's email"
    *                 ),
    *                 @OA\Property(
    *                     property="company",
    *                     type="string",
    *                     description="User's company"
    *                 ),
    *                 @OA\Property(
    *                     property="password",
    *                     type="string",
    *                     format="password",
    *                     description="User's password"
    *                 ),
    *                 @OA\Property(
    *                     property="image",
    *                     type="string",
    *                     format="binary",
    *                     description="User profile image (PNG, JPEG, JPG)"
    *                 )
    *             )
    *         )
    *     ),
    *      @OA\Response(
    *          response=201,
    *          description="User created successfully",
    *          @OA\MediaType(
    *              mediaType="application/json",
    *              @OA\Schema(
    *                  type="object",
    *                  @OA\Property(property="id", type="integer", example=1),
    *                  @OA\Property(property="image", type="string", example="images/jsko99asd00dj.jpg"),
    *                  @OA\Property(property="name", type="string", example="John Doe"),
    *                  @OA\Property(property="email", type="string", example="john.doe@example.com"),
    *                  @OA\Property(property="company", type="string", example="Microsoft"),
    *                  @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-07T12:00:00Z"),
    *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-07T12:00:00Z")
    *              )
    *          )
    *      ),
    *  )
    */
    public function store(Request $request) {

        $request->validate($this->user->rules());
        $image = $request->file('image');
        $image_urn = $image->store('images/users', 'public');

        $user = $this->user->create([
            'name'      => $request->name,
            'image'     => $image_urn,
            'email'     => $request->email,
            'company'   => $request->company,
            'password'  => Hash::make($request->password),
        ]);

        try {
            MailRegisterJob::dispatch($user);
        } catch(\Exception $e) {
            return response()->json($e, 500);
        }

        return response()->json($user->makeHidden('password'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Request  $request
     * @param  Integer 
     * @return \Illuminate\Http\Response
     */
    /**
    *  @OA\PATCH(
    *      path="/api/user/{id}",
    *      summary="Update a new user",
    *      description="Update a new user by providing the necessary information",
    *      tags={"Users"},
    *      @OA\Parameter(
    *          name="id",
    *          in="path",
    *          required=true,
    *          description="user id",
    *          @OA\Schema(type="integer")
    *      ),
    *      @OA\RequestBody(
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="name", type="string", example="John Doe", description="The user's name"),
    *              @OA\Property(property="email", type="string", format="email", example="john.doe@example.com", description="The user's email"),
    *              @OA\Property(property="company", type="string", example="Microsoft", description="Company you work for"),
    *          )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="User updated successfully",
    *          @OA\MediaType(
    *              mediaType="application/json",
    *              @OA\Schema(
    *                  type="object",
    *                  @OA\Property(property="id", type="integer", example=1),
    *                  @OA\Property(property="name", type="string", example="John Doe"),
    *                  @OA\Property(property="email", type="string", example="john.doe@example.com"),
    *                  @OA\Property(property="company", type="string", example="Microsoft"),
    *                  @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-07T12:00:00Z")
    *              )
    *          )
    *      ),
    *  )
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

        return response()->json($user->makeHidden('password'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    /**
     *  @OA\DELETE(
     *      path="/api/user/{id}",
     *      summary="Delete user",
     *      description="Delete user",
     *      tags={"Users"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User removed successfully.",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *
     *  )
    */
    public function destroy($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json(['error' => 'Resource searched for does not exist.'], 404);
        }

        if ($user->image !== null) {
            Storage::disk('public')->delete($user->image);
        }

        $user->phones()->delete();
        $user->delete();

        return response()->json(['msg' => 'User removed successfully.'], 200);
    }
}
