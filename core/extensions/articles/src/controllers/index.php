<?php

defined('ACCESS') or die('No direct script access.');

/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Articles_extension
{

  private $dburl = EXTENSIONS.'/articles/src/storage/articles.db';
  private $dbname = 'articles';
  private $dbtable = 'articles';
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
   *  Preview content
   * 
   * @param  string $name
   * @return array json
   */
  public function preview($name)
  {
    $data = App::Db($this->dburl)->select('articles','name = :name',array(':name' => $name));
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
      
      $file = Url::base().'/core/extensions/'.$this->dbname.'/src/assets/preview.css';

      $head .= '<link rel="stylesheet" href="'.$file.'"/>';
      $body = '<div class="container mt-5 mb-5">';
      $body .= '<div class="row">';
      $body .= '<div class="col-md-8 offset-2 m-auto">';
      $body .= base64_decode($data['content']);
      $body .= '</div>';
      $body .= '</div>';
      $body .= '</div>';

      $html = '<!Doctype html><html lang="es"><head>'.$head.'</head></body>'.$body.'</body></html>';
      echo($html);
    }
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
        $out = App::Db($this->dburl)->select('articles');
        Url::json($out);
      }else{
        $out = App::Db($this->dburl)->select('articles','category = :category',array(':category' => $cat));
        Url::json($out);
      }
    }else{
      $data = App::Db($this->dburl)->select('articles','category = :category and name = :name',array(':category' => $cat,':name' => $name));
      if($data){
        $data = $data[0];
        Url::json($data);
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
        'articles' => $storage,
        'title' => ucfirst($this->dbname),
        'content' => App::partial(EXTENSIONS.'/'.$this->dbname.'/src/templates/index.html')
      ));
    } else App::die();
  }


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
          'name' => Text::slug(Url::post('title')),
          'title' => Url::post('title'),
          'description' => Url::post('description'),
          'image' => Url::post('image'),
          'keywords' => Url::post('keywords'),
          'status' => (Url::post('status') == 'on') ? 1 : 0,
          'content' => Url::post('content'),
          'data' => Url::post('data'),
          'created' => date('Y-m-d'),
          'author' => Session::get('name'),
          'category' => (Url::post('category')) ? ucfirst(Url::post('category')) : 'all',
        );
        $insert = App::Db($this->dburl)->insert($this->dbname, $arr);
        if($insert){
          Debug::log('New article created by '.Session::get('name'));
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
    } else App::die();
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
          'data' => Url::post('data'),
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
    } else App::die();
  }


  /**
   *  Upload file
   * 
   * @param  number $uid
   * @return array
   */
  public function upload()
  {
    if(App::isLogged()){ 
      $type = File::ext($_FILES['image']['name']);
      $tempFile = $_FILES['image']['tmp_name'];
      $file = File::name(Url::parse($_FILES['image']['name'])).'.'.File::ext($_FILES['image']['name']);
      if($type == 'svg' || $type == 'jpg' || $type == 'png' || $type == 'gif' || $type == 'JPG' || $type == 'JPEG' || $type == 'jpeg'){
        $targetFile = PUBLICFOLDER .'/images/'.$file;
        if(!Dir::exists(ROOT.'/public/images/')) Dir::create(ROOT.'/public/images/');
        if(move_uploaded_file($tempFile, $targetFile)){
          Debug::log('User'.Session::get('name').' upload article file '.$file);
          print_r(json_encode(array(
            'success' => true,
            'file' => array(
              'url' => Url::base().'/public/images/'.$file
            )
          )));
        }
      }
    } else App::die();
  }


  /**
   *  Rename page
   * 
   * @param  number $uid
   * @return array
   */
  public function rename($uid = 0)
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