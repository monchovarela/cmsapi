<?php

defined('ACCESS') or die('No direct script access.');

/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */
class Filemanager_extension
{
    
  private $name = 'filemanager';

  /**
  *  Construct
  */
  public function __construct()
  {
      // head assets
      Action::add('head',function(){
        $file = Url::base().'/core/extensions/'.$this->name.'/src/assets/style.css?'.time();
        echo '<link rel="stylesheet" href="'.$file.'"/>';
      });
      // footer assets
      Action::add('footer',function(){
        $file = Url::base().'/core/extensions/'.$this->name.'/src/assets/index.js?'.time();
        echo '<script rel="javascirpt" src="'.$file.'?'.time().'"></script>';
      });
  }

  /**
   * Get all files
   */
  public function api($name)
  {
    Url::cors();

    switch ($name) {
      case 'all':
        $files = File::scan(ROOT.'/public');
        break;
      case 'images':
        $types = array('jpg','JPG','jpeg','JPEG','png','gif');
        $files = File::scan(ROOT.'/public',$types);
        break;
      case 'videos':
        $types = array('mp4','webm');
        $files = File::scan(ROOT.'/public',$types);
        break;
      case 'audio':
        $types = array('mp3','wav','ogg');
        $files = File::scan(ROOT.'/public',$types);
        break;
      case 'docs':
        $types = array('pdf','txt','docx');
        $files = File::scan(ROOT.'/public',$types);
        break;
      case 'other':
        $types = array('json','html','css','rar','zip','tar');
        $files = File::scan(ROOT.'/public',$types);
        break;
    }

    $arr = array();
    foreach ($files as $file) {
      if(!is_dir($file) && !preg_match("/assets/",$file) && !preg_match("/galleries/",$file)){
        $link = str_replace(ROOT, Url::base(), $file);
        $link = str_replace("\\","/", $link);
        $arr[] = array(
          'name' => File::name($file),
          'ext' =>  File::ext($file),
          'other' => File::info($file,array('name','size','date')),
          'url' => $link
        );
      }
    }
    $out = $arr;
    Url::json($out);
  }

  /**
   *
   * Delete file
   * 
   * @param  string $name
   * @return string
   */
  public function deleteFile($name)
  {
    $name = base64_decode($name);
    $name= str_replace(Url::base(),ROOT,$name);
    if(App::isLogged()){
      if(File::delete($name)){
        Debug::log('User'.Session::get('name').' upload file ');
        Url::json(array(
          'status' => true,
          'message' => 'The file has been deleted!'
        ));
      }else{
        Url::json(array(
          'status' => true,
          'message' => 'Error on delete file !'
        ));
      }
    }else App::die();
  }

  /**
   * Rename file
   * 
   * @param string $old - Old name
   * @param string $new - New name
   * 
   * @return array
   */
  public function renameFile()
  {
    $_POST = json_decode(file_get_contents("php://input"),true);

    if(App::isLogged())
    {
        $old = $_POST['old'];
        $new = $_POST['new'];
        $ext = $_POST['ext'];

        // check the folder name by type
        $folder = PUBLICFOLDER;
        $type = $ext;
        if($type == 'svg' || $type == 'jpg' || $type == 'png' || $type == 'gif' || $type == 'JPG' || $type == 'JPEG' || $type == 'jpeg'){
          $folder = PUBLICFOLDER.'/images/'; 
        }elseif($type == 'psd' || $type == 'txt' || $type == 'html' || $type == 'py' || $type == 'doc' || $type == 'css' || $type == 'js' || $type == 'js' || $type == 'json' || $type == 'xml' || $type == 'docx' || $type == 'pdf' || $type == 'md' || $type == 'zip' || $type == 'tar' || $type == 'rar'){
          $folder = PUBLICFOLDER.'/documents/'; 
        }elseif($type == 'mp4' || $type == 'webm' || $type == 'ogv'){
          $folder = PUBLICFOLDER.'/video/'; 
        }elseif($type == 'mp3' || $type == 'wav' || $type == 'ogg'){
          $folder = PUBLICFOLDER.'/audio/'; 
        }

        // check if already exists new name
        if(File::exists($folder.'/'.$new.'.'.$ext))
        {
          Url::json(array('status' => false,'msg' => 'The name already exists'));
        }
        else
        {
          // check if already exists old name to rename
          if(File::exists($folder.'/'.$old.'.'.$ext))
          {
            File::rename($folder.'/'.$old.'.'.$ext,$folder.'/'.$new.'.'.$ext);
            Url::json(array('status' => true,'msg' => 'The file has been renamed!'));
          }
        }

    }
  }


  /**
   *
   * New file
   * 
   * @return string
   */
  public function uploadFile()
  {
    if(App::isLogged())
    { 

      $type = File::ext($_FILES['file']['name']);

      $tempFile = $_FILES['file']['tmp_name'];

      $file = File::name(Url::parse($_FILES['file']['name'])).'.'.File::ext($_FILES['file']['name']);
      
      if($type == 'svg' || $type == 'jpg' || $type == 'png' || $type == 'gif' || $type == 'JPG' || $type == 'JPEG' || $type == 'jpeg'){
        
        $targetFile = ROOT.'/public/images/'.$file;
        
        if(!Dir::exists(ROOT.'/public/images/')) Dir::create(ROOT.'/public/images/');
        
        if(move_uploaded_file($tempFile, $targetFile)){
          Debug::log('User'.Session::get('name').' upload file '.$file);
          Url::json(array('status' => true,'message' => 'The file has been uploaded!'));
        }

      }elseif($type == 'psd' || $type == 'txt' || $type == 'html' || $type == 'py' || $type == 'doc' || $type == 'css' || $type == 'js' || $type == 'js' || $type == 'json' || $type == 'xml' || $type == 'docx' || $type == 'pdf' || $type == 'md' || $type == 'zip' || $type == 'tar' || $type == 'rar'){
        
        $targetFile = ROOT.'/public/documents/'.$file;

        if(!Dir::exists(ROOT.'/public/documents/')) Dir::create(ROOT.'/public/documents/');

        if(move_uploaded_file($tempFile, $targetFile)){
          Debug::log('User '.Session::get('name').' upload file '.$file);
          Url::json(array('status' => true,'message' => 'The file has been uploaded!'));
        }

      }elseif($type == 'mp4' || $type == 'webm' || $type == 'ogv'){
        
        $targetFile = ROOT.'/public/video/'.$file;
        
        if(!Dir::exists(ROOT.'/public/video/')) Dir::create(ROOT.'/public/video/');
        
        if(move_uploaded_file($tempFile, $targetFile)){
          Debug::log('User '.Session::get('name').' upload file '.$file);
          Url::json(array('status' => true,'message' => 'The file has been uploaded!'));
        }

      }elseif($type == 'mp3' || $type == 'wav' || $type == 'ogg'){
       
        $targetFile = ROOT.'/public/audio/'.$file;
        
        if(!Dir::exists(ROOT.'/public/audio/')) Dir::create(ROOT.'/public/audio/');
        
        if(move_uploaded_file($tempFile, $targetFile)){
          Debug::log('User '.Session::get('name').' upload file '.$file);
          Url::json(array('status' => true,'message' => 'The file has been uploaded!'));
        }

      }else{
        Url::json(array('status' => false,'type'=>$type,'message' => 'Error on upload file!'));
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
      // view template
      $template = EXTENSIONS.'/'.$this->name.'/src/templates/index.html';
      // data
      $data = array(
        'name' => $this->name,
        'num' => $num,
        'title' => ucfirst($this->name),
        'content' => App::partial($template)
      );
      // show page data
      echo App::page($data);
    }else App::die();
  }
}