<?php 

namespace Drupal\devjobs\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Routing;
use Symfony\Component\HttpFoundation;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

use function PHPUnit\Framework\returnSelf;

    class JobsController extends ControllerBase {

      private function calculatePassedTime($nodeCreated) {
        $current_time = time();
        $created_date = $nodeCreated;
        $timestamp = $current_time - $created_date;
        $time = $timestamp;
        if($time > 60){
            $time = floor((int)$time / 60 ) . 'm';
            $tm = rtrim($time,'m');
            if($tm >= 60) {
                $time = floor((int)$time / 60) . 'h';
                $tm = rtrim($time,'h');
                if($tm >= 24) {
                    $time = floor((int)$time / 24) . 'd';
                    $tm = rtrim($time,'d');
                    if($tm >= 7){
                        $time = floor($time / 7) . 'w';
                        $tm = rtrim($time,'w');
                        if($tm >= 5){
                            $time = floor((int)$time / 5) . 'm';
                            $tm = rtrim($time,'w');
                            if($tm >= 12){
                                $time = floor((int)$time / 12) . 'y';
                            }
                        }
                    }
                }
            }
        }
        return $time;
      }

        public function content() {
            //    et query parameters
                $title = \Drupal::request()->request->get('title'); 
                $location = \Drupal::request()->request->get('location'); 
                $checkbox = \Drupal::request()->request->get('box'); 
            //    nodes
                $node_storage= \Drupal::entityTypeManager()->getStorage('node');
            //      filter
            //    dump($checkbox);
                if(strlen($checkbox)===0){
                  $nids = $node_storage->getQuery()
                    ->condition('type', 'Jobs')
                    ->execute();
                }else{
                      $nids = $node_storage->getQuery()
                        ->condition('type', 'Jobs')
            //            ->condition('title.value', $title)
            //            ->condition('field_location.value', $location)
                        ->condition('field_job_name.value', $checkbox)
                        ->execute();
            
                }

                $jobs = [];
                foreach ($nids as $nid){
                  $node = Node::load($nid);
                  $fid = $node-> field_img ->getValue($jobs)[0]['target_id'];

               //   $fid = $node->field_img->getValue()[0]['target_id'];
                   $file = File::load($fid);
                    // Get origin image URI
                    $image_uri = $file -> getFileUri();
                    // Load image style "thumbnail"
                    $style = ImageStyle::load('thumbnail');
                    // Get URL
                    $uri = $style->buildUri($image_uri);

                    $url = $style -> buildUrl($image_uri);


                    //var_dump ($url);
                   // exit;

                    


//$fid =  $node -> field_img -> getValue()[0]['target_id'];
// Load file.
//$file = File::load($fid);
// Get origin image URI.
//$image_uri = $file->getFileUri();
// Load image style "thumbnail".
//$style = ImageStyle::load('thumbnail');
// Get URI.
//$url = $style->buildUrl($image_uri);


                  

                    



                    $created = $node->getCreatedTime();

            
            
      
                    $jobs[$nid] = [
                      'nid' => $nid,
                      'style' => $image_uri,
                      'thumbnail' => $url,
                      'time' => $this->calculatePassedTime((int)$created),
                      'country' => $node->field_country->getValue()[0]['value'],
                      'company' => $node->field_company_name->getValue()[0]['value'],
                      'duration' => $node->field_duration->getValue()[0]['value'],
                      'name' => $node->field_job_name->getValue()[0]['value'],
                      'title' => $node->getTitle(),
                    ];
                    
                  }   
          

    return[
        //'#type' => 'markup',
        //'#markup' => 'hello',
       
        
            // Your theme hook name.
     '#theme' => 'devjobs_theme_hook',

     '#jobs' => $jobs,

      ];
}
}
