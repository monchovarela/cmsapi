<?php

defined('ACCESS') or die('No direct script access.');

/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Ijson_extension
{

  private $dburl = EXTENSIONS.'/ijson/src/storage/ijson.db';
  private $dbname = 'ijson';
  private $dbtable = 'vars';
  private $dblimit = 6;

  /**
   *  Construct
   */
  public function __construct()
  {
    Action::add('head',function(){
      $file = Url::base().'/core/extensions/'.$this->dbname.'/src/assets/style.css?'.time();
      echo '<link rel="stylesheet" href="'.$file.'"/>';
    });

    Action::add('head',function(){
      $file = Url::base().'/core/extensions/'.$this->dbname.'/src/assets/index.js?'.time();
      echo '<script rel="javascirpt" src="'.$file.'"></script>';
    });
  }

  /**
   *  Api name
   * 
   * @param  string $name
   * @return array json
   */
  public function api($name = "")
  {
    Url::cors();
    if($name == 'all'){
      $data = App::Db($this->dburl)->select($this->dbtable);
      $data[0]['content'] = json_decode($data[0]['content']);
      if($data)  Url::json($data);
      else Url::json(array('status' => false,'title' => '404 not found'));
    }else{
      $data = App::Db($this->dburl)->select(
        $this->dbtable,
        'name = :name',
        array(':name' => $name)
      );
      if($data){
        $data = $data[0];
        $out = array(
          'uid' => $data['uid'],
          'name' => $data['name'],
          'title' => $data['title'],
          'description' => $data['description'],
          'status' => $data['status'],
          'created' => $data['created'],
          'updated' => $data['updated'],
          'author' => $data['author'],
          'data' => json_decode($data['content']),
        );
        Url::json($out);
      }else{
        $out = array(
          'status' => '404',
          'description' => 'Error api not found'
        );
        Url::json($out);
      }
    }
  }

  /**
   * Init 
   * 
   * @return array
   */
  public function init($num)
  {
    if(App::isLogged())
    {
      $name = $this->dbtable;
      $limit = $this->dblimit;
      $start = $num * $limit;
      
      $storage = App::Db($this->dburl)->run("SELECT * FROM $name ORDER BY updated DESC LIMIT $limit OFFSET $start");

      $count = App::Db($this->dburl)->run("SELECT COUNT(*) FROM $name")[0]["COUNT(*)"];

      echo App::page(array(
        'name' => $this->dbname,
        'num' => $num,
        'last' => ceil($count / $this->dblimit) - 1,
        'total' => $count,
        'vars' => $storage,
        'title' => ucfirst($this->dbname),
        'content' => App::partial(EXTENSIONS.'/'.$this->dbname.'/src/templates/index.html')
      ));
    }else App::die();
  }

  /**
   * search by name
   */
  public function search($name)
  {
    if(App::isLogged()){
      $bind = array(":search" => "%$name%");
      $results = App::Db($this->dburl)->select($this->dbtable, "name LIKE :search", $bind);
      Url::json(array(
        'total' => count($results),
        'title' => 'Search',
        'content' => $results
      ));
    }else App::die();
  }

  /**
   *  Edit page
   * 
   * @param  number $uid
   * @return array
   */
  public function edit($uid = 0)
  {
    if(App::isLogged())
    {
      if(array_key_exists('update',$_POST))
      {

        $arr = array(
          'title' => Url::post('title'),
          'description' => Url::post('description'),
          'status' => (Url::post('status') == 'on') ? 1 : 0,
          'content' => (Url::post('content')) ? Url::post('content') : '[]',
          'updated' => date('Y-m-d'),
        );

        $update = App::Db($this->dburl)->update($this->dbtable, $arr, "uid = :uid",array(':uid' => $uid));

        if($update){
          Message::set('The file has been updated!');
          Url::redirect(Url::base().'/'.$this->dbname.'/edit/'.$uid);
        }else{
          Message::set('Error maybe the name already exists !');
          Url::redirect(Url::base().'/'.$this->dbname.'/edit/'.$uid);
        }
      }

      $storage = App::Db($this->dburl)->select(
        $this->dbtable,
        'uid = :uid',
        array('uid' => $uid)
      );

      echo App::page(array(
        'vars' => $storage[0],
        'name' => $this->dbname,
        'title' => ucfirst($this->dbname).' - Edit',
        'content' => App::partial(EXTENSIONS.'/'.$this->dbname.'/src/templates/edit.html')
      ));
    }else App::die();
  }

   /**
   * New page
   *
   * @return array
   */
  public function new()
  {
    if(App::isLogged())
    {
      if(array_key_exists('insert',$_POST)){
        $arr = array(
          'name' => Text::slug(Url::post('name')),
          'title' => Url::post('title'),
          'description' => Url::post('description'),
          'status' => (Url::post('status') == 'on') ? 1 : 0,
          'content' => (Url::post('content')) ? Url::post('content') : '[]',
          'created' => date('Y-m-d'),
          'author' => Session::get('api_name'),
        );
        $insert = App::Db($this->dburl)->insert($this->dbtable, $arr);
        if($insert){
          Debug::log('New page created by '.Session::get('api_name'));
          Message::set('The file has been created!');
          Url::redirect(Url::base().'/'.$this->dbname);
        }else{
          Message::set('Error maybe the name already exists !');
          Url::redirect(Url::base().'/'.$this->dbname.'/new');
        }
      }
      echo App::page(array(
        'name' => $this->dbname,
        'title' => ucfirst($this->dbname).' - New',
        'content' => App::partial(EXTENSIONS.'/'.$this->dbname.'/src/templates/new.html')
      ));
    }else App::die();
  }

  /**
   *  Rename page
   * 
   * @param  number $uid
   * @return array
   */
  public function rename($uid = 0)
  {
    if(App::isLogged())
    {
      if(array_key_exists('rename',$_POST)){
        $arr = array('name' => Text::slug(Url::post('new')));

        $rename = App::Db($this->dburl)->update(
          $this->dbtable, 
          $arr, 
          "uid = :uid",
          array(':uid' => $uid)
        );
        
        if($rename){
          Message::set('The file has been renamed!');
          Url::redirect(Url::base().'/'.$this->dbname);
        }else{
          Message::set('Error maybe the name already exists !');
          Url::redirect(Url::base().'/'.$this->dbname.'/rename/'.$uid);
        }
      }

      $storage = App::Db(
        $this->dburl)->select(
          $this->dbtable,
          'uid = :uid',
          array(
            'uid' => $uid
          )
      );
      echo App::page(array(
        'vars' => $storage[0],
        'name' => $this->dbname,
        'title' => ucfirst($this->dbname).' - rename',
        'content' => App::partial(EXTENSIONS.'/'.$this->dbname.'/src/templates/rename.html')
      ));
    }else App::die();
  }

  /**
   *  Delete page
   * 
   * @param  number $uid
   * @return array
   */
  public function del($uid = 0)
  {
    if(App::isLogged())
    {
      $delete = App::Db($this->dburl)->delete($this->dbtable, "uid = :uid", array(':uid' => $uid));
      if($delete){
        Debug::log('User '.Session::get('api_name').' delete '.$this->dbname);
        Message::set('The file has been deleted!');
        Url::redirect(Url::base().'/'.$this->dbname);
      }      
    }else App::die();
  }
}