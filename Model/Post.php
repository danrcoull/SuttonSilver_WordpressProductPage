<?php
namespace SuttonSilver\WordpressProductPage\Model;

class Post extends \FishPig\WordPress\Model\Post {

    public function getMainContent(){
        $all = $this->getAllMetaValues();
        return $this->_filter->process($this->getMetaValue('main_content', $this));
    }

    public function clean($content)
    {
        return $this->_filter->clean($content);
    }

}