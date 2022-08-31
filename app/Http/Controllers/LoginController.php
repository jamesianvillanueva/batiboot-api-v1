<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDO;

class LoginController extends AppBaseController
{
    public function validateUser($uemail, $upass) {
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email = :uemail order by id desc');
        $stmt->bindValue(':uemail',$uemail,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetch();

        if(!$result)
        {
            return false;
        }
        if ( password_verify($upass, $result->password ) )
        {
            return $result;
        }
    }
    public function Login(Request $request){
        $input = $request->all();
        $user = $this->validateUser($input['uemail'], $input['upass']);

        if(!$user)
        {
             $invalid = array(
                'message'=>'Invalid Username or Password',           
             );
             return response($invalid,400)
             ->header('Content-Type','application/json');
        };
        $access_token = $this->createWebToken($user->userID, $user->email);
        
        switch($user -> user_role) {
            case 0 : $user -> user_role = 'user';
                break;
            case 1 : $user -> user_role = 'admin';
                break;
            
            default:
                return $user -> user_role = 'user';
        }
        $response = array(
            'accessToken'=>$access_token,
            'user'=>array(
                'userID'=> $user->userID,
                'id'=> $user->id,
                'email'=> $user->email,
                'name'=>  $user->name,
                'user_role'=> $user->user_role,
                'photoURL' => $user->photo_id,
            )
        );

        return response((array)$response,200)
        ->header('Content-Type','application/json');
    }
}
