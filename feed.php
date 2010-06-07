<?php

$wp_root = dirname(__FILE__) .'/../../../';
if(file_exists($wp_root . 'wp-load.php')) {
        require_once($wp_root . "wp-load.php");
} else if(file_exists($wp_root . 'wp-config.php')) {
        require_once($wp_root . "wp-config.php");
} else {
        exit;
}

//check that the plugin is activated before serving the client
require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
If (!is_plugin_active('downloadfeed/activatefeed.php')) {
    //plugin is not activated, return
    return;
}


$category = $_GET['cat'];


$category = $_GET['cat'];
$limit = $_GET['i'];
$offset = $_GET['n'];

$dl = NULL;
$querystring = 'orderby=hits&order=desc';

if( isset( $category ) ){
   $querystring .= "&category=".$category;
}
if( isset( $limit ) ){
   $querystring .= "&limit=".$limit;
}
if( isset( $offset ) ) {
   $querystring .= "&offset".$offset;
}

$dl = get_downloads($querystring);
if (!empty($dl)) {
   getDownloadFeed($dl); 
}

function getDownloadFeed( $feed )
{
   $doc = new DOMDocument();
   $doc->formatOutput = true;
   
   $r = $doc->createElement( "dowloads" );
   $doc->appendChild( $r );

   foreach($feed as $d) {
       $b = $doc->createElement( "entry" );

       $title = $doc->createElement( "title" );
       $title->appendChild( $doc->createTextNode( $d->title ) );
       $b->appendChild( $title );

       $version = $doc->createElement( "version" );
       $version->appendChild( $doc->createTextNode( $d->version ) );
       $b->appendChild( $version );
      
       $description = $doc->createElement( "description" );
       $description->appendChild( $doc->createTextNode( $d->description ) );
       $b->appendChild( $description );

       $cat = $doc->createElement( "category" );
       $cattitle = $doc->createElement( "title" );
       $cattitle->appendChild( $doc->createTextNode( $d->category ) );
       $cat->appendChild( $cattitle );
       $catid = $doc->createElement( "ID" );
       $catid->appendChild( $doc->createTextNode( $d->category_id ) );
       $cat->appendChild( $catid );
       $b->appendChild( $cat );
       
       $hits = $doc->createElement( "hits" );
       $hits->appendChild( $doc->createTextNode( $d->hits ) );
       $b->appendChild( $hits );

       $thumb = $doc->createElement( "thumbnail" );
       $thumb->appendChild( $doc->createTextNode( $d->image ) );
       $b->appendChild( $thumb );
       
       $link = $doc->createElement( "link" );
       $link->setAttribute("href", $d->url );
       $b->appendChild( $link );
       
       
   
       $r->appendChild($b);    
   }
   
   echo $doc->saveXML();
}
