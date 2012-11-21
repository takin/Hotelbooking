<form method="get" id="searchsite" action="<?php bloginfo('url'); ?>/">

<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<input type="submit" id="searchsubmit" value="Go" />
</div><div class="clear"></div>

</form>
