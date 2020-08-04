<?php

defined('ACCESS') or die('No direct script access.');

/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Pages_extension
{

  private $dburl = EXTENSIONS.'/pages/src/storage/pages.db';
  private $dbname = 'pages';
  private $dbtable = 'pages';
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

    Action::add('footer',function(){
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
  public function api($category="all",$name = "all")
  {

    $cat = ($category == 'all') ? $cat = 'all' : $cat = $category;

    Url::cors();
    if($name == "all"){
      if($category == 'all'){
        $out = App::Db($this->dburl)->select('pages');
        Url::json($out);
      }else{
        $out = App::Db($this->dburl)->select('pages','category = :category',array(':category' => $cat));
        Url::json($out);
      }
    }else{
      $data = App::Db($this->dburl)->select('pages','category = :category and name = :name',array(':category' => $cat,':name' => $name));
      if($data){
        $data = $data[0];
        $content = Shortcode::parse($data['content']);
        $parse = Shortcode::parse($content);
        $parse2 = Shortcode::parse($parse);
        $parse3 = Shortcode::parse($parse2);
        $output = $parse3;
        $out = array(
          'uid' => $data['uid'],
          'name' => $data['name'],
          'title' => $data['title'],
          'description' => $data['description'],
          'image' => $data['image'],
          'keywords' => $data['keywords'],
          'status' => $data['status'],
          'created' => $data['created'],
          'updated' => $data['updated'],
          'author' => $data['author'],
          'category' => $data['category'],
          'content' => base64_encode($output)
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
   *  Preview content
   *
   * @param  string $name
   * @return array json
   */
  public function preview($name)
  {
    $data = App::Db($this->dburl)->select('pages','name = :name',array(':name' => $name));
    if($data){
      $data = $data[0];
      $head = '<title>'.$data['title'].'</title>';
      $head .= '<meta charset="UTF-8">';
      $head .= '<meta http-equiv="X-UA-Compatible" content="ie=edge">';
      $head .= '<meta name="keywords" content="'.$data['keywords'].'" />';
      $head .= '<meta name="description" content="'.$data['description'].'"/>';
      $head .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />';
      $head .= '<meta name="application-name" content="Cloud CMS" />';
      $head .= '<meta property="og:title" content="'.$data['title'].'" />';
      $head .= '<meta property="og:type" content="website" />';
      $head .= '<meta property="og:image" content="'.$data['image'].'" />';
      $head .= '<meta property="og:url" content="'.Url::base().'" />';
      $head .= '<meta property="og:description" content="'.$data['description'].'" />';
      $head .= '<meta name="twitter:card" content="summary" />';
      $head .= '<meta name="twitter:title" content="'.$data['title'].'" />';
      $head .= '<meta name="twitter:description" content="'.$data['description'].'" />';
      $head .= '<meta name="twitter:image" content="'.$data['image'].'" />';

      $content = Shortcode::parse($data['content']);
      $parse = Shortcode::parse($content);
      $parse2 = Shortcode::parse($parse);
      $output = Shortcode::parse($parse2);

      echo '<!Doctype html><html lang="en"><head>';
      echo $head;
      Action::run('theme_head');
      echo '</head><body>';
      echo '<div class="container">'.$output.'</div>';
      Action::run('theme_footer');
      echo '</body></html>';
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
      $storage = App::Db($this->dburl)->run("
        SELECT * FROM $name 
        ORDER BY updated 
        DESC LIMIT $limit 
        OFFSET $start"
      );
      
      $count = App::Db($this->dburl)->run("SELECT COUNT(*) FROM $name")[0]["COUNT(*)"];

      echo App::page(array(
        'name' => $this->dbname,
        'num' => $num,
        'last' => ceil($count / $this->dblimit) - 1,
        'total' => $count,
        'pages' => $storage,
        'title' => ucfirst($this->dbname),
        'content' => App::partial(EXTENSIONS.'/'.$this->dbname.'/src/templates/index.html')
      ));
    }else App::die();
  }


  public function search($name)
  {
    if(App::isLogged()){
      $bind = array(":search" => "%$name%");
      $results = App::Db($this->dburl)->select($this->dbname, "name LIKE :search", $bind);
      Url::json(array(
        'total' => count($results),
        'title' => 'Search',
        'content' => $results
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
          'name' => Text::slug(ucfirst(Url::post('title'))),
          'title' => Url::post('title'),
          'description' => Url::post('description'),
          'image' => Url::post('image'),
          'keywords' => Url::post('keywords'),
          'status' => (Url::post('status') == 'on') ? 1 : 0,
          'content' => Url::post('content'),
          'created' => date('Y-m-d'),
          'author' => Session::get('name'),
          'category' => (Url::post('category')) ? ucfirst(Url::post('category')) : 'all',
        );
        $insert = App::Db($this->dburl)->insert($this->dbname, $arr);
        if($insert){
          Debug::log('New page created by '.Session::get('name'));
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
   *  Edit page
   *
   * @param  number $uid
   * @return array
   */
  public function edit($uid = 0)
  {
    if(App::isLogged())
    {
      if(array_key_exists('update',$_POST)){
        $arr = array(
          'title' => Url::post('title'),
          'description' => Url::post('description'),
          'image' => Url::post('image'),
          'keywords' => Url::post('keywords'),
          'status' => (Url::post('status') == 'on') ? 1 : 0,
          'content' => Url::post('content'),
          'updated' => date('Y-m-d'),
          'category' => (Url::post('category')) ? ucfirst(Url::post('category')) : 'all',
        );
        $update = App::Db($this->dburl)->update($this->dbname, $arr, "uid = :uid",array(':uid' => $uid));
        if($update){
          Message::set('The file has been updated!');
          Url::redirect(Url::base().'/'.$this->dbname.'/edit/'.$uid);
        }else{
          Message::set('Error maybe the name already exists !');
          Url::redirect(Url::base().'/'.$this->dbname.'/edit/'.$uid);
        }
      }

      $storage = App::Db($this->dburl)->select($this->dbname,'uid = :uid',array('uid' => $uid));
      echo App::page(array(
        'page' => $storage[0],
        'name' => $this->dbname,
        'title' => ucfirst($this->dbname).' - Edit',
        'content' => App::partial(EXTENSIONS.'/'.$this->dbname.'/src/templates/edit.html')
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
        $update = App::Db($this->dburl)->update($this->dbname, $arr, "uid = :uid",array(':uid' => $uid));
        if($update){
          Message::set('The file has been renamed!');
          Url::redirect(Url::base().'/'.$this->dbname);
        }else{
          Message::set('Error maybe the name already exists !');
          Url::redirect(Url::base().'/'.$this->dbname.'/rename/'.$uid);
        }
      }

      $storage = App::Db($this->dburl)->select($this->dbname,'uid = :uid',array('uid' => $uid));
      echo App::page(array(
        'page' => $storage[0],
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
    if(App::isLogged()){
      $delete = App::Db($this->dburl)->delete($this->dbname, "uid = :uid", array(':uid' => $uid));
      if($delete){
        Debug::log('User '.Session::get('name').' delete page');
        Message::set('The file has been deleted!');
        Url::redirect(Url::base().'/'.$this->dbname);
      }
    }else App::die();
  }

}
