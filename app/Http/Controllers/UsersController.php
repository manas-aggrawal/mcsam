<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    
    private $usersModel;

    public function __getUsersModel() {
        if($this->usersModel == null) {
            $this->userModel = new Users();
        }
        return $this->userModel;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws \Exception
     * @return Object $user
     */
    private function validateUser()
    {
        $token = str_replace('Bearer ', '', \request()->header('Authorization'));
        $user = $this->__getUsersModel()->newQuery()->where('api_token', $token)->first();
        if (!$user) {
            return false;
        }
        return $user;
    }

    public function registerUser(Request $request) {

        $user = $this->__getUsersModel()->registerUser($request);

        if(!empty($user)){
            return response()->json([
                'status'    =>  200,
                'message'   =>  'User registered successfully!',
                'data'      =>  $user,
            ]);
        }
        else {
            return response()->json([
                'status'    =>  400,
                'message'   =>  'User not registered!',
            ]);
        }
        
    }

    public function loginUser(Request $request){
        $user = $this->__getUsersModel()->loginUser($request);
        if(!empty($user)){
            return response()->json([
                'status'    =>  200,
                'message'   =>  'User login success!',
                'data'      =>   $user[0],
            ]);
        }else{
            return response()->json([
                'status'    =>  400,
                'message'   =>  'User login failed!',
            ]);
        }
    }

    /**
     * Method to logout fb user
     * @return Response
     * @throws \Throwable
     */
    public function logout()
    {
        $user_info = $this->validateUser();
        if ($user_info == true) {
            try {

                $user = Users::query()->where('email', $user_info->email)->update([
                    'api_token' => null,
                ]);

                return response()->json([
                    "status" => 200,
                    "message" => "User logged out!",
                ]);
            } catch (\Throwable$th) {
                throw $th;
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorised',
            ]);
        }
    }
}
