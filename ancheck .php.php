<?php
include('simple_html_dom.php');
 
// here DOM is accesing data
$html = file_get_html('http://www.bracu.ac.bd/');

// usins a tag fnding different reference
foreach($html->find('a') as $e) 
    echo $e->href . '<br>';

// finding all images
foreach($html->find('img') as $e)
    echo $e->src . '<br>';

// fimd images and teir text
foreach($html->find('img') as $e)
    echo $e->outertext . '<br>';

// using the tag id
foreach($html->find('section-header') as $e)
    echo $e->innertext . '<br>';
?>