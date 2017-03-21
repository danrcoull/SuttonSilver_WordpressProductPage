<?php
namespace SuttonSilver\WordpressProductPage\Model;

class Post extends \FishPig\WordPress\Model\Post {

    public function getMainContent(){
        $content =  $this->_filter->process($this->getMetaValue('main_content', $this));
        return $content;
    }

    public function clean($content)
    {
        return $this->_filter->clean($content);
    }

}