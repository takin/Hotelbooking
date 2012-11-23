<?php
//Data
/*
$emaildata["commentID"]
$emaildata["email"]
$emaildata["firstname"]
$emaildata["lastname"]
$emaildata["property_number"]
$emaildata["property_name"]
$emaildata["property_city"]
$emaildata["property_country"]
$emaildata["property_type"]
$emaildata["comment"]
$emaildata["rating"]
$emaildata["author_ip"]
*/
?>
<html>
<body>
<div align="left">
<table>
<tr><td>A new comment on property "<?php echo $property_name;?>" need your approval</td></tr>
<tr><td><?php echo anchor(site_url($this->Db_links->get_link("info")."/$property_name/$property_number"));?></td></tr>
<tr><td><br /></td></tr>
<tr><td>Author : <?php echo $firstname." ". $lastname; ?> (IP : <?php echo $author_ip; ?>)</td></tr>
<tr><td>E-mail : <?php echo $email; ?></td></tr>
<tr><td>Nationality : <?php echo $nationality; ?></td></tr>
<?php /*?><tr><td>Whois  : <?php echo anchor("http://ws.arin.net/cgi-bin/whois.pl?queryinput=". $author_ip); ?></td></tr><?php */?>
<tr><td>Note   : <?php echo $rating; ?></td></tr>
<tr><td>Comment :</td></tr>
<tr><td><?php echo $comment; ?></td></tr>
<tr><td><br /></td></tr>
<tr><td>Approve : <?php echo anchor($this->wordpress->get_option('aj_comment_url')."/wp-admin/comment.php?action=approve&c=".$commentID); ?></td></tr>
<?php /*?><tr><td>Mettre à la corbeille&nbsp;: <?php echo anchor($this->config->item('wp_base_url')."/wp-admin/comment.php?action=trash&c=".$commentID); ?></td></tr>
<tr><td>Le marquer comme indésirable : <?php echo anchor($this->config->item('wp_base_url')."/wp-admin/comment.php?action=spam&c=".$commentID); ?></td></tr><?php */?>
<tr><td><br /></td></tr>
<?php /*?><tr><td>Ou veuillez vous rendre sur le panneau de modération :</td></tr>
<tr><td><?php echo anchor($this->config->item('wp_base_url')."/wp-admin/edit-comments.php?comment_status=moderated"); ?></td></tr><?php */?>
</table>
</div>
</body>
</html>
