<?php /*

Template Name: Page Contact

*/?><?php get_header(); ?>
<?php
if($GLOBALS['print']=='mobile'){
include( TEMPLATEPATH . '/page-mobile.php' );
}else{?>
<?php get_sidebar(); ?>
<div id="main" class="grid_12">
	
	<div class="box_content box_round group">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    
    <h1 class="border_title"><?php the_title();?></h1>
      <div class="entry faq">
				<?php the_content();?>
        <?php $perma = get_permalink();?>
      </div>  
		<?php endwhile; endif; ?>
		<div class="contact-form box_round">
      <form action="" method="post" class="cform" id="sendEmail">
      
      <ul>
          <li><label for="name"><span id="name1"><?php _e('Nom','auberge');?></span></label><input class="small-text" name="name" id="name" type="text" /></li>
          <li><label for="email"><span id="email1"><?php _e('Email','auberge');?></span></label><input class="small-text" name="email" id="email" type="text" /></li>
          <li><label for="subject"><span id="subject1"><?php _e('Sujet','auberge');?></span></label><input class="small-text" name="subject" id="subject" type="text" /></li>
          <li><label for="numreserv"><span id="subject1"><?php _e('Numéro de réservation','auberge');?></span></label><input class="small-text" name="numreserv" id="numreserv" type="text" /></li>
          <li><label for="message"><span id="message1"><?php _e('Message','auberge');?></span></label><textarea name="message" id="message" class="large-text" rows="10" cols="30"></textarea></li>
          <li class="group"><label class="verif" for="verif"><span id="verif1"><?php _e('Sécurité: combien font 7 plus 2?','auberge');?></span></label><input class="very-small-text" name="verif" id="verif" type="text" /></li>
          <li class="buttons">
					<button type="submit" id="sendemail-submit" class="box_round button-orange side_submit hoverit" onfocus="this.blur()"><?php _e('Submit','auberge');?></button>
          <input type="hidden" name="submitted" id="submitted" value="true"/>
          <input type="hidden" name="adminemail" id="adminemail" value="<?php bloginfo('admin_email') ?>" />
          <input type="hidden" name="templateurl" id="templateurl" value="<?php bloginfo('template_url') ?>" />
          <input type="hidden" name="permalink" id="permalink" value="<?php echo $perma; ?>" />
          <input type="hidden" name="sitename" id="sitename" value="<?php bloginfo('name') ?>" />
          <input type="hidden" name="validation" id="validation" value="<?php _e('Merci pour votre commentaire. Nous vous répondrons d\'ici peu.','auberge');?>" />
          <input type="hidden" name="errorstring" id="errorstring" value="<?php _e('Veuillez remplir les champs requis correctement.','auberge');?>" />
          
          
          </li>
      </ul>
      <div style="clear:both;"></div>
      </form>
      
    </div>
          
  </div>   
</div>

<?php /* if(isset($_POST['submitted'])) {
	include(TEMPLATEPATH .'/sendemail.php');
}
*/ ?>


<?php }?>
<?php get_footer(); ?>