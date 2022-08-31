<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDO;

class ForgotPasswordController extends AppBaseController
{
    public function ValidateUser(Request $request) {

        /* if(!$request -> hasHeader('x-api-key')){
            $invalid = array(
                'message' => 'Missing Request Header'
            );
            return response($invalid,400)
            ->header('Content-Type','application/json');
        }
        $signature = $request->header('x-api-key');
        $verifyKeySignature = $this->validateAPIKEY($signature); 
        if(!$verifyKeySignature)
        {
            $invalid = array(
                'message'=>'Invalid Access Key.',           
             );
            return response($invalid, 401)
            ->header('Content-Type','application/json');
        } */

        $input = $request->all();

        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email = :uemail order by id desc');
        $stmt->bindValue(':uemail',$input['email'],PDO::PARAM_STR);
       /*  $stmt->bindValue(':upass',$upass,PDO::PARAM_STR); */
        $stmt->execute();
        $result = $stmt -> fetch();

        if(!$result)
        {
            $invalid = array(
                'message'=>'Invalid Email.',           
             );
            return response($invalid, 404)
            ->header('Content-Type','application/json');
        }
        if ( password_verify($input['password'], $result->password ) )
        {
            return true;
        }
        $invalid = array(
            'message'=>'Invalid Password.',           
        );
        return response($invalid, 404)
        ->header('Content-Type','application/json');;
    }

    public function findUser($email) {
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email = :email order by id desc');
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetch();
        
        if(!$result)
        {
            return false;
        }
        return true;
    }
    
    public function changePassword($email, $password){

        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('update users set password = :password where email = :email order by id desc');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashedPassword ,PDO::PARAM_STR);

        $stmt->execute();
    
        return true;
    }  
    
    public function ForgotPassword(Request $request){
       
        $input = $request->all();
        
        $ifExists = $this->findUser($input['email']);
    
        if(!$ifExists){
            $invalid = array(
                'message'=>'User is not exist.',           
            );
            return response($invalid, 409)
            ->header('Content-Type','application/json');
        }

        $result = $this->changePassword($input['email'], $input['password']);

        if(!$result){
            $invalid = array(
                'message'=>'Password not changed.',           
            );
            return response($invalid, 500)
            ->header('Content-Type','application/json');
        }

        $invalid = array(
            'message'=>'Password changed.',           
        );
        return response($invalid, 200)
        ->header('Content-Type','application/json');
    }

    public function CheckEmailCode(Request $request){
        
        $input = $request->all();

        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where verification_code = :code order by id desc');
        $stmt->bindValue(':code', $input['code'],PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt -> fetch();
        
        if(!$result) {
            $invalid = array(
                'message'=>'Invalid code.',           
             );
            return response($invalid, 404)
            ->header('Content-Type','application/json');
        }

        return response(array('email'=>$result->email), 200)
            ->header('Content-Type','application/json');
    }



}

