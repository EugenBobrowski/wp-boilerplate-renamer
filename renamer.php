<?php
/**
 * Created by PhpStorm. Recursively
 * User: eugen
 * Date: 5/4/15
 * Time: 10:59 PM
 *
 * http://site.loc/wp-content/themes/twentyfifteen/renamer.php
 */

class Renamer {
    /**
     * @var string
     */
    public $search;
    /**
     * @var string
     */
    public $replace;

    /**
     * @var array. Keys are searched. Values are replaced.
     */
    public $replace_array;

    public function __construct() {

    }

    public function generate_replace_array() {
        $replace_array = array(
            $this->search => $this->replace,
        );
        $search_exploded = explode(' ', $this->search);
        $replace_exploded = explode(' ', $this->replace);

        //    'twentyfifteen' => 'twentysomething',
        $replace_array[strtolower(implode('',$search_exploded))] = strtolower(implode('',$replace_exploded));
        //    'twenty-fifteen' => 'twenty-something',
        $replace_array[strtolower(implode('-',$search_exploded))] = strtolower(implode('-',$replace_exploded));
        //    'twenty_fifteen' => 'twenty_something',
        $replace_array[strtolower(implode('_',$search_exploded))] = strtolower(implode('_',$replace_exploded));
        //    'TWENTY_FIFTEEN' => 'TWENTY_SOMETHING',
        $replace_array[strtoupper(implode('_',$search_exploded))] = strtoupper(implode('_',$replace_exploded));

        $this->replace_array = $replace_array;

    }

    /**
     * @param $content string
     * @return string
     */
    public function str_replace_custom ($content) {

        foreach ($this->replace_array as $search=>$replace) {
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    /**
     * @param $path
     */
    public function scanTheDir($path) {
        echo '<ul>';
        $items = scandir($path);
        foreach ($items as $item) {
            $path_item = $path.'/'.$item;
            if ($item == '.' || $item == '..') {
//            echo $item;
            } elseif (realpath($path_item) == __FILE__ ) {
                echo '<li>';
                echo '<em>THIS FILE '.__FILE__.'</em>';
                echo '</li>';
            } elseif (is_dir($path_item)) {
                echo '<li>';
                echo '<strong>'.$item.'</strong>';
                $path_item = $this->renamefiles($path, $item);
                $this->scanTheDir($path_item);
                echo '</li>';
            } else {
                echo '<li>';
                echo '<em>'.$item.'</em>';
                $path_item = $this->renamefiles($path, $item);
                $content = file_get_contents($path_item);
                $new_content = $this->str_replace_custom($content);
                if ($new_content != $content && file_put_contents($path_item, $new_content)) {
                    echo ' [REPLACED] ';
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
    public function renamefiles($path, $item){
        $new_name = $this->str_replace_custom($item);
        if ($new_name != $item && rename($path.'/'.$item, $path.'/'.$new_name)) {
            echo ' >> '.$new_name;
            return $path.'/'.$new_name;
        } else {
            return $path.'/'.$item;
        }
    }

}

$renameObject = new Renamer();

$renameObject->replace_array = array(
    'Twenty Fifteen' => 'Twenty Something',
    'twentyfifteen' => 'twentysomething',
    'twenty-fifteen' => 'twenty-something',
    'twenty_fifteen' => 'twenty_something',
    'TWENTY_FIFTEEN' => 'TWENTY_SOMETHING',
);

/**
 * You can generate this with next three lines. Or add another search-replace items to array to the beginning or to the end, but after generation of replace array.
 */

$renameObject->search = 'Twenty Fifteen';
$renameObject->replace = 'Twenty Something';
$renameObject->generate_replace_array();

/**
 * Run the renaming process
 */

$renameObject->scanTheDir('./');