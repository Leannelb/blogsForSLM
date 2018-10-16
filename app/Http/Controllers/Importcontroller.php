<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \XMLReader;
use \DOMDocument;

class Importcontroller extends Controller{
  protected function data()
  {
    $doc = new \DOMDocument;
    $xml = new \XMLReader;
    $mainArray = [];
    $tags = [];
    $postid = 0;
    $xml->open('/Users/leanne/Sites/php-script/wordpress.xml');  
   
    while ($xml->read() && $xml->name !== 'item');
    
    while ($xml->name === 'item')
    {
      $node = simplexml_import_dom($doc->importNode($xml->expand(), true));
      
      if ($xml->nodeType == XMLReader::ELEMENT && !empty($node->category)) {
            //$tags[] = $xml->getAttribute('nicename');
      }
  
      $mainArray[] = ['postid'=>$node->postid, 'title'=>$node->title, 'published_at'=>$node->wppostdategmt, 'content'=>(string) $node->contentencoded, 'slug'=>$node->wppostname];
      //$tags = [];
      $xml->next('item');
    }
  
  
    $tags = [];
      $xml = new XMLReader();
  
      if (!$xml->open('/Users/leanne/Sites/php-script/wordpress.xml')) {
          die("Failed to open 'data.xml'");
      }
  
      while($xml->read()) {
          if ($xml->nodeType == XMLReader::ELEMENT && $xml->localName == 'postid') {
            $node = simplexml_import_dom($doc->importNode($xml->expand(), true));
            $postid = $node;
        }
          if ($xml->nodeType == XMLReader::ELEMENT && $xml->name == 'category') {
              $tags["$postid"][] = $xml->getAttribute('nicename');
          }
          
      }
  
      //print_r($tags);
      //die();
      $newArray = array();
      foreach($mainArray as $post)
      {
        $id = $post['postid'];
        if(!empty($tags["$id"]))
        {
          //die();
          $post['tags'] = $tags["$id"];
          $newArray[] = $post;
        }
      }
  
      return $newArray;
  }
  
  public function import()
  {

      $allBlogs = $this->data();

      foreach($allBlogs as $singleBlog)
      {
        $id = $singleBlog['postid'];

        if(!empty($tags["$id"]))
        {
          $singleBlog['tags'] = $tags["$id"];
           // dd($singleTag);
        // echo $singleTag['tags'];
          $t = new Tag();
          $t->fill($singleBlog);
          
          $t->save(); 
        }
      }

        // $b = new Blog();
        // $b->fill($singleBlog);
        // $b->client_id = 1;
        // $b->status_id = 1;
        // $b->site_id = 2;
        // $b->author_id = 1;
        // $b->save(); 

        //$b has the primary key
      
      // foreach($allBlogs as $singleTag){
      //   $currentTag = $singleTag['tags'];
      //   // dd($currentTag);
        
      //   foreach($currentTag as $thisTag){
      //     $t = new Tag();
      //     $t->fill($thisTag);
      //     $t->site_id = 2;
      //     $t->save(); 
      //   }
        // dd($singleTag);
        // echo $singleTag['tags'];
        // $t = new Tag();
        // $t->fill($singleTag);
        // dd($singleTag);
        // $t->site_id = 2;
        // $t->save(); 

        //$b has the primary key
   


      // $saveResult = EloquentModel::create($insertArray);

      // $allBlogs = $this->data();
      // foreach($newArray as $singleTag){
      //   $t = new Tag();
      //   $t->fill($singleTag);
      //   $t->client_id = 1;
      //   $t->name;
      //   dd($singleTag);
      //   // $t->save(); 

        //$b has the primary key
      
    }
}