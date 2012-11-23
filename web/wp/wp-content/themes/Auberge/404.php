<?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="main" class="grid_12">
	<div class="box_content box_round group">

        <h1 class="content_title"><?php _e('Erreur 404 - Page introuvable','auberge');?></h1>
        
        <div class="entry">
        
    		<p><?php _e("Désolé nous ne pouvons trouver cette page. Veuillez utiliser le menu du haut pour naviguer sur le site et trouver l'information désiré. Vous pouvez aussi effectuer une recherche:",'auberge');?></p>
            <?php include (TEMPLATEPATH . '/searchform.php'); ?>
				</div>
        
   </div>

</div>

<?php get_footer(); ?>
