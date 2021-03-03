<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Auth model.
 * 
 * @author Cecep Rokani
 */
use Jenssegers\Agent\Agent;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
class AuthModel extends CI_Model {
    private $secretkey = '@HLytwwap+MV4w4b9(Q!aTxm)EyTb.s**my2Z7%D';

    public function __construct()
    {
        parent::__construct();
    }

    public function checkUsername($username)
    {
        $status = false;

        $this->db->where("username", "{$username}");
        $query	= $this->db->get('users');

        if ($query->num_rows() > 0) {
            $status = true;
        }

        return $status;
    }

    public function processLogin($username, $password)
    {
        // variable initialization, make sure everything is false.
        $result['status']   = 'failed';
        $result['message']  = '';

        $loginstatus        = false;
        $updatehash         = false;
        $userortu           = false;
        $blackliststatus    = false;
        $loginmethod        = 'normal';
        
        // if provided value is empty
        if (empty($username) or empty($password)) {
            $result['message'] = "Username dan password tidak boleh kosong";
            return $result;
        }

        $this->db->where("username", "{$username}");
        $query	= $this->db->get('users');

        // is the user even exist?
        if ($query->num_rows() == 0) {
            $result['message'] = "Username atau Password yang anda masukan salah!";
            return $result;
        }

        $getUser	            = $query->row();
        $user 		            = $query->last_row('array');

        if ($user['status'] == "suspend") {
            $result['message']  = 'Akun tersuspend, mohon hubungi developer.';
            return $result;
        }

        $checkAttemptStatus     = $this->checkAttempt($getUser->id, 'signin');
        if($checkAttemptStatus['blacklist']) {
            $blackliststatus    = true;
            $result['message']  = "Terlalu banyak upaya login yang gagal, coba lagi dalam 30 menit.";
        }

        if (!$blackliststatus) {
            // check if using newer hash (general user)
            if (password_verify($password, $getUser->password)) {
                $loginstatus = true;
            }

            // check if using cleartext (general user)
            if ($getUser->password == $password) {
                $loginstatus = true;
                $updatehash = true;
            }

            // sorry, please recheck your password.
            if (!$loginstatus) {
                $result['message'] = "Username atau Password yang anda masukan salah!";
                $this->recordLoginAttempt($user["id"], $loginmethod, 'wrong_password', "signin");
                return $result;
            }

            if ($loginstatus) {
                // when needed, update the hash to more stronger crypto.
                if ($updatehash) {
                    $updatedata['password'] = password_hash($password, PASSWORD_BCRYPT);
                    $this->db->where("id", $user["id"]);
                    $this->db->update("users", $updatedata);
                }

                unset($user["password"]);
                
                $user['logged_in']              = true;
                $user['key']                    = $this->generateToken($user['id'], $user['username'], $user['name'], $user['role']);

                $this->session->set_userdata($user);
                // congratulations, you've made it.
                $result["user"]	    = $user;
                $result["status"]	= "success";
                $result["message"]	= "Login successfully!";

                $this->recordLoginAttempt($user["id"], $loginmethod, "success", "signin");
            }
        }

        return $result;
    }

    public function generateToken($id, $username, $name, $role)
    {
        $signer = new Sha256();
        $time = time();

        $token = (new Builder())->issuedBy('https://authentication.abubakarsufyan.org') // Configures the issuer (iss claim)
                        ->permittedFor(base_url()) // Configures the audience (aud claim)
                        ->identifiedBy('pezc2ds5Cp8', true) // Configures the id (jti claim), replicating as a header item
                        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
                        ->expiresAt($time + 43200) // Configures the expiration time of the token (exp claim)
                        ->withClaim('id', $id)
                        ->withClaim('username', $username)
                        ->withClaim('name', $name)
                        ->withClaim('role', $role)
                        ->getToken($signer, new Key($this->secretkey)); // Retrieves the generated token
        
        return (string)$token;
    }

    public function checkAttempt($user_id, $type)
    {
        $result['attempt'] = 0;
        $result['blacklist'] = false;

        $ip_address = $this->getUserIP();

        $this->db->where('user_id', $user_id);
        $this->db->where('ip_address', $ip_address);
        $this->db->where('status !=', 'success');
        $this->db->where('type', $type);
        $this->db->where('time >=', 'DATE_SUB(NOW(),INTERVAL 30 MINUTE)', false);
        $result['attempt'] = $this->db->count_all_results('authentication_log');

        if ($result['attempt'] > 4) {
            $blockdata = $this->registerBlockIP($user_id, $ip_address);
            if (time() - strtotime($blockdata) < 1801) {
                $result['blacklist'] = true;
            }
        }

        return $result;
    }

    public function recordLoginAttempt($user_id, $method, $status, $type=null)
    {
        $result                     = false;
        $device_info                = $this->getDeviceInfo();
        $insertdata['ip_address']   = $device_info['ip_address'];
        $insertdata['device']       = $device_info['device'];
        $insertdata['platform']     = $device_info['platform'];
        $insertdata['browser']      = $device_info['browser'];
        $insertdata['version']      = $device_info['version'];
        $insertdata['time']         = $device_info['time'];
        $insertdata['type']         = $type;
        $insertdata['user_id']      = $user_id;
        $insertdata['status']       = $status;
        $insertdata['method']       = $method;

        $result = $this->db->insert('authentication_log', $insertdata);
        return $result;
    }

    public function validateUserToken()
	{		
        $result['status']       = false;
        $result['message']      = '';
        $result['data']         = new stdClass();

        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $token                  = $_SERVER['HTTP_AUTHORIZATION'];
            try {
                $token = (new Parser())->parse((string) $token);
                $signer = new Sha256();

                $data = new ValidationData();
                $data->setIssuer('https://authentication.abubakarsufyan.org');
                $data->setAudience(base_url());
                $data->setId('pezc2ds5Cp8');

                if ($token->verify($signer, $this->secretkey)) {
                    if ($token->validate($data)) {
                        $result['status']           = true;
                        
                        $result['data']->id         = $token->getClaim('id');
                        $result['data']->username   = $token->getClaim('username');
                        $result['data']->name       = $token->getClaim('name');
                        $result['data']->role       = $token->getClaim('role');
                        $result['data']->logged_in  = true;
                    } else {
                        $result['message'] = "Authorization token is expired or invalid.";
                    }
                } else {
                    $result['message'] = "Unauthorized Access.";
                }
            } catch (Exception $e) {
                $result['message'] = "Token or authorization couldn't be processed.";
            }
        } else {
            $result['message'] = "Token or authorization incomplete.";
        }

        if ($result['status']) {
            return $result['data'];
        } else {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    public function getDeviceInfo()
    {
        $devicedata = array();
        $devicedata['time'] = date('Y-m-d H:i:s');

        $devicedata['ip_address']   = $this->getUserIP();

        $device_status              = "Unknown";
        $devicedata['device']       = $device_status;
        $devicedata['platform']     = "Unknown";
        $devicedata['browser']      = "Unknown";
        $devicedata['version']      = "Unknown";
        
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $agent = new Agent();
            $agent->setUserAgent($_SERVER['HTTP_USER_AGENT']);
            if ($agent->isMobile()) {
                $device_status = "Mobile";
            } elseif ($agent->isTablet()) {
                $device_status = "Tablet";
            } elseif ($agent->isDesktop()) {
                $device_status = "Desktop";
            } elseif ($agent->isRobot()) {
                $device_status = "Robot";
            }

            $devicedata['device']       = $device_status;
            $devicedata['platform']     = $agent->platform();
            $devicedata['browser']      = $agent->browser();
            $devicedata['version']      = $agent->version($devicedata['browser']);
        }

        return $devicedata;
    }

    public function registerBlockIP($user_id, $ip_address)
    {
        $date = date('Y-m-d H:i:s');

        $this->db->where('user_id', $user_id);
        $this->db->where('ip_address', $ip_address);
        $this->db->where('block_time >=', 'DATE_SUB(NOW(),INTERVAL 1 HOUR)', false);
        $checkdata = $this->db->get('authentication_blacklist')->row();

        if (empty($checkdata->id)) {
            $insertdata['user_id'] = $user_id;
            $insertdata['ip_address'] = $ip_address;
            $insertdata['block_time'] = $date;
            $this->db->insert('authentication_blacklist', $insertdata);
        } else {
            $date = $checkdata->block_time;
        }

        return $date;
    }

    public function getUserIP()
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER)) {
            $proxy_list = explode (",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $client_ip = trim (end ($proxy_list));
            if (filter_var($client_ip, FILTER_VALIDATE_IP)) {
                $ip_address = $client_ip;
            }
        }

        return $ip_address;
    }
}