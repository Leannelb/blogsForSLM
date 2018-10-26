<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use XMLReader;
use DOMDocument;

class Importcontroller extends Controller{
      protected function data()
      {
          $doc = new DOMDocument;
          $xml = new XMLReader;
          $mainArray = [];
          $tags = [];
          $postid = 0;
          $xml->open('/Users/leanne/Sites/php-script/wordpress.xml');  
        
          /**
           * stole this idea from : https://stackoverflow.com/questions/1835177/how-to-use-xmlreader-in-php
           */
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
      
          if (!$xml->open('/Users/leanne/Sites/php-script/wordpress.xml')) 
          {
              die("Failed to open 'wordpress.xml");
          }
          while($xml->read()) 
          {
              if ($xml->nodeType == XMLReader::ELEMENT && $xml->localName == 'postid') 
              {

                $node = simplexml_import_dom($doc->importNode($xml->expand(), true));
                $postid = ((array) $node)[0];
                // dd($postid);
              }
              if ($xml->nodeType == XMLReader::ELEMENT && $xml->name == 'category') 
              {
                  $tags[$postid][] = $xml->getAttribute('nicename');
              }
          }
      
          $newArray = array();
          foreach($mainArray as $post)
          {
              $id = $post['postid'];
              if(!empty($tags["$id"])){
                $post['tags'] = $tags["$id"];
                $newArray[] = $post;
              }
          }
          return $newArray;
      }

      /**
       * selecting tags taht contain ids from post request
       * extrac
       */
      protected function example(array $mainArray = [], array $post = [])
      {
        $newArray = array();
        foreach($mainArray as $post)
        {
            $id = $post['id'];
            if(!empty($tags["$id"])){
              $post['tags'] = $tags["$id"];
              $newArray[] = $post;
            }
        }
    
        return $newArray;
      }

      protected function handleTag(string $tag)
      {
        $instance = Tag::where('name', trim($tag))->first();
        if(null === $instance){
            return Tag::create(['name' => $tag]);
        }
        return $instance;
       
      }

      public function import()
      {
          $allBlogs = $this->data();

          foreach($allBlogs as $singleBlog)
          {
            $id = $singleBlog['postid'];
 
            // $blog = Blog::where("postid",12)->first();
            // dd($blog);
           $b = Blog::updateOrCreate(
                ['postid' => $id], 
            [
                'title'=>$singleBlog['title'],
                'content'=>$singleBlog['content'],
                'slug'=>$singleBlog['slug'],
                'published_at' =>$singleBlog['published_at'],
                'client_id' => 1, 
                'status_id' => 1, 
                'site_id'=> 2, 
                'author_id' => 1, 
                
            ]);
        
            if(!empty($id))
            {
                $tagIds = array();
              foreach($singleBlog["tags"] as $singleTag)
              {
                $tag = $this->handleTag($singleTag);
                array_push($tagIds, $tag->id);
              }
              $b->tags()->sync($tagIds);

            }
        }

         // TO PRINT OUT A SPECIFIC BLOG POST DO THE BELOW: : 
        //  $blog = Blog::where("postid",4833)->first();
        //  dd($blog);
        //  $blog = Blog::find(1);

     // TO PRINT OUT THE TAGS IN AN ARRAY: I.E. SEE WHAT TAGS ARE THERE: 
        //  $blog = Blog::where("postid",4833)->first();
        //  $tags = $blog->tags()->get();
        //  dump($tags);
        //  foreach($tags as $tag){
        //      dump($tag['name']);
        //  }
        


    }

}