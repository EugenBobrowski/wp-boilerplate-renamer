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

    /**
     * Generate replacement array.
     */
    public function generate_replace_array() {
        $this->search = strtolower($this->search);
        $this->replace = strtolower($this->replace);
        $replace_array = array(
            //    'twenty fifteen' => 'twenty something',
            $this->search => $this->replace,
        );
        $search_exploded_lc = explode(' ', $this->search);
        $replace_exploded_lc = explode(' ', $this->replace);

        $search_exploded_ucfirst = explode(' ', ucwords($this->search));
        $replace_exploded_ucfirst = explode(' ', ucwords($this->replace));



        //    'Twenty Fifteen' => 'Twenty Something',
        $replace_array[implode(' ', $search_exploded_ucfirst)] = implode(' ', $replace_exploded_ucfirst);
        //    'TwentyFifteen' => 'TwentySomething',
        $replace_array[implode('_', $search_exploded_ucfirst)] = implode('_', $replace_exploded_ucfirst);
        //    'Twenty_Fifteen' => 'Twenty_Something',
        $replace_array[implode('_', $search_exploded_ucfirst)] = implode('_', $replace_exploded_ucfirst);
        //    'twentyfifteen' => 'twentysomething',
        $replace_array[implode('',$search_exploded_lc)] = implode('',$replace_exploded_lc);
        //    'twenty-fifteen' => 'twenty-something',
        $replace_array[implode('-',$search_exploded_lc)] = implode('-',$replace_exploded_lc);
        //    'twenty_fifteen' => 'twenty_something',
        $replace_array[implode('_',$search_exploded_lc)] = implode('_',$replace_exploded_lc);
        //    'TWENTY_FIFTEEN' => 'TWENTY_SOMETHING',
        $replace_array[strtoupper(implode('_',$search_exploded_lc))] = strtoupper(implode('_',$replace_exploded_lc));

        $this->add_replacement($replace_array);

    }

    /**
     * @param $replace_array
     */
    public function add_replacement($replace_array) {
        $this->replace_array = array_merge($this->replace_array, $replace_array);
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

$renameObject->add_replacement(array(
    'WordPress Plugin Boilerplate' => 'Events Manager',
    'Events Manager:' => 'Plugin Name:',
));

/**
 * Run the renaming process
 */

$renameObject->scanTheDir('./');