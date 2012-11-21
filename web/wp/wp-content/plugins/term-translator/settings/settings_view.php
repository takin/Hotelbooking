<div class="wrap">
<h2>Translation import CSV</h2>

<form enctype="multipart/form-data" action="<?php bloginfo('url');?>/wp-admin/admin.php?page=term-translator/plugin.php" method="post">

     <fieldset>
       <div class="field">
        <div class="label"><label for="pofile">CSV translation data</label></div>
        <div class="input"><input id="CSVtrans_file" name="CSVtrans_file" type="file" /></div>
        </div>
    </fieldset>

    <div>
    <input type="submit" name="translateCSV" value="TranslateCSVimport" />
    </div>

</form>

</div>
</div>
