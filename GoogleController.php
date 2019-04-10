<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use DateTime;
use Auth;
use Socialite;
use Session;

//use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    public function GoogleLogIn(){
    	return view('GoogleLogin');
    }
    public function GoogleUser(Request $req){
    	
    	
    		$email=$req->email;
    		$data=User::where('email',$email)->get();
    //dd(count($data));	
    	if(count($data)>0)
    	{
    		//return($req->name);
    		$LogId=User::where('email',$email)->first();
    		Auth::loginUsingId($LogId->id);
    		return("true");
    	
    	}else{
    		$now = new DateTime();
    		$User= new User();
    		$User->name=$req->name;
    		$User->email=$req->email;
    		$User->password=Hash::make($email);
    		$User->created_at=$now;
    		$User->updated_at=$now;
    		$User->save();
    		$LogId=User::where('email',$User->email)->first();
    		Auth::loginUsingId($LogId->id);
    		return('true');
    	}
    	

    }
    
   	public function Google()
    {
        
        return Socialite::driver('google')->redirect();
 //   
    }
    public function handleGoogleCallback()
    {
        try {
        	$req = Socialite::driver('google')->user();
        	//dd($req);
        	if(session()->has('status') && (session('status')=='signup'))
    		{
    			
    			return $this->NewGoogleUser($req);
    			
    			//return("kay ha");
    		}
   		else{
	            //$req = Socialite::driver('google')->user();
	            //dd($req);
	            $email=$req->email;
	    		$data=User::where('email',$email)->first();


	    		//dd($data);


				if(count($data)>0)
		    	{
		    		//return($req->name);
			    		$LogId=User::where('email',$email)->first();
			    		Auth::loginUsingId($LogId->id);
			    		return view('home');
			    		//return("true");
		    	
		    	}else{
		    			Session::flash('message', 'Your Record Not Found In Server !! Please Sign Up');
			    		return redirect()->route('login');
		    	}
		   
		    }

            //dd($user);
            // $userModel = new User;
            // $createdUser = $userModel->addNew($user);
             //Auth::loginUsingId($createdUser->id);
            // return redirect()->route('home');
        } catch (Exception $e) {
            //return redirect('auth/facebook');
        }
    }
    public function GoogleSignUp(){
    	
    		Session(['status'=>'signup']);
    		//8dd(Session()->all());
    		return Socialite::driver('google')->redirect();
	   


    }
    public function NewGoogleUser($pra_req){
    	//return("pk bhai");
    	if(session()->has('status'))
    	{

    	//dd(Session()->all());
    		$req = $pra_req;
    		//dd($req);
    		$email=$req->email;
	    	
	    	$data=User::where('email',$email)->first();
	    	if(count($data)>0)
		    {
		    	//dd(session()->all());
		    	Session(['status'=>'login']);
		    	Session::flash('message', 'Your Record  Found In Server. Please Sign In');
			    return redirect()->route('login');
		    }
	    	
	    	$now = new DateTime();
			$User= new User();
			$User->name=$req->name;
			$User->email=$req->email;
			$User->password=Hash::make(str_random(12));
			$User->created_at=$now;
			$User->updated_at=$now;
			$User->save();
			$LogId=User::where('email',$User->email)->first();
			Auth::loginUsingId($LogId->id);
			//session()->flush('status');
			//Session::forget('status');
			Session()->forget('status');
			Session(['status'=>'login']);
			return view('home');
		}	

    }
    
}
