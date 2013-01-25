<?php $code=$this->wordpress->get_option('aj_lang_code');
			$code=str_replace('-','_',$code);
			if($code=='')$code="en_US";
?>
<div class="widget">
  <div class="gray-block">
    <script src="https://connect.facebook.net/<?php echo $code;?>/all.js#xfbml=1"></script><fb:like show_faces="false" width="280"></fb:like>
  </div>
</div>

