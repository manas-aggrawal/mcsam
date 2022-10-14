<?php

namespace App\Models;

use App\Models\CommonModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Users extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'email', 'password', 'api_token', 'created_at', 'updated_at'];

    protected $hidden = ['password'];

    private $commonModel;

    /**
     * @return CommonModel
     */
    public function __getCommonModel()
    {
        if ($this->commonModel === null) {
            $this->commonModel = new CommonModel();
        }
        return $this->commonModel;
    }

    /**
     * Method to register users in DB
     * @param Request $request
     * @return Array
     */
    public function registerUser($request)
    {

        $array = [];
        $emails = Users::get()->keyBy('email');

        if (!isset($emails[$request->input('email')])) {

            $name = "";
            if (isset($request->name) && !empty($request->input('name'))) {
                $name = $request->input('name');
            }
            $email = "";
            if (isset($request->email) && !empty($request->input('email'))) {
                $email = $request->input('email');
            }
            $password = "";
            if (isset($request->password) && !empty($request->input('password'))) {
                $password = Hash::make($request->input('password'));
            }

            $created_time = now();
            if (isset($request->created_time) && !empty($request->input('created_time'))) {
                $created_time = Carbon::parse($request->input('created_time'));
            }
            $token = $this->__getCommonModel()->createToken();

            array_push($array, [
                "email" => $email,
                "name" => $name,
                "password" => $password,
                'created_at' => $created_time,
                'api_token' => $token,
            ]);

            Users::insert($array);
            $user = Users::select('*')->where('email', $request->input('email'))->get()->toArray();
            return $user;
        } else {
            return [];
        }
    }

    /**
     * Method for user login.
     * @param Request $request
     * @return Array
     */
    public function loginUser($request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $pwd = Users::select('password')->where('email', $email)->get();

        if (!$pwd->isEmpty() && Hash::check($request->input('password'), $pwd[0]->password)) {
            $token = $this->__getCommonModel()->createToken();
            Users::where('email', $email)->update([
                'api_token' => $token,
            ]);

            return Users::select('*')->where('email', $email)->get()->toArray();
        } else {
            return [];
        }
    }

}
