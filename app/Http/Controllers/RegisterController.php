<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDO;

class RegisterController extends AppBaseController
{
    public function findUser($email) {
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email = :email order by id desc');
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetch();
        
        if($result)
        {
            return false;
        }
        return true;
    }
    public function registerUser($input){
        /* address column */ 
        $con = $this -> defaultConnection();
       /*  $stmt = $con -> prepare('insert into users (
                name,
                phone, 
                address,
                email,
                marital_status,
                birth_date,
                gender,
                blood_group ,
                religion,
                country_id,
                role_id,
                designation_id,
                department_id,
                shift_id,
                basic_salary,
                password   
            ) 
            values(
                :name, :phone, :address, :email, :marital_status, :birth_date, :gender, 
                :blood_group, :religion, :country_id, :role_id, :designation_id, :department_id, :shift_id, :basic_salary, :password
            )'
        ); */

        $stmt = $con -> prepare('insert into users (
                name,
                email,
                password   
            ) 
            values(
                :name, :email, :password
            )'
        );
        $fullName = $input['firstName']." ".$input['lastName'];
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':name', $fullName, PDO::PARAM_STR);
        $stmt->bindParam(':email',$input['email'], PDO::PARAM_STR);
        $stmt->bindParam(':password',$hashedPassword, PDO::PARAM_STR);
        $inserted = $stmt->execute();
        if(!$inserted){
            $invalid = array(
                'message'=>'Something went wrong',           
            );
            return response($invalid, 500)
            ->header('Content-Type','application/json');
        }
        return $inserted;
    }

    public function Register(Request $request){
        $input = $request->all();        
        $ifExists = $this->findUser($input['email']);     
        if(!$ifExists){
            $invalid = array(
                'message'=>'User already exists.',           
            );
            return response($invalid, 409)
            ->header('Content-Type','application/json');
        }
        $user = $this->registerUser($input);
        $response = array(
            'message'=>'USER Successfully Added',
            'title'=>'USER Added.'
        );
        return response($response, 200)
        ->header('Content-Type','application/json');
    }
}   
