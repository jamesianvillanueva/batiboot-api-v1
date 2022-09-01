<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDO;

class UserManagementController extends AppBaseController
{
    public function userVerified($email) {
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email = :email order by id desc');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetch();
        
        if(!$result)
        {
            return false;
        }
        return $result;
        
    }

    public function User(Request $request) {
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

        $response = array(
            'user'=>array(
                'id'=> $result->id,
                'company_id'=> $result->company_id,
                'country_id'=> $result->country_id,
                'name'=>  $result->name,
                'userID'=> $result->userID,
                'photo_id' =>  $result->photo_id,
                'face_recognition' => $result->face_recognition,
                'face_data' => $result->face_data,
                'email' => $result -> email,
                'phone' => $result -> phone,
                'is_admin' => $result -> phone,
                'is_hr' => $result -> phone,
                'role_id' => $result -> phone,
                'user_role' => $result -> phone,
                'department_id' => $result -> phone,
                'shift_id' => $result -> phone,
                'designatio_id' => $result -> phone,
                'permissions' => $result -> phone,
                'verification_code' => $result -> phone,
                'manager_id' => $result -> phone,
                'employee_id' => $result -> phone,
                'employee_type' => $result -> phone,
                'grade' => $result -> phone,
                'nationality' => $result -> phone,
                'nid_card_number' => $result -> phone,
                'nid_card_id' => $result -> phone,
                'facebook_link' => $result -> phone,
                'linkedin_link' => $result -> phone,
                'instagram_link' => $result -> phone,
                'passport_number' => $result -> phone,
                'passport_file_id' => $result -> phone,
                'tin ' => $result -> phone,
                'tin_id_front_file' => $result -> phone,
                'tin_id_back_file' => $result -> phone,
                'bank_name' => $result -> phone,
                'bank_account' => $result -> phone,
                'emergency_name' => $result -> phone,
                'emergency_mobile_number'  => $result -> phone,
                'emergency_mobile_relationship' => $result -> phone,
                'email_verify_token' => $result -> phone,
                'is_email_verified' => $result -> phone,
                'email_verified_at' => $result -> phone,
                'phone_verify_token' => $result -> phone,
                'is_phone_verified' => $result -> phone,
                'phone_verified_at' => $result -> phone,
                'password' => $result -> phone,
                'password_hints' => $result -> phone,
                'avatar_id' => $result -> phone,
                'status_id' => $result -> phone,
                'last_login_at' => $result -> phone,
                'last_logout_at' => $result -> phone,
                'last_login_ip' => $result -> phone,
                'device_token' => $result -> phone,
                'login_access' => $result -> phone,
                'address' => $result -> phone,
                'gender' => $result -> phone,
                'birth_date' => $result -> phone,
                'religion'=> $result -> phone,
                'blood_group' => $result -> phone,
                'joining_date' => $result -> phone,
                'basic_salary' => $result -> phone,
                'marital_status' => $result -> phone,
                'social_id' => $result -> phone,
                'social_type' => $result -> phone,
                'remember_token' => $result -> phone,
                'deleted_at' => $result -> phone,
                'created_at' => $result -> phone,
                'updated_at' => $result -> phone,
                'stripe_id' => $result -> phone,
                'pm_type' => $result -> phone,
                'pm_last_four' => $result -> phone,
                'trial_ends_at' => $result -> phone,
                'lang' => $result -> phone,
            )
        );
          return response((array)$response, 200)
        ->header('Content-Type','application/json');
    }

    public function UserData($email) {
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email != :email order by id desc');
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetchAll();
        
        if(!$result)
        {
            return false;
        }
        return $result;
    }   
    
}
