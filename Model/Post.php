<?php
namespace SuttonSilver\WordpressProductPage\Model;

class Post extends \FishPig\WordPress\Model\Post {

    public function getMainContent(){
        $all = $this->getAllMetaValues();
        $content =  $this->_filter->process($this->getMetaValue('main_content', $this));
        return $content;
    }

    public function clean($content)
    {
        return $this->_filter->clean($content);
    }

}