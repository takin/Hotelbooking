<?php // Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
		die ( __('Veuillez ne pas charger cette page directement. Merci.','auberge') );
	}
	
	if ( post_password_required() ) {
?>
<?php
		return;
	}

	// Show the comments
	if ( have_comments() ) {
?>

 <h2 id="comments" class="green-bar">
  <?php comments_number('0', '1', '%' );?>
  <?php _e('Commentaires','auberge'); ?>
  <a href="#respond" title="<?php _e('Laisser un commentaire','auberge'); ?>">&raquo;</a></h2>
<div class="comment-section">
    <ol class="commentlist" id="singlecomments">
      <?php wp_list_comments('type=comment&callback=mytheme_comment'); ?>
    </ol>
    <div id="pagination">
      <div id="older">
        <?php previous_comments_link(__('Commentaires Précédents','auberge')); ?>
      </div>
      <div id="newer">
        <?php next_comments_link(__('Commentaires Suivants','auberge')); ?>
      </div>
    </div>
</div>
<?php
		} else {
			// this is displayed if there are no comments so far
			if ('open' == $post->comment_status) {
			} else {
				if(!is_page()) {
?>
<div class="comment-section">
<p class="nocomments">
  <?php _e('Les commentaires sont fermés','auberge'); ?>
</p>
</div>
<?php
				}
			}
		}
	
		if ('open' == $post-> comment_status) {
?>
<?php
		// Begin Trackbacks 
		foreach ($comments as $comment) {
			if ($comment->comment_type == "trackback" || $comment->comment_type == "pingback" || ereg("<pingback />", $comment->comment_content) || ereg("<trackback />", $comment->comment_content)) {
				if (!$runonce) { $runonce = true;
?>
<h2 id="trackbacks" class="dark-bar">
  <?php _e('Articles en lien','auberge'); ?>  
</h2>
<div class="comment-section">
<ol id="trackbacklist">
  <?php
				}
?>
  <li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>"> <cite>
    <?php comment_author_link() ?>
    </cite> </li>
  <?php
			}
		}
		if ($runonce) {
?>
</ol>
</div>
<?php
		}
		// End Trackbacks
?>

  <h2 class="green-bar">
    <?php _e('Laisser une réponse ou un autre recommandation pour cette ville','auberge'); ?>
  </h2>
  <div id="respond">
  <div class="comment-section">
  <p id="cancel-comment-reply">
    <?php cancel_comment_reply_link(__('Annuler la réponse','auberge')); ?>
  </p>
  <?php
		if ( get_option('comment_registration') && !$user_ID ) {
			_e('Vous devez','auberge');
?>
  <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">
  <?php _e('vous connecter','auberge'); ?>
  </a>
  <?php	_e('pour publier un commentaire.','auberge');
		} else {
?>
  <ol class="comment-form-list">
  <li>
  
  <div class="comment-author vcard">
            		
      <img height="60" width="60" class="avatar avatar-60 photo" src="<?php bloginfo('template_url');?>/images/gravatar.jpg" alt=""/>				
      <div class="commentmetadata">
          <cite class="fn"><?php	_e('Votre Nom','auberge');?></cite>					
          <div class="comment-date">
          <?php echo strftime("%e %B",get_the_time('U')); ?>
          </div>
      </div>
  </div>
  <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
    <?php
			if ( $user_ID ) {
?>
    
      <p>
      <textarea name="comment" class="field" id="comment" cols="10" rows="10" tabindex="4"></textarea>
      
    </p>
    <p>
      <?php _e('Connecté entant que','auberge'); ?>
      <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a> &bull; <a href="<?php echo wp_logout_url($redirect); ?>">
      <?php _e('Se déconnecter','auberge'); ?>
      &raquo;</a> </p>
    <?php
			} else {
?>
    <p>
      <textarea name="comment" class="field" id="comment" cols="10" rows="10" tabindex="4"></textarea>
       
    </p>
    <p>
      <input class="field" type="text" name="author" id="author" value="<?php echo $comment_author; ?>" tabindex="1" />
      <label for="author">
      <?php _e('Nom','auberge'); ?>
      <?php if ($req) { ?>
      <span class="required">
      <?php _e('(requis)','auberge'); ?>
      </span>
      <?php } ?>
      </label>
    </p>
    <p>
      <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="2" class="field" />
      <label for="email">
      <?php _e('Email (ne sera pas publé)','auberge'); ?>
      <?php if ($req) { ?>
      <span class="required">
      <?php _e('(requis)','auberge'); ?>
      </span>
      <?php } ?>
      </label>
    </p>
    <p>
      <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" tabindex="3" class="field" />
      <label for="url">
      <?php _e('Site Web','auberge'); ?>
      </label>
    </p>
    <?php
		 	}
			comment_id_fields();
?>
    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" />
    
    <?php
			if (get_option("comment_moderation") == "1") {
?>
    <p>
      <?php _e('Veuillez noter: la modération est active et ralentira la publication de votre commentaire. Nul besoin de soumettre à nouveau votre commentaire.','auberge'); ?>
    </p>
    <?php
			}
?>
    <p>
      <button name="submit" type="submit" id="submit" class="button"><?php _e('Soumettre','auberge'); ?></button>
    </p>
    <?php
			do_action('comment_form', $post->ID);
?>
  </form>
  </li>
  </ol>
</div>
</div>
<?php } } ?>
