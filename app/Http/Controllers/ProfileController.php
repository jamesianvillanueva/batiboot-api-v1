<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDO;

class ProfileController extends AppBaseController
{
    public function Profile(Request $request){

        $basic = $request->header('Authorization');
        $basic_split = explode(' ',$basic);

        $user = $this->getPayload($basic_split[1]);
       
        $userResponse = $this->userVerified($user['u']);
        

        switch($userResponse -> user_role) {
            case 0 : $userResponse -> user_role = 'user';
                break;
            case 1 : $userResponse -> user_role = 'admin';
                break;
            
            default:
                return $userResponse -> user_role = 'user';
        }
        $response = array(
            'user'=>array(
                'userID'=> $userResponse->userID,
                'id'=> $userResponse->id,
                'email'=> $userResponse->email,
                'name'=>  $userResponse->name,
                'user_role'=> $userResponse->user_role,
                'phone' =>  $userResponse->phone,
                'address' => $userResponse->address,
                'photoURL' => $userResponse->photo_id,
            )
        );
       
        return response((array)$response, 200)
        ->header('Content-Type','application/json');
    }
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


    # PROFILE UPDATE

    public function UpdateProfile(Request $request){

        $basic = $request->header('Authorization');
        $basic_split = explode(' ',$basic);
        $user = $this->getPayload($basic_split[1]);
        $input = $request->all();        
        $result =  $this->Update($input, $user);
    }

    public function Update($input, $user){

        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('update users set name = :name, email = :email, phone = :phone, photo_id = :photo_id, address = :address where email = :old_email');
        $stmt->bindValue(':name', $input['name'] ,PDO::PARAM_STR);
        $stmt->bindValue(':email', $input['email'] ,PDO::PARAM_STR);
        $stmt->bindValue(':phone', $input['phone'] ,PDO::PARAM_STR);
        $stmt->bindValue(':photo_id', $input['photoURL'] ,PDO::PARAM_LOB);
        $stmt->bindValue(':address', $input['address'] ,PDO::PARAM_STR);
        $stmt->bindValue(':old_email', $user['u'] ,PDO::PARAM_STR);
        
        $stmt->execute();

        return true;
    }

}
