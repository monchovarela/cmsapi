<?php

defined('ACCESS') or die('No direct script access.');

/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Gallery_extension
{
    
  private $dburl = EXTENSIONS.'/gallery/src/storage/gallery.dbx';
  private $dbname = 'gallery';
  private $dbtable = 'Galleries';
  private $dblimit = 6;

  /**
  *  Construct
  */
  public function __construct()
  {
      // head assets
      Action::add('head',function(){
        $file = Url::base().'/core/extensions/'.$this->dbname.'/src/assets/style.css?'.time();
        echo '<link rel="stylesheet" href="'.$file.'"/>';
      });
      // footer assets
      Action::add('footer',function(){
        $file = Url::base().'/core/extensions/'.$this->dbname.'/src/assets/index.js?'.time();
        echo '<script rel="javascirpt" src="'.$file.'?'.time().'"></script>';
      });
  }

  
  /**
   *  Resize image
   * 
   * @param  string $file
   * @param  string $output
   * @param  integer $w
   * @param  integer $w
   */
  function resize_image($file,$output, $w, $h) {
    $image = new SimpleImage();
    $image->load($file);
    $image->resize($w, $h);
    $image->save($output);
  }

  /**
   *  Upload image
   * 
   * @param  string $name
   * @return array files
   */
  public function uploadImage($name)
  {
    if(App::isLogged())
    { 

      $type = File::ext($_FILES['file']['name']);
      $tempFile = $_FILES['file']['tmp_name'];
      $file = File::name(Url::parse($_FILES['file']['name'])).'.'.File::ext($_FILES['file']['name']);
      
      $extensions = array('jpg', 'jpeg', 'png');
      if(in_array($type,$extensions))
      {
        // small folder 
        $folderFile = ROOT.'/public/galleries/'.$name;
        // if not exists create folders
        if(!Dir::exists($folderFile.'/small'))Dir::create($folderFile.'/small');
        if(!Dir::exists($folderFile.'/medium'))Dir::create($folderFile.'/medium');
        if(!Dir::exists($folderFile.'/large'))Dir::create($folderFile.'/large');

        $smallFile = $folderFile.'/small/'.$file;
        $mediumFile = $folderFile.'/medium/'.$file;
        $largeFile = $folderFile.'/large/'.$file;
        // resize and save images
        $this->resize_image($tempFile,$smallFile,200, 100);
        $this->resize_image($tempFile,$mediumFile,1024, 720);
        $this->resize_image($tempFile,$largeFile,1280, 768);

        Debug::log('User'.Session::get('name').' upload file ');
        Url::json(array(
            'status' => true,
            'message' => 'The image has been uploaded!',
        ));
      }

    }else{
      Url::json(array('status' => false,'type'=>$type));
    }
  }

  /**
   *  Delete image
   * 
   * @param  string $file
   * @return array files
   */
  public function deleteImage($file)
  {
      $medium_url = str_replace('small','medium',$file);
      $large_url = str_replace('small','large',$file);

      $small = ROOT.$file;
      $medium = ROOT.$medium_url;
      $large = ROOT.$large_url;
      
      if(File::exists($small)) File::delete($small);
      if(File::exists($medium)) File::delete($medium);
      if(File::exists($large)) File::delete($large);

      Debug::log('User'.Session::get('name').' delete image '.$file);

      $name_of_gallery = explode('/',$file);
      return $this->api($name_of_gallery[3]);

  }

  /**
   *  Scan folders
   * 
   * @param  string $name
   * @return array files
   */
  public function scanImages($folder,$name)
  {
    // if not exists create folders
    if(!Dir::exists($folder.'/'.$name))Dir::create($folder.'/'.$name);
    // find images and remove ROOT url on small folder
    $files = [];
    $folderFiles = File::scan($folder.'/'.$name,array('jpg', 'jpeg', 'png'));
    foreach ($folderFiles as $item) {

      $replace_root = str_replace(ROOT, '', $item);
      $replace_slash = str_replace('\\', '/', $replace_root);
      $source_url = rtrim($replace_slash, '/');
      $files[] = $source_url;
    }
    return $files;
  }

  /**
   *  Preview
   */
  public function preview($name)
  {
    $data = App::Db($this->dburl)->select($this->dbtable,'name = :name',array(':name' => $name));
    if($data){
      $data = $data[0];
      $head = '<title>'.$data['title'].'</title>';
      $head .= '<meta charset="UTF-8">';
      $head .= '<meta http-equiv="X-UA-Compatible" content="ie=edge">';
      $head .= '<meta name="description" content="'.$data['description'].'"/>';
      $head .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />';
      $head .= '<meta name="application-name" content="Cloud CMS" />';
      $head .= '<meta property="og:title" content="'.$data['title'].'" />';
      $head .= '<meta property="og:type" content="website" />';
      $head .= '<meta property="og:image" content="'.$data['poster'].'" />';
      $head .= '<meta property="og:url" content="'.Url::base().'" />';
      $head .= '<meta property="og:description" content="'.$data['description'].'" />';
      $head .= '<meta name="twitter:card" content="summary" />';
      $head .= '<meta name="twitter:title" content="'.$data['title'].'" />';
      $head .= '<meta name="twitter:description" content="'.$data['description'].'" />';
      $head .= '<meta name="twitter:image" content="'.$data['poster'].'" />';

      $head .= '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"/>';
      $head .= '<script rel="javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>';
      $head .= '<script rel="javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>';

      $head .= '<style>
        .jumbotron{
          position:relative;
          background: url('.$data['poster'].') no-repeat center center #333;
          background-size: cover;
          color:#333;
        }
        .jumbotron:after {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(255, 255, 255, 0.88);
          z-index: 1;
        }
        .jumbotron .container {
          position: relative;
          z-index: 5;
        }
      </style>';

      $folder = ROOT.'/public/galleries/'.$name;
      $medium = $this->scanImages($folder,'medium');
      $template = '';
      foreach ($medium as $item) {
         $template .='<div class="col-6 mb-3">
          <a href="'.Url::base().$item.'">
            <img class="img-fluid" src="'.Url::base().'/'.$item.'" />
          </a>
         </div>';
      };

    
      $row = '<div class="jumbotron jumbotron-fluid">
        <div class="container">
          <h1 class="display-3 mt-4 text-dark font-weight-bold">'.$data['title'].'</h1>
          <p class="lead">'.$data['description'].'</p>
          <p><b>Update: </b>'.$data['update_at'].'</p>
        </div>
      </div>';

      $row .= '<div class="container"><div class="row">'.$template.'</div></div>';
      $body = $row;

      $html = '<!Doctype html><html lang="en"><head>'.$head.'</head><body>'.$body.'</body></html>';
      die($html);
    }
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
      $out = App::Db($this->dburl)->select($this->dbtable);
      Url::json($out);
    }else{
      $out = App::Db($this->dburl)->select($this->dbtable,'name = :name',array(':name' => $name));
      if($out)
      {
        // source folder
        $folder = ROOT.'/public/galleries/'.$name;
        // if not exists create
        if(!Dir::exists($folder))
        {
           Dir::create($folder);
           Dir::create($folder.'/small');
           Dir::create($folder.'/medium');
           Dir::create($folder.'/large');
        }
        // scan folders
        $small = $this->scanImages($folder,'small');
        $medium = $this->scanImages($folder,'medium');
        $large = $this->scanImages($folder,'large');
        // json output
        Url::json(array(
          'status' => true,
          'info' => $out,
          'total' => count($medium),
          'images' => array(
            'small' => $small,
            'medium' => $medium,
            'large' => $large
          )
        ));
      }else{
        $out = array(
          'status' => false,
          'info' => array(
            'title' => '404',
            'description' => 'Not found'
          )
        );
        Url::json($out);
      }
    }
  }


  /**
   *  Update name
   * 
   * @param  string $name
   * @return array json
   */
  public function update($name)
  {
      $_POST = json_decode(file_get_contents("php://input"),true);

      if(App::isLogged())
      {
        $uid = Url::post('uid');
        $arr = array(
          'title' => Url::post('title'),
          'name' => Url::post('name'),
          'description' => Url::post('description'),
          'poster' => Url::post('poster'),
          'updated_at' => date('Y-m-d'),
        );
        $update = App::Db($this->dburl)->update($this->dbtable, $arr, "uid = :uid",array(':uid' => $uid));
        if($update){

          Debug::log('User'.Session::get('name').' update gallery '.$arr['name']);

          Url::json(array(
            'status' => true,
            'message' => 'The file has been updated!'
          ));
        }else{
          Url::json(array(
            'status' => false,
            'message' => 'Error maybe the title already exists !'
          ));
        }
      }else App::die();
  }

  /**
   *  Delete name
   * 
   * @param  string $name
   * @param  integer $uid
   * @return array json
   */
  public function delete($name,$uid)
  {
    if(App::isLogged()){
      $delete = App::Db($this->dburl)->delete($this->dbtable, "uid = :uid", array(':uid' => $uid));
      if($delete){

        $folder = ROOT.'/public/galleries/'.$name;
        if(Dir::exists($folder)) Dir::delete($folder);

        Debug::log('User'.Session::get('name').' delete gallery '.$name);
        
        return Url::json(array(
          'status' => true,
          'message' => 'The gallery has been deleted!'
        ));
      }else{
        return Url::json(array(
          'status' => false,
          'data' => $this->api($name),
          'message' => 'Error on delete gallery !'
        ));
      }
    }else App::die();
  }

  /**
   *  New gallery
   */
  public function newGallery()
  {
    $_POST = json_decode(file_get_contents("php://input"),true);

    if(App::isLogged())
    {
      $arr = array(
        'name' => Text::slug(Url::post('title')),
        'title' => Url::post('title'),
        'description' => Url::post('description'),
        'poster' => Url::post('poster')
      );
      $create = App::Db($this->dburl)->insert($this->dbtable, $arr);
      if($create){
        Debug::log('New gallery '.$arr['name'].' created by '.Session::get('name'));
        Url::json(array(
          'status' => true,
          'message' => 'The file has been create!'
        ));
      }else{
        Url::json(array(
          'status' => false,
          'message' => 'Error maybe the title already exists !'
        ));
      }
    }else App::die();
    
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
      // database name
      $name = $this->dbname;
      // view template
      $template = EXTENSIONS.'/'.$this->dbname.'/src/templates/index.html';
      // data
      $data = array(
        'name' => $name,
        'num' => $num,
        'title' => ucfirst($name),
        'content' => App::partial($template)
      );
      // show page data
      echo App::page($data);
    }else App::die();
  }
}