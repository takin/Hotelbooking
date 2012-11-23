<?php

echo login_check($this->tank_auth->is_logged_in(),"",
      "<div class=\"widget\">
       <a class=\"sidebar-account\" href=\"".site_url($this->Db_links->get_link("register"))."\">"._('Créer un compte dès maintenant')."</a>
       </div>");
?>
