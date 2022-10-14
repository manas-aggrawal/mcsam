<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\CommonModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Users extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'email', 'password', 'api_token', 'created_at', 'updated_at'];

    protected $hidden = ['password'];

    private $commonModel;

    public function __getCommonModel()
    {
        if ($this->commonModel === null) {
            $this->commonModel = new CommonModel();
        }
        return $this->commonModel;
    }

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
                "password"  =>  $password,
                'created_at' => $created_time,
                'api_token' => $token,
            ]);

            Users::insert($array);
            $user = Users::select('*')->where('email', $request->input('email'))->get()->toArray();
            return $user[0];
        }else {
            return [];
        }
    }

    public function loginUser($request){
        $email = $request->input('email');
        $password = $request->input('password');

        $pwd = Users::select('password')->where('email', $email)->get();

        if(!$pwd->isEmpty() && Hash::check($request->input('password'), $pwd[0]->password)){
            $token = $this->__getCommonModel()->createToken();
            Users::where('email', $email)->update([
                'api_token' => $token,
            ]);

            return Users::select('*')->where('email', $email)->get()->toArray();
        }else{
            return [];
        }
    }

    
}
