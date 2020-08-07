<?php if(!defined('ABSPATH')) exit;
/**
 * @package Dbmovies Plugin WordPress
 * @author Doothemes (Erick Meza & Brendha Mayuri)
 * @since 1.0
 */


/**
* @since 1.0
* @version 1.0
*/
class DooSimpleDomNode {
    public $nodetype = 3;
    public $tag      = 'text';
    public $attr     = array();
    public $children = array();
    public $nodes    = array();
    public $parent   = null;
    public $_        = array();
    private $dom     = null;

    /**
     * @since 1.0
     */
    function __construct($dom) {
        $this->dom = $dom;
        $dom->nodes[] = $this;
    }

    /**
     * @since 1.0
     */
    function __destruct() {
        $this->clear();
    }

    /**
     * @since 1.0
     */
    function __toString() {
        return $this->outertext();
    }

    /**
     * @since 1.0
     */
    function clear() {
        $this->dom      = null;
        $this->nodes    = null;
        $this->parent   = null;
        $this->children = null;
    }

    /**
     * @since 1.0
     */
    function dump($show_attr=true) {
        dump_html_tree($this, $show_attr);
    }

    /**
     * @since 1.0
     */
    function parent() {
        return $this->parent;
    }

    /**
     * @since 1.0
     */
    function children($idx=-1) {
        if ($idx===-1) return $this->children;
        if (isset($this->children[$idx])) return $this->children[$idx];
        return null;
    }

    /**
     * @since 1.0
     */
    function first_child() {
        if (count($this->children)>0) return $this->children[0];
        return null;
    }

    /**
     * @since 1.0
     */
    function last_child() {
        if (($count=count($this->children))>0) return $this->children[$count-1];
        return null;
    }

    /**
     * @since 1.0
     */
    function next_sibling() {
        if ($this->parent===null) return null;
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx<$count && $this!==$this->parent->children[$idx])
            ++$idx;
        if (++$idx>=$count) return null;
        return $this->parent->children[$idx];
    }

    /**
     * @since 1.0
     */
    function prev_sibling() {
        if ($this->parent===null) return null;
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx<$count && $this!==$this->parent->children[$idx])
            ++$idx;
        if (--$idx<0) return null;
        return $this->parent->children[$idx];
    }

    /**
     * @since 1.0
     */
    function innertext() {
        if (isset($this->_[5])) return $this->_[5];
        if (isset($this->_[4])) return $this->dom->restore_noise($this->_[4]);
        $ret = '';
        foreach($this->nodes as $n)
            $ret .= $n->outertext();
        return $ret;
    }

    /**
     * @since 1.0
     */
    function outertext() {
        if ($this->tag==='root') return $this->innertext();
        if($this->dom->callback!==null)
            call_user_func_array($this->dom->callback, array($this));
        if(isset($this->_[6])) return $this->_[6];
        if(isset($this->_[4])) return $this->dom->restore_noise($this->_[4]);
        $ret = $this->dom->nodes[$this->_[0]]->makeup();
        if (isset($this->_[5]))
            $ret .= $this->_[5];
        else {
            foreach($this->nodes as $n)
                $ret .= $n->outertext();
        }
        if(isset($this->_[1]) && $this->_[1]!=0)
            $ret .= '</'.$this->tag.'>';
        return $ret;
    }

    /**
     * @since 1.0
     */
    function text() {
        if(isset($this->_[5])) return $this->_[5];
        switch($this->nodetype) {
            case '3': return $this->dom->restore_noise($this->_[4]);
            case '2': return '';
            case '6': return '';
        }
        if(strcasecmp($this->tag, 'script')===0) return '';
        if(strcasecmp($this->tag, 'style')===0) return '';
        $ret = '';
        foreach($this->nodes as $n)
        $ret .= $n->text();
        return $ret;
    }

    /**
     * @since 1.0
     */
    function xmltext() {
        $ret = $this->innertext();
        $ret = str_ireplace('<![CDATA[', '', $ret);
        $ret = str_replace(']]>', '', $ret);
        return $ret;
    }

    /**
     * @since 1.0
     */
    function makeup() {
        if (isset($this->_[4])) return $this->dom->restore_noise($this->_[4]);
        $ret = '<'.$this->tag;
        $i = -1;
        foreach($this->attr as $key=>$val) {
            ++$i;
            if ($val===null || $val===false)
                continue;
                $ret .= $this->_[3][$i][0];
                if ($val===true)
                $ret .= $key;
            else {
                switch($this->_[2][$i]) {
                    case '0': $quote = '"'; break;
                    case '1': $quote = '\''; break;
                    default: $quote = '';
                }
                $ret .= $key.$this->_[3][$i][1].'='.$this->_[3][$i][2].$quote.$val.$quote;
            }
        }
        $ret = $this->dom->restore_noise($ret);
        return $ret . $this->_[7] . '>';
    }

    /** @since 1.0 */
    function find($selector, $idx=null) {
        $selectors = $this->parse_selector($selector);
        if (($count=count($selectors))===0) return array();
        $found_keys = array();
        for ($c=0; $c<$count; ++$c) {
            if (($levle=count($selectors[0]))===0) return array();
            if (!isset($this->_[0])) return array();
            $head = array($this->_[0]=>1);
            for ($l=0; $l<$levle; ++$l) {
                $ret = array();
                foreach($head as $k=>$v) {
                    $n = ($k===-1) ? $this->dom->root : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret);
                }
                $head = $ret;
            }
            foreach($head as $k=>$v) {
                if (!isset($found_keys[$k])) $found_keys[$k] = 1;
            }
        }
        ksort($found_keys);
        $found = array();
        foreach($found_keys as $k=>$v)
            $found[] = $this->dom->nodes[$k];
        if (is_null($idx)) return $found;
    	else if ($idx<0) $idx = count($found) + $idx;
        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    /**
     * @since 1.0
     */
    protected function seek($selector, &$ret) {
        list($tag, $key, $val, $exp, $no_key) = $selector;
        if($tag && $key && is_numeric($key)) {
            $count = 0;
            foreach ($this->children as $c) {
                if ($tag==='*' || $tag===$c->tag) {
                    if (++$count==$key) {
                        $ret[$c->_[0]] = 1;
                        return;
                    }
                }
            }
            return;
        }
        $end = (!empty($this->_[1])) ? $this->_[1] : 0;
        if ($end==0) {
            $parent = $this->parent;
            while (!isset($parent->_[1]) && $parent!==null) {
                $end -= 1;
                $parent = $parent->parent;
            }
            $end += $parent->_[1];
        }
        for($i=$this->_[0]+1; $i<$end; ++$i) {
            $node = $this->dom->nodes[$i];
            $pass = true;
            if ($tag==='*' && !$key) {
                if (in_array($node, $this->children, true))
                $ret[$i] = 1;
                continue;
            }
            if($tag && $tag!=$node->tag && $tag!=='*') $pass=false;
            if ($pass && $key) {
                if ($no_key) {
                    if (isset($node->attr[$key])) $pass=false;
                }
                else if (!isset($node->attr[$key])) $pass=false;
            }

            if ($pass && $key && $val  && $val!=='*') {
                $check = $this->match($exp, $val, $node->attr[$key]);
                if (!$check && strcasecmp($key, 'class')===0) {
                    foreach(explode(' ',$node->attr[$key]) as $k) {
                        $check = $this->match($exp, $val, $k);
                        if ($check) break;
                    }
                }
                if (!$check) $pass = false;
            }
            if ($pass) $ret[$i] = 1;
            unset($node);
        }
    }

    /**
     * @since 1.0
     */
    protected function match($exp, $pattern, $value) {
        switch ($exp) {
            case '=':  return ($value===$pattern);
            case '!=': return ($value!==$pattern);
            case '^=': return preg_match("/^".preg_quote($pattern,'/')."/", $value);
            case '$=': return preg_match("/".preg_quote($pattern,'/')."$/", $value);
            case '*=':
                if ($pattern[0]=='/') return preg_match($pattern, $value);
                return preg_match("/".$pattern."/i", $value);
        }
        return false;
    }

    /**
     * @since 1.0
     */
    protected function parse_selector($selector_string) {
        $pattern = "/([\w-:\*]*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[@?(!?[\w-]+)(?:([!*^$]?=)[\"']?(.*?)[\"']?)?\])?([\/, ]+)/is";
        preg_match_all($pattern, trim($selector_string).' ', $matches, PREG_SET_ORDER);
        $selectors = array();
        $result = array();
        foreach ($matches as $m) {
            $m[0] = trim($m[0]);
            if ($m[0]==='' || $m[0]==='/' || $m[0]==='//') continue;
            if ($m[1]==='tbody') continue;
            list($tag, $key, $val, $exp, $no_key) = array($m[1], null, null, '=', false);
            if(!empty($m[2])) {$key='id'; $val=$m[2];}
            if(!empty($m[3])) {$key='class'; $val=$m[3];}
            if(!empty($m[4])) {$key=$m[4];}
            if(!empty($m[5])) {$exp=$m[5];}
            if(!empty($m[6])) {$val=$m[6];}
            if($this->dom->lowercase) {$tag=strtolower($tag); $key=strtolower($key);}
            if(isset($key[0]) && $key[0]==='!') {$key=substr($key, 1); $no_key=true;}
            $result[] = array($tag, $key, $val, $exp, $no_key);
            if (trim($m[7])===',') {
                $selectors[] = $result;
                $result = array();
            }
        }
        if (count($result)>0) $selectors[] = $result;
        return $selectors;
    }

    /**
     * @since 1.0
     */
    function __get($name) {
        if (isset($this->attr[$name])) return $this->attr[$name];
        switch($name){
            case 'outertext': return $this->outertext();
            case 'innertext': return $this->innertext();
            case 'plaintext': return $this->text();
            case 'xmltext': return $this->xmltext();
            default: return array_key_exists($name, $this->attr);
        }
    }

    /**
     * @since 1.0
     */
    function __set($name, $value) {
        switch($name) {
            case 'outertext': return $this->_[6] = $value;
            case 'innertext':
                if (isset($this->_[4])) return $this->_[4] = $value;
                return $this->_[5] = $value;
        }
        if (!isset($this->attr[$name])) {
            $this->_[3][] = array(' ', '', '');
            $this->_[2][] = 0;
        }
        $this->attr[$name] = $value;
    }

    /**
     * @since 1.0
     */
    function __isset($name) {
        switch($name) {
            case 'outertext': return true;
            case 'innertext': return true;
            case 'plaintext': return true;
        }
        return (array_key_exists($name, $this->attr)) ? true : isset($this->attr[$name]);
    }

    /**
     * @since 1.0
     */
    function __unset($name) {
        if (isset($this->attr[$name]))
            unset($this->attr[$name]);
    }

    /**
     * @since 1.0
     */
    function getAllAttributes() {
        return $this->attr;
    }

    /**
     * @since 1.0
     */
    function getAttribute($name) {
        return $this->__get($name);
    }

    /**
     * @since 1.0
     */
    function setAttribute($name, $value) {
        $this->__set($name, $value);
    }

    /**
     * @since 1.0
     */
    function hasAttribute($name) {
        return $this->__isset($name);
    }

    /**
     * @since 1.0
     */
    function removeAttribute($name) {
        $this->__set($name, null);
    }

    /**
     * @since 1.0
     */
    function getElementById($id) {
        return $this->find("#$id", 0);
    }

    /**
     * @since 1.0
     */
    function getElementsById($id, $idx=null) {
        return $this->find("#$id", $idx);
    }

    /**
     * @since 1.0
     */
    function getElementByTagName($name) {
        return $this->find($name, 0);
    }

    /**
     * @since 1.0
     */
    function getElementsByTagName($name, $idx=null) {
        return $this->find($name, $idx);
    }

    /**
     * @since 1.0
     */
    function parentNode() {
        return $this->parent();
    }

    /**
     * @since 1.0
     */
    function childNodes($idx=-1) {
        return $this->children($idx);
    }

    /**
     * @since 1.0
     */
    function firstChild() {
        return $this->first_child();
    }

    /**
     * @since 1.0
     */
    function lastChild() {
        return $this->last_child();
    }

    /**
     * @since 1.0
     */
    function nextSibling() {
        return $this->next_sibling();
    }

    /**
     * @since 1.0
     */
    function previousSibling() {
        return $this->prev_sibling();
    }

}



/**
 * @since 1.0
 * @version 1.0
 */
class DooSimpleDom {
    public $root      = null;
    public $nodes     = array();
    public $callback  = null;
    public $lowercase = false;
    protected $pos;
    protected $doc;
    protected $char;
    protected $size;
    protected $cursor;
    protected $parent;
    protected $noise = array();
    protected $token_blank = " \t\r\n";
    protected $token_equal = ' =/>';
    protected $token_slash = " />\r\n\t";
    protected $token_attr  = ' >';
    protected $self_closing_tags = array('img'=>1, 'br'=>1, 'input'=>1, 'meta'=>1, 'link'=>1, 'hr'=>1, 'base'=>1, 'embed'=>1, 'spacer'=>1);
    protected $block_tags = array('root'=>1, 'body'=>1, 'form'=>1, 'div'=>1, 'span'=>1, 'table'=>1);
    protected $optional_closing_tags = array(
        'tr'=>array('tr'=>1, 'td'=>1, 'th'=>1),
        'th'=>array('th'=>1),
        'td'=>array('td'=>1),
        'li'=>array('li'=>1),
        'dt'=>array('dt'=>1, 'dd'=>1),
        'dd'=>array('dd'=>1, 'dt'=>1),
        'dl'=>array('dd'=>1, 'dt'=>1),
        'p'=>array('p'=>1),
        'nobr'=>array('nobr'=>1),
    );

    /**
     * @since 1.0
     */
    function __construct($str=null) {
        if ($str) {
            if (preg_match("/^http:\/\//i",$str) || is_file($str))
                $this->load_file($str);
            else
                $this->load($str);
        }
    }

    /**
     * @since 1.0
     */
    function __destruct() {
        $this->clear();
    }

    /**
     * @since 1.0
     */
    function load($str, $lowercase=true) {
        $this->prepare($str, $lowercase);
        $this->remove_noise("'<!--(.*?)-->'is");
        $this->remove_noise("'<!\[CDATA\[(.*?)\]\]>'is", true);
        $this->remove_noise("'<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>'is");
        $this->remove_noise("'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is");
        $this->remove_noise("'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is");
        $this->remove_noise("'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is");
        $this->remove_noise("'<\s*(?:code)[^>]*>(.*?)<\s*/\s*(?:code)\s*>'is");
        $this->remove_noise("'(<\?)(.*?)(\?>)'s", true);
        $this->remove_noise("'(\{\w)(.*?)(\})'s", true);
        while ($this->parse());
        $this->root->_[1] = $this->cursor;
    }

    /**
     * @since 1.0
     */
    function load_file() {
        $args = func_get_args();
        $this->load(call_user_func_array('file_get_contents', $args), true);
    }

    /**
     * @since 1.0
     */
    function set_callback($function_name) {
        $this->callback = $function_name;
    }

    /**
     * @since 1.0
     */
    function remove_callback() {
        $this->callback = null;
    }

    /**
     * @since 1.0
     */
    function save($filepath='') {
        $ret = $this->root->innertext();
        if ($filepath!=='') file_put_contents($filepath, $ret);
        return $ret;
    }

    /**
     * @since 1.0
     */
    function find($selector, $idx=null) {
        return $this->root->find($selector, $idx);
    }

    /**
     * @since 1.0
     */
    function clear() {
        foreach($this->nodes as $n) {
            $n->clear(); $n = null;
        }
        if (isset($this->parent)) {$this->parent->clear(); unset($this->parent);}
        if (isset($this->root)) {$this->root->clear(); unset($this->root);}
        unset($this->doc);
        unset($this->noise);
    }

    /**
     * @since 1.0
     */
    function dump($show_attr=true) {
        $this->root->dump($show_attr);
    }

    /**
     * @since 1.0
     */
    protected function prepare($str, $lowercase=true) {
        $this->clear();
        $this->doc = $str;
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = array();
        $this->nodes = array();
        $this->lowercase = $lowercase;
        $this->root = new DooSimpleDomNode($this);
        $this->root->tag = 'root';
        $this->root->_[0] = -1;
        $this->root->nodetype = 5;
        $this->parent = $this->root;
        $this->size = strlen($str);
        if ($this->size>0) $this->char = $this->doc[0];
    }

    /**
     * @since 1.0
     */
    protected function parse() {
        if (($s = $this->copy_until_char('<'))==='')
            return $this->read_tag();
        $node = new DooSimpleDomNode($this);
        ++$this->cursor;
        $node->_[4] = $s;
        $this->link_nodes($node, false);
        return true;
    }

    /**
     * @since 1.0
     */
    protected function read_tag() {
        if ($this->char!=='<') {
            $this->root->_[1] = $this->cursor;
            return false;
        }
        $begin_tag_pos = $this->pos;
        $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
        if ($this->char==='/') {
            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
            $this->skip($this->token_blank_t);
            $tag = $this->copy_until_char('>');
            if (($pos = strpos($tag, ' '))!==false)
                $tag = substr($tag, 0, $pos);
            $parent_lower = strtolower($this->parent->tag);
            $tag_lower = strtolower($tag);
            if($parent_lower!==$tag_lower) {
                if(isset($this->optional_closing_tags[$parent_lower]) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[1] = 0;
                    $org_parent = $this->parent;
                    while (($this->parent->parent) && strtolower($this->parent->tag)!==$tag_lower)
                        $this->parent = $this->parent->parent;
                    if(strtolower($this->parent->tag)!==$tag_lower) {
                        $this->parent = $org_parent;
                        if ($this->parent->parent) $this->parent = $this->parent->parent;
                        $this->parent->_[1] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                }
                else if (($this->parent->parent) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[1] = 0;
                    $org_parent = $this->parent;
                    while (($this->parent->parent) && strtolower($this->parent->tag)!==$tag_lower)
                        $this->parent = $this->parent->parent;
                    if(strtolower($this->parent->tag)!==$tag_lower) {
                        $this->parent = $org_parent;
                        $this->parent->_[1] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                }
                else if (($this->parent->parent) && strtolower($this->parent->parent->tag)===$tag_lower) {
                    $this->parent->_[1] = 0;
                    $this->parent = $this->parent->parent;
                }
                else
                    return $this->as_text_node($tag);
            }
            $this->parent->_[1] = $this->cursor;
            if ($this->parent->parent) $this->parent = $this->parent->parent;
            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
            return true;
        }

        $node = new DooSimpleDomNode($this);
        $node->_[0] = $this->cursor;
        ++$this->cursor;
        $tag = $this->copy_until($this->token_slash);
        if(isset($tag[0]) && $tag[0]==='!') {
            $node->_[4] = '<' . $tag . $this->copy_until_char('>');
            if(isset($tag[2]) && $tag[1]==='-' && $tag[2]==='-') {
                $node->nodetype = 2;
                $node->tag = 'comment';
            } else {
                $node->nodetype = 6;
                $node->tag = 'unknown';
            }
            if($this->char==='>') $node->_[4].='>';
            $this->link_nodes($node, true);
            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
            return true;
        }
        if($pos=strpos($tag, '<')!==false) {
            $tag = '<' . substr($tag, 0, -1);
            $node->_[4] = $tag;
            $this->link_nodes($node, false);
            $this->char = $this->doc[--$this->pos];
            return true;
        }
        if(!preg_match("/^[\w-:]+$/", $tag)) {
            $node->_[4] = '<' . $tag . $this->copy_until('<>');
            if ($this->char==='<') {
                $this->link_nodes($node, false);
                return true;
            }
            if($this->char==='>') $node->_[4].='>';
            $this->link_nodes($node, false);
            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
            return true;
        }
        $node->nodetype = 1;
        $tag_lower = strtolower($tag);
        $node->tag = ($this->lowercase) ? $tag_lower : $tag;
        if (isset($this->optional_closing_tags[$tag_lower]) ) {
            while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
                $this->parent->_[1] = 0;
                $this->parent = $this->parent->parent;
            }
            $node->parent = $this->parent;
        }
        $guard = 0;
        $space = array($this->copy_skip($this->token_blank), '', '');
        do {
            if ($this->char!==null && $space[0]==='') break;
            $name = $this->copy_until($this->token_equal);
            if($guard===$this->pos) {
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
                continue;
            }
            $guard = $this->pos;
            if($this->pos>=$this->size-1 && $this->char!=='>') {
                $node->nodetype = 3;
                $node->_[1] = 0;
                $node->_[4] = '<'.$tag . $space[0] . $name;
                $node->tag = 'text';
                $this->link_nodes($node, false);
                return true;
            }
            if($this->doc[$this->pos-1]=='<') {
                $node->nodetype = 3;
                $node->tag = 'text';
                $node->attr = array();
                $node->_[1] = 0;
                $node->_[4] = substr($this->doc, $begin_tag_pos, $this->pos-$begin_tag_pos-1);
                $this->pos -= 2;
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
                $this->link_nodes($node, false);
                return true;
            }
            if($name!=='/' && $name!=='') {
                $space[1] = $this->copy_skip($this->token_blank);
                $name = $this->restore_noise($name);
                if ($this->lowercase) $name = strtolower($name);
                if ($this->char==='=') {
                    $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
                    $this->parse_attr($node, $name, $space);
                } else {
                    $node->_[2][] = 3;
                    $node->attr[$name] = true;
                    if ($this->char!='>') $this->char = $this->doc[--$this->pos];
                }
                $node->_[3][] = $space;
                $space = array($this->copy_skip($this->token_blank), '', '');
            }
            else
                break;
        } while($this->char!=='>' && $this->char!=='/');
        $this->link_nodes($node, true);
        $node->_[7] = $space[0];
        if ($this->copy_until_char_escape('>')==='/') {
            $node->_[7] .= '/';
            $node->_[1] = 0;
        } else {
            if(!isset($this->self_closing_tags[strtolower($node->tag)])) $this->parent = $node;
        }
        $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
        return true;
    }

    /**
     * @since 1.0
     */
    protected function parse_attr($node, $name, &$space) {
        $space[2] = $this->copy_skip($this->token_blank);
        switch($this->char) {
            case '"':
                $node->_[2][] = 0;
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
                $node->attr[$name] = $this->restore_noise($this->copy_until_char_escape('"'));
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
                break;
            case '\'':
                $node->_[2][] = 1;
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
                $node->attr[$name] = $this->restore_noise($this->copy_until_char_escape('\''));
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
                break;
            default:
                $node->_[2][] = 3;
                $node->attr[$name] = $this->restore_noise($this->copy_until($this->token_attr));
        }
    }

    /**
     * @since 1.0
     */
    protected function link_nodes(&$node, $is_child) {
        $node->parent = $this->parent;
        $this->parent->nodes[] = $node;
        if ($is_child)
            $this->parent->children[] = $node;
    }

    /**
     * @since 1.0
     */
    protected function as_text_node($tag) {
        $node = new DooSimpleDomNode($this);
        ++$this->cursor;
        $node->_[4] = '</' . $tag . '>';
        $this->link_nodes($node, false);
        $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null;
        return true;
    }

    /**
     * @since 1.0
     */
    protected function skip($chars) {
        $this->pos += strspn($this->doc, $chars, $this->pos);
        $this->char = ($this->pos<$this->size) ? $this->doc[$this->pos] : null;
    }

    /**
     * @since 1.0
     */
    protected function copy_skip($chars) {
        $pos = $this->pos;
        $len = strspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = ($this->pos<$this->size) ? $this->doc[$this->pos] : null;
        if ($len===0) return '';
        return substr($this->doc, $pos, $len);
    }

    /**
     * @since 1.0
     */
    protected function copy_until($chars) {
        $pos = $this->pos;
        $len = strcspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = ($this->pos<$this->size) ? $this->doc[$this->pos] : null;
        return substr($this->doc, $pos, $len);
    }

    /**
     * @since 1.0
     */
    protected function copy_until_char($char) {
        if ($this->char===null) return '';
        if (($pos = strpos($this->doc, $char, $this->pos))===false) {
            $ret = substr($this->doc, $this->pos, $this->size-$this->pos);
            $this->char = null;
            $this->pos = $this->size;
            return $ret;
        }
        if ($pos===$this->pos) return '';
        $pos_old = $this->pos;
        $this->char = $this->doc[$pos];
        $this->pos = $pos;
        return substr($this->doc, $pos_old, $pos-$pos_old);
    }

    /**
     * @since 1.0
     */
    protected function copy_until_char_escape($char) {
        if ($this->char===null) return '';
        $start = $this->pos;
        while(1) {
            if (($pos = strpos($this->doc, $char, $start))===false) {
                $ret = substr($this->doc, $this->pos, $this->size-$this->pos);
                $this->char = null;
                $this->pos = $this->size;
                return $ret;
            }

            if ($pos===$this->pos) return '';

            if ($this->doc[$pos-1]==='\\') {
                $start = $pos+1;
                continue;
            }

            $pos_old = $this->pos;
            $this->char = $this->doc[$pos];
            $this->pos = $pos;
            return substr($this->doc, $pos_old, $pos-$pos_old);
        }
    }

    /**
     * @since 1.0
     */
    protected function remove_noise($pattern, $remove_tag=false) {
        $count = preg_match_all($pattern, $this->doc, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
        for($i=$count-1; $i>-1; --$i) {
            $key = '___noise___'.sprintf('% 3d', count($this->noise)+100);
            $idx = ($remove_tag) ? 0 : 1;
            $this->noise[$key] = $matches[$i][$idx][0];
            $this->doc = substr_replace($this->doc, $key, $matches[$i][$idx][1], strlen($matches[$i][$idx][0]));
        }
        $this->size = strlen($this->doc);
        if ($this->size>0) $this->char = $this->doc[0];
    }

    /**
     * @since 1.0
     */
    function restore_noise($text) {
        while(($pos=strpos($text, '___noise___'))!==false) {
            $key = '___noise___'.$text[$pos+11].$text[$pos+12].$text[$pos+13];
            if (isset($this->noise[$key]))
            $text = substr($text, 0, $pos).$this->noise[$key].substr($text, $pos+14);
        }
        return $text;
    }

    /**
     * @since 1.0
     */
    function __toString() {
        return $this->root->innertext();
    }

    /**
     * @since 1.0
     */
    function __get($name) {
        switch($name) {
             case 'outertext': return $this->root->innertext();
             case 'innertext': return $this->root->innertext();
             case 'plaintext': return $this->root->text();
        }
    }

    /**
     * @since 1.0
     */
    function childNodes($idx=-1) {
        return $this->root->childNodes($idx);
    }

    /**
     * @since 1.0
     */
    function firstChild() {
        return $this->root->first_child();
    }

    /**
     * @since 1.0
     */
    function lastChild() {
        return $this->root->last_child();
    }

    /**
     * @since 1.0
     */
    function getElementById($id) {
        return $this->find("#$id", 0);
    }

    /**
     * @since 1.0
     */
    function getElementsById($id, $idx=null) {
        return $this->find("#$id", $idx);
    }

    /**
     * @since 1.0
     */
    function getElementByTagName($name) {
        return $this->find($name, 0);
    }

    /**
     * @since 1.0
     */
    function getElementsByTagName($name, $idx=-1) {
        return $this->find($name, $idx);
    }

    /**
     * @since 1.0
     */
    function loadFile() {
        $args = func_get_args();
        $this->load(call_user_func_array('file_get_contents', $args), true);
    }

}
