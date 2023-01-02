<?php
namespace App\Http\Controllers\Auth;
ini_set("allow_url_fopen",'1');
use App\User;
use App\Http\Controllers\Controller;
use Socialite;
use Exception;
use Auth;
use Image;
use Session;

class FacebookController extends Controller
{


   public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * List of providers configured in config/services acts as whitelist
     *
     * @var array
     */
    protected $providers = [
        'github',
        'facebook',
        'google',
        'twitter'
    ];

    /**
     * Show the social login page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('auth.social');
    }

    /**
     * Redirect to provider for authentication
     *
     * @param $driver
     * @return mixed
     */
    public function redirectToProvider($driver)
    {
        if( ! $this->isProviderAllowed($driver) ) {
            return $this->sendFailedResponse("{$driver} is not currently supported");
        }

        try {
            return Socialite::driver($driver)->redirect();
        } catch (Exception $e) {
            // You should show something simple fail message
            return $this->sendFailedResponse($e->getMessage());
        }
    }

    /**
     * Handle response of authentication redirect callback
     *
     * @param $driver
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback( $driver )
    {
        try {
            $user = Socialite::driver($driver)->user();
        } catch (Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }

        // check for email in returned user
        return empty( $user->email )
            ? $this->sendFailedResponse("No email id returned from {$driver} provider.")
            : $this->loginOrCreateAccount($user, $driver);
    }

    /**
     * Send a successful response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendSuccessResponse()
    {
       // echo "hello";
        return redirect('myaccount');
    }

    /**
     * Send a failed response with a msg
     *
     * @param null $msg
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedResponse($msg = null)
    {
        return redirect()->route('social.login')
            ->withErrors(['msg' => $msg ?: 'Unable to login, try with another provider to login.']);
    }

    protected function loginOrCreateAccount($providerUser, $driver)
    {
        // check for already has account
     
        $user = User::where('email', $providerUser->getEmail())->first();
      
        // if user already found
        if( $user ) {
            // update the avatar and provider that might have changed
               
            $png_url="";
            if($providerUser->avatar!=""){
                $imgold=public_path().'/upload/profile/' . $user->profile_pic;
                $png_url = "profile-".mt_rand(100000, 999999).".png";
                $path = public_path().'/upload/profile/' . $png_url;
                $content=$this->file_get_contents_curl($providerUser->avatar);
                $savefile = fopen($path, 'w');
                fwrite($savefile, $content);
                fclose($savefile);
                $img=public_path().'/upload/profile/' . $png_url;
                Session::put("profile_pic",$img);
                unset($imgold);
            } 
          
            if($driver=="facebook"){
                $type=2;
            }
            elseif($driver=="google"){
                $type=3;
            }else{
                $type=1;
            }
            
            
            $user->profile_pic=$png_url;
            $user->soical_id=$providerUser->id;
            $user->login_type=$type;
            $user->save();
            Session::put("login_username",$user->first_name." ".$user->last_name);
            Session::put("user_id",$user->id);
        } else {
            // create a new user
           
            $checkuser=User::where("email",$providerUser->getEmail())->first();
                if($checkuser){
                        return $this->sendFailedResponse("email already existe");
                }
                else{
                        if($driver=="facebook"){
                            $type=2;
                        }
                        elseif($driver=="google"){
                            $type=3;
                        }else{
                            $type=1;
                        }
                        $png_url="";
                        if($providerUser->avatar!=""){
                            $png_url = "profile-".mt_rand(100000, 999999).".png";
                            $path = public_path().'/upload/profile/' . $png_url;
                            $content=$this->file_get_contents_curl($providerUser->avatar);
                            $savefile = fopen($path, 'w');
                            fwrite($savefile, $content);
                            fclose($savefile);
                             $img=public_path().'/upload/profile/' . $png_url;
                             Session::put("profile_pic",$img);
                        }
                    $str=explode(" ",$providerUser->getName());
                    $user=new User();
                    $user->first_name=$str[0];
                    $user->last_name=$str[1];
                    $user->email=$providerUser->getEmail();
                    $user->soical_id=$providerUser->id;
                    $user->login_type=$type;
                    $user->profile_pic=$png_url;
                    $user->save();
                     Session::put("login_username",$user->first_name." ".$user->last_name);
                    Session::put("user_id",$user->id); 
                }
           
             
        }
       
        Auth::login($user, true);

        return $this->sendSuccessResponse();
    }

    /**
     * Check for provider allowed and services configured
     *
     * @param $driver
     * @return bool
     */
    private function isProviderAllowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }
    public function file_get_contents_curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}