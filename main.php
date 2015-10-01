<?php
                 include("simple_html_dom.php");

                 $con=mysql_connect("localhost","root","");
                 mysql_select_db("google",$con);
                 $button=$_GET['submit'];
                 $search=$_GET['Search'];
                 //echo $search;
                 
                 $sql="SELECT urlname FROM url WHERE urlname='$search'";
                 $url=mysql_query($sql);
                 $count=mysql_num_rows($url);
                 if($count==0)
                 {
                  
                     send_data($search);

                     $sql="SELECT urlname FROM url WHERE urlname='$search'";
                    // $sql="SELECT *FROM url";
                     $url=mysql_query($sql);
                     while($rows=mysql_fetch_assoc($url))
                        {

                              
                          $name=$rows['urlname'];
                          
                          echo get_url("$name");
                          //echo $name;
                          
                      }

                 }
                 else
                 {
                    while($rows=mysql_fetch_assoc($url))
                        {

                              
                          $name=$rows['urlname'];
                         echo get_url("$name");
                         //echo $name;                          
                        }
                 

                 }
                 
function get_url($url) 
{
    $ch = curl_init();
     
    if($ch === false)
    {
        die('Failed to create curl object');
    }
     
    $timeout = 30;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function send_data($url1)
{

$crawled_urls=array();
$found_urls=array();
function rel2abs($rel, $base){
 if (parse_url($rel, PHP_URL_SCHEME) != ''){
  return $rel;
 }
 if ($rel[0]=='#' || $rel[0]=='?'){
  return $base.$rel;
 }
 extract(parse_url($base));
 $path = preg_replace('#/[^/]*$#', '', $path);
 if ($rel[0] == '/'){
  $path = '';
 }
 $abs = "$host$path/$rel";
 $re = array('#(/.?/)#', '#/(?!..)[^/]+/../#');
 for($n=1; $n>0;$abs=preg_replace($re,'/', $abs,-1,$n)){}
 $abs=str_replace("../","",$abs);
 return $scheme.'://'.$abs;
}
function perfect_url($u,$b){
 $bp=parse_url($b);
 if(($bp['path']!="/" && $bp['path']!="") || $bp['path']==''){
  if($bp['scheme']==""){
   $scheme="http";
  }else{
   $scheme=$bp['scheme'];
  }
  $b=$scheme."://".$bp['host']."/";
 }
 if(substr($u,0,2)=="//"){
  $u="http:".$u;
 }
 if(substr($u,0,4)!="http"){
  $u=rel2abs($u,$b);
 }
 return $u;
}
function crawl_site($u){
 global $crawled_urls, $found_urls;
 //$con=mysql_connect("localhost","root","");
 //mysql_select_db("google",$con);
 $uen=urlencode($u);
 if((array_key_exists($uen,$crawled_urls)==0 || $crawled_urls[$uen] < date("YmdHis",strtotime('-25 seconds', time())))){
  $html = file_get_html($u);
  $crawled_urls[$uen]=date("YmdHis");
  foreach($html->find("a") as $li){
   $url=perfect_url($li->href,$u);
   $enurl=urlencode($url);
   if($url!='' && substr($url,0,4)!="mail" && substr($url,0,4)!="java" && array_key_exists($enurl,$found_urls)==0){
    $found_urls[$enurl]=1;
    //echo $url."<br/>";
    $sql="INSERT INTO url (urlname) VALUES('$url');";
    if(mysql_query($sql))
    {
        //echo "yes i am here";
    }
    else
    {
        mysql_error();
    }

   }
  }
 }
}
crawl_site("$url1");
}

?>
