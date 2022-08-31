<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDO;

class UserManagementController extends AppBaseController
{
    public function userVerified($email) {
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email = :email order by id desc');
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetch();
        
        if(!$result)
        {
            return false;
        }
        return $result;
    }

    public function User() {
        $basic = $request->header('Authorization');
        $basic_split = explode(' ',$basic);
        $user = $this->getPayload($basic_split[1]);       
        $userResponse = $this->userVerified($user['u']);

        if(!$userResponse){
            $invalid = array(
                'message'=>'Invalid Authorization Token',           
            );
            return response($invalid, 500)
            ->header('Content-Type','application/json');
        }   

        $result = $this -> UserData($userResponse-> email);

    }

    public function UserData($email) {


    }   
    
}
