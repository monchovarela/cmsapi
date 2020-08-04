<?php

defined('ACCESS') or die('No direct script access.');



/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Login_extension
{


  private $DB_USERS = EXTENSIONS.'/login/src/storage/users.dbx';

  /**
   *  Settings view
   * 
   * @return array
   */
  public function settings()
  {
    if(App::isLogged())
    {
      echo App::page(array(
        'title' => 'Settings',
        'content' => App::partial(EXTENSIONS.'/login/src/templates/settings.html')
      ));
    }else App::die();
  }


  /**
   *  Logout
   * 
   * @return func
   */
  public function logout()
  {
    if(App::isLogged()){
      Debug::log("Logout ".Session::get('api_name'));
      Session::destroy();
      Url::redirect(Url::base());
    }else App::die();
  }



  /**
   *  init login
   * 
   * @return func
   */
  public function init()
  {
    if(!App::isLogged()){
      if(array_key_exists("login",$_POST)){
        if(array_key_exists("robot",$_POST)){
          if(array_key_exists("email",$_POST) && array_key_exists("pass",$_POST)){
            // post email & pass
            $email = Url::post('email');
            $pass = sha1(md5(Url::post('pass')));
            $bind = array(
              ":email" => $email,
              ":pass" => $pass
            );
            $db = $this->DB_USERS;
            $out = App::Db($db)->select("users","email = :email and password = :pass",$bind);
            if($out){
              // set session
              Session::set('api_token',sha1(md5($email.$pass)));
              Session::set('api_name',$out[0]['name']);
              Session::set('api_email',$out[0]['email']);
              Session::set('api_role',$out[0]['role']);
              Debug::log("User enter");
              // set msg and redirect
              Message::set("Welcome ".$out[0]['name']);
              Url::redirect(Url::base());
            }else{
              // error msg write log and redirect
              Message::set("The email not exists or password is not correct.");
              Debug::log("Login error");
              Url::redirect(Url::base());
            }
          }
        }else{
          Message::set("You are a robot ? please check the value");
          Debug::log("Login form not check robot");
          Url::redirect(Url::base());
        }
      }
      echo App::page(array(
        'title' => 'Login',
        'content' => App::partial(EXTENSIONS.'/login/src/templates/login.html')
      ));
    }else{
      echo App::page(array(
        'title' => 'Dashboard',
        'content' => App::partial(EXTENSIONS.'/login/src/templates/dashboard.html')
      ));
    }
  }


  /**
   *  Get extensions
   * 
   * @return array
   */
  public static function getExtensions()
  {
      $extensions_dir = Dir::scan(EXTENSIONS);
      $html = '<ul class="list-group">';
      foreach ($extensions_dir as $extension) {
          $jsonfile = EXTENSIONS.'/'.$extension.'/config.json';
          if (File::exists($jsonfile)) {
              $extensionsFile = file_get_contents($jsonfile);
              $json = json_decode($extensionsFile, true);
              $obj = $json[0];
              if (is_array($obj)) {
                  $filename = $obj['filename'];
                  if ($filename == 'login') continue;
                  $name = $obj['name'];
                  $filename = $obj['filename'];
                  $status = ($obj['enabled']) ? 'Active' : 'Inactive';
                  $html .= '<li class="list-group-item bg-dark text-light mb-2">
                              <p><a href="'.Url::base().'/'.$filename.'">'.$name.'</a></p>
                              <p>'.$obj['description'].'</p>
                              <p>
                                <small>
                                  <b>Author: </b> <a target="_blank" href="'.$obj['url'].'">'.$obj['author'].'</a>
                                  - <b>Version: </b> <span class="text-danger">'.$obj['version'].'</span>
                                  - <b>Status: </b>  <a class="text-primary" href="'.Url::base().'/ext/toggle/'.$filename.'">'.$status.'</a>
                                </small>
                              </p>
                            </li>';
              }
          }
      }
      $html .= '</ul>';
      return $html;
  }


  /**
   *  Toggle extension
   * 
   * @param  string
   * @return function
   */
  public function toggleExtension($name)
  {
      // scand dir better than file scan here
      $extensions_dir = Dir::scan(EXTENSIONS);
      foreach ($extensions_dir as $extension) {
          // get content of json
          $extensionsFile = file_get_contents(EXTENSIONS.'/'.$extension.'/config.json');
          // decode json
          $json = json_decode($extensionsFile, true);
          $obj = $json[0];
          // is array !
          if (is_array($obj)) {
              // same name
              if ($obj['filename'] == $name) {
                  // toogle enabled or disabled
                  if ($obj['enabled']) {
                      Arr::set($json[0], 'enabled', false);
                  } else {
                      Arr::set($json[0], 'enabled', true);
                  }
                  file_put_contents(EXTENSIONS.'/'.$name.'/config.json', json_encode($json));

                  $enabled = ($obj['enabled']) ? 'Inactive' : 'Active';

                  Debug::log('The extension '.$name.' has changed to '.$enabled);
                  Message::set('The extension has changed to '.$enabled);
                  Url::redirect(Url::base().'/settings');
              }
          }
      }
  }


  /**
   *  Get users
   * 
   * @return array
   */
  public function users()
  {
    if(App::isLogged()){
      $db = $this->DB_USERS;
      echo App::page(array(
        'name' => 'users',
        'total' => count(App::Db($db)->select("users")),
        'users' => App::Db($db)->select("users"),
        'title' => 'Users',
        'content' => App::partial(EXTENSIONS.'/login/src/templates/users.html')
      ));
    }else App::die();
  }





  /**
   *  Add user
   * 
   * @return array
   */
  public function userAdd()
  {
    if(App::isLogged()){
      if(array_key_exists('insert',$_POST)){

        $secret_token = sha1(md5(Url::post('email').Url::post('password').uniqid().time()));

        $token = App::randomPassword(11);
        $token .= '-'.App::randomPassword(4);
        $token .= '-'.App::randomPassword(4);
        $token .= '-'.App::randomPassword(4);
        $token .= '-'.App::randomPassword(12);

        $arr = array(
          'name' => Url::post('name'),
          'email' => Url::post('email'),
          'role' => Url::post('role'),
          'token' => $token,
          'secret_token' => $secret_token,
          'password' => sha1(md5(Url::post('password'))),
          'created' => date('Y-m-d')
        );
        $db = $this->DB_USERS;
        $insert = App::Db($db)->insert('users', $arr);
        if($insert){
          Message::set('The user has been added!');
          Url::redirect(Url::base().'/users');
        }else{
          Message::set('Error maybe the name already exists !');
          Url::redirect(Url::base().'/users/add');
        }
      }

      echo App::page(array(
        'name' => 'users',
        'title' => 'Users Add',
        'content' => App::partial(EXTENSIONS.'/login/src/templates/users-add.html')
      ));
    }else App::die();
  }

  /**
   *
   *  User edit
   * 
   * @param  number
   * @return func
   */
  public function userEdit($uid)
  {
    if(App::isLogged()){

      $db = $this->DB_USERS;
      $user = App::Db($db)->select("users","uid = :uid",array("uid" => $uid))[0];

      if(array_key_exists('update',$_POST)){

        $secret_token = sha1(md5(Url::post('email').Url::post('password').uniqid().time()));
        
        $arr = array(
          'name' => Url::post('name'),
          'email' => Url::post('email'),
          'role' => Url::post('role'),
          'secret_token' => (Url::post('password')) ? $secret_token : $user['secret_token'],
          'token' => Url::post('token'),
          'password' => (Url::post('password')) ? sha1(md5(Url::post('password'))) : $user['password'],
          'updated' => date('Y-m-d')
        );

        $update = App::Db($db)->update('users', $arr, "uid = :uid",array(':uid' => $uid));
        
        if($update){
          Message::set('The user has been updated!');
          Url::redirect(Url::base().'/users');
        }else{
          Message::set('Error maybe the name already exists !');
          Url::redirect(Url::base().'/users/edit/'.$uid);
        }
      }

      echo App::page(array(
        'name' => 'users',
        'user' => $user,
        'title' => 'Users Edit',
        'content' => App::partial(EXTENSIONS.'/login/src/templates/users-edit.html')
      ));
    }else App::die();
  }


  /**
   *  User delete
   * 
   * @param  number $uid
   * @return func
   */
  public function userDel($uid)
  {
    if(App::isLogged())
    {
      $db = $this->DB_USERS;
      $all = App::Db($db)->select('users');
      if(count($all) > 1){
        $delete = App::Db($db)->delete('users', "uid = :uid", array(':uid' => $uid));
        if($delete){
          Debug::log('User '.Session::get('name').' delete user');
          Message::set('The user has been deleted!');
          Url::redirect(Url::base().'/users');
        }
      }else{
        Debug::log('User '.Session::get('name').' try to delete user');
        Message::set('We need one user to work');
        Url::redirect(Url::base().'/users');
      }      
    }else App::die();
  }
}
