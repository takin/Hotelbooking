<?php 
/*variables
$user['email']
$user['id']
$user_comments


Data of one comment in foreach
stdClass Object
(
  [review_id] => 6 
  [wp_comment_id] => 25 
  [firstname] => Aaron 
  [lastname] => Palushaj 
  [user_country] => 
  [user_rating] => 8 
  [user_visited] => 2009-03-01 
  [property_number] => 30462 
  [property_name] => Colombe Hotel 
  [property_city] => Oran 
  [property_country] => Algeria 
  [property_type] => Hotel 
  [comment_ID] => 25 
  [comment_post_ID] => 363 
  [comment_author] => Aaron Palushaj 
  [comment_author_email] => technical@mcwebmanagement.com 
  [comment_author_url] => 
  [comment_author_IP] => 67.71.69.226 
  [comment_date] => 2010-03-03 11:17:25 
  [comment_date_gmt] => 0000-00-00 00:00:00 
  [comment_content] => Cest une future star 
  [comment_karma] => 0 
  [comment_approved] => 0 
  [comment_agent] => Mozilla/5.0 (Windows; U; Windows NT 6.0; fr; rv:1.9.1.8) Gecko/20100202 (BT-canadiens) Firefox/3.5.8 (.NET CLR 3.5.30729) 
  [comment_type] => 
  [comment_parent] => 0 
 
)
*/
?>
<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?> 
</div>
<div id="main" class="grid_12">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _("Vos évaluations");?></h1>
          
		 <?php if(isset($user_comments) && ($user_comments->num_rows()>0)):?>
		 
		 <p><?php printf(gettext("Voici la liste de toutes les évaluations que vous avez fait sur les auberge de jeunesses du site %s."),$this->config->item('site_name'));?></p>
		 <div id="booking-table">		
		 <table cellpadding="0" cellspacing="0">
				 <thead>
				 <tr valign="middle" align="center">
						<th class="title"><?php echo _("Nom de l'établissement");?></th>
						<th><?php echo _('Soumis le');?></th>
						<th><?php echo _('évaluations');?></th>
						<th><?php echo _('Commentaire');?></th>
						<th class="last"><?php echo _('Status');?></th>
													 
				 </tr>
				 </thead>
				 
				 <tbody>
				 
			 <?php foreach ($user_comments->result() as $comment): ?>
			 
					
					<tr>
						<td class="first review"><a href="<?php echo site_url($this->Db_links->get_link("info"))."/".url_title($comment->property_name)."/".$comment->property_number?>"><?php echo $comment->property_name?></a></td>
						<td class="review center"><?php echo date_conv($comment->comment_date,$this->wordpress->get_option('aj_date_format'));?></td>
						<td class="review center"><strong><?php echo (int)($comment->user_rating)*10;?>%</strong></td>
						<td class="review"><?php echo nl2p($comment->comment_content,false,true);?></td>
					 
						<td class="review">
		<?php if ($comment->comment_approved ==0){ echo _("En attente d'approbation");} else { echo _('Publié');}?></td>
					</tr>
					
			 <?php endforeach; ?>
			 </tbody>
		 
		 </table>
		 </div>
		 <?php else: ?>
		 <p><?php echo _("Vous n'avez fait aucune évaluation d'auberge de jeunesse à ce jour.");?></p>
		 
		 <?php endif;?>
		
    </div>
</div>
