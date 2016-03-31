<?php

/**
 * Created by PhpStorm. Recursively
 * User: eugen
 * Date: 5/4/15
 * Time: 10:59 PM
 *
 * http://site.loc/wp-content/themes/twentyfifteen/renamer.php
 */
class Renamer
{
    /**
     * @var string
     */
    public $search;
    /**
     * @var string
     */
    public $replace;
    /**
     * @var array. Which files or dirs must be ignored by Renamer. Add the relative path with path which you insert in scanTheDir() method. Ex. ./ + / + .git = .//.git
     */
    public $ignore = array();
    /**
     * @var array. Keys are searched. Values are replaced.
     */
    public $replace_array;

    public function __construct()
    {

    }

    public function addIgnore($file)
    {
        $this->ignore[] = $file;
    }

    public function generate_replace_array()
    {
        $replace_array = array(
            $this->search => $this->replace,
        );
        $search_exploded = explode(' ', $this->search);
        $replace_exploded = explode(' ', $this->replace);

        //    'twentyfifteen' => 'twentysomething',
        $replace_array[strtolower(implode('', $search_exploded))] = strtolower(implode('', $replace_exploded));
        //    'twenty-fifteen' => 'twenty-something',
        $replace_array[strtolower(implode('-', $search_exploded))] = strtolower(implode('-', $replace_exploded));
        //    'twenty_fifteen' => 'twenty_something',
        $replace_array[strtolower(implode('_', $search_exploded))] = strtolower(implode('_', $replace_exploded));
        //    'TWENTY_FIFTEEN' => 'TWENTY_SOMETHING',
        $replace_array[strtoupper(implode('_', $search_exploded))] = strtoupper(implode('_', $replace_exploded));

        $this->replace_array = $replace_array;

    }

    /**
     * @param $path
     */
    public function scanTheDir($path)
    {
        echo '<ul>';
        $items = scandir($path);
        foreach ($items as $item) {
            $path_item = $path . '/' . $item;
            if ($item == '.' || $item == '..') {
//            echo $item;
            } elseif (realpath($path_item) == __FILE__) {
                echo '<li>';
                echo '<em>' . __FILE__ . '</em> <span class="label label-primary">This file</span>';
                echo '</li>';
            } elseif (in_array($path_item, $this->ignore)) {
                echo '<li>';
                echo '<em>' . $item . '</em>';
                echo ' <span class="label label-default">Ignored</span> ';
                echo '</li>';
            } elseif (is_dir($path_item)) {
                echo '<li>';
                echo '<strong>' . $item . '</strong>';
                $path_item = $this->renamefiles($path, $item);
                $this->scanTheDir($path_item);
                echo '</li>';
            } else {
                echo '<li>';
                echo '<em>' . $item . '</em>';
                $path_item = $this->renamefiles($path, $item);
                $content = file_get_contents($path_item);
                $new_content = $this->str_replace_custom($content);
                if ($new_content != $content && file_put_contents($path_item, $new_content)) {
                    echo ' <span class="label label-warning">Replaced</span> ';
                }
                echo '</li>';
            }
        }
        echo '</ul>';
    }

    /**
     * @param $path
     * @param $item
     * @return string
     */
    public function renamefiles($path, $item)
    {
        $new_name = $this->str_replace_custom($item);
        if ($new_name != $item && rename($path . '/' . $item, $path . '/' . $new_name)) {
            echo ' <span class="glyphicon glyphicon-menu-right"></span>  ' . $new_name;
            echo ' <span class="label label-danger">Renamed</span> ';
            return $path . '/' . $new_name;
        } else {
            return $path . '/' . $item;
        }
    }

    /**
     * @param $content string
     * @return string
     */
    public function str_replace_custom($content)
    {

        foreach ($this->replace_array as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Boilerplate Renamer</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
          integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body style="background: #473E48; padding: 30px 0;">
<div class="container" style="background: #ffffff; border-radius: 2px; padding-top: 15px; padding-bottom: 15px; ">
    <div class="page-header">
        <h1>Boilerplate Renamer</h1>
    </div>
    <?php

    $renameObject = new Renamer();

    /**
     * You can generate this with next three lines. Or add another search-replace items to array to the beginning or to the end, but after generation of replace array.
     */

    $renameObject->search = 'Twenty Fifteen';
    $renameObject->replace = 'Twenty Something';
    $renameObject->generate_replace_array();
    $renameObject->replace_array = array_merge($renameObject->replace_array, array(
        $renameObject->replace . ':' => 'Plugin Name:', //Fix plugin description replacement
        'Your Name or Your Company' => 'Your Name or Your Company',
        'http://example.com/' => 'http://example.com/',
        'WordPress Plugin Boilerplate' => 'WordPress Plugin Boilerplate'
    ));
    $renameObject->addIgnore('.//.git');

    /**
     * Run the renaming process
     */

    $renameObject->scanTheDir('./');


    ?>
</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
</body>
</html>
