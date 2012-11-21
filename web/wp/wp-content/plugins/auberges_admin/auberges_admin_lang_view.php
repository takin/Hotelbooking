<div class="wrap">
<h2>Administration - Langage</h2>

<form enctype="multipart/form-data" action="<?php bloginfo('url');?>/wp-admin/admin.php?page=auberges_admin/auberges_admin.php" method="post">
    
     <fieldset>
    <legend>Input</legend>
       <div class="field">
        <div class="label"><label for="pofile">PO File</label></div>
        <div class="input"><input id="pofile" name="pofile" type="file" /></div>
        </div>
    </fieldset>
    
    <fieldset>
    <legend>Output options</legend>

 <div class="field">
        <div class="label"><label for="language">Target Language</label></div>
        <div class="input"><select id="language" name="language">
            <option value="af">Afrikaans</option>
            <option value="sq">Albanian</option>
            <option value="ar">Arabic</option>
            <option value="be">Belarusian</option>
            <option value="bg">Bulgarian</option>
            <option value="ca">Catalan</option>
            <option value="zh-CN">Chinese (Simplified)</option>
            <option value="zh-TW">Chinese (Traditional)</option>
            <option value="hr">Croatian</option>
            <option value="cs">Czech</option>
            <option value="da">Danish</option>
            <option value="nl">Dutch</option>
            <option value="en">English</option>
            <option value="et">Estonian</option>
            <option value="tl">Filipino</option>
            <option value="fi">Finnish</option>
            <option value="fr">French</option>
            <option value="gl">Galician</option>
            <option value="de">German</option>
            <option value="el">Greek</option>
            <option value="iw">Hebrew</option>
            <option value="hi">Hindi</option>
            <option value="hu">Hungarian</option>
            <option value="is">Icelandic</option>
            <option value="id">Indonesian</option>
            <option value="ga">Irish</option>
            <option value="it">Italian</option>
            <option value="ja">Japanese</option>
            <option value="ko">Korean</option>
            <option value="lv">Latvian</option>
            <option value="lt">Lithuanian</option>
            <option value="mk">Macedonian</option>
            <option value="ms">Malay</option>
            <option value="mt">Maltese</option>
            <option value="no">Norwegian</option>
            <option value="fa">Persian</option>
            <option value="pl">Polish</option>
            <option value="pt">Portuguese</option>
            <option value="ro">Romanian</option>
            <option value="ru">Russian</option>
            <option value="sr">Serbian</option>
            <option value="sk">Slovak</option>
            <option value="sl">Slovenian</option>
            <option value="es">Spanish</option>
            <option value="sw">Swahili</option>
            <option value="sv">Swedish</option>
            <option value="th">Thai</option>
            <option value="tr">Turkish</option>
            <option value="uk">Ukrainian</option>
            <option value="vi">Vietnamese</option>
            <option value="cy">Welsh</option>
            <option value="yi">Yiddish</option>
            </select>
        </div>
     </div>

     </fieldset>
   
    <div>
    <input type="submit" name="POtranslate" value="Translate File" />
    </div>
    
</form>

</div>
</div>
