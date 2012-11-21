<?php

/**
 * Define delay between Google API calls (can be fractional for sub-second delays)
 * 
 * This reduces load on the google server.
 */ 
define('GOOGLE_DELAY', 0.0);

 /**
 * POProcessor provides a simple PO file parser
 * 
 * Can parse a PO file and calls processEntry for each entry in it
 * Can derive from this class to perform any transformation you
 * like
 */
class POProcessor
{
    public $max_entries=0; //for testing you can limit the number of entries processed
    private $start=0; //timestamp when we started
    
    public function __construct()
    {
        
    }

    /**
     * Set callback function which is passed the completion
     * percentage and remaining time of the parsing operation. This callback
     * will be called up to 100 times, depending on the
     * size of the file.
     * 
     * Callback is a function name, or an array of ($object,$methodname)
     * as is common for PHP style callbacks
     */
    public function setProgressCallback($callback)
    {
        $this->progressCallback=$callback;
    }


    /**
     * Parses input file and calls processEntry for each recgonized entry
     * and output for all other lines
     * 
     * To track progress, see setProgressCallback
     */
    public function process($inFile)
    {
        set_time_limit(86400);
        $this->start=time();
        
        $msgid=array();
        $msgstr=array();
        $count=0;
        
        $size=filesize($inFile);
        $percent=-1;
        
        $state=0; 
        $in=fopen($inFile, 'r');
        
        $header = true;
        
        while (!feof($in))
        {
            $line=trim(fgets($in));
            $pos=ftell($in);
            $percent_now=round(($pos*100)/$size);
            if ($percent_now!=$percent)
            {
                $percent=$percent_now;
                $remain='';
                $elapsed=time()-$this->start;
                if ($elapsed>=5)
                {
                    $total = $elapsed/($percent/100);
                    $remain=$total-$elapsed;
                }
                
                $this->showProgress($percent,$remain);
            }
            
            $match=array();
            
            switch ($state)
            {
                case 0://waiting for msgid
                    if (preg_match('/^msgid "(.*)"$/', $line,$match)&&(strcasecmp('msgid ""',$line)!=0))
                    {
                        $clean=stripcslashes($match[1]);
                        $msgid=array($clean);
                        $state=1;
                        $header=false;
                    }
                    break;
                case 1: //reading msgid, waiting for msgstr
                    if (preg_match('/^msgstr "(.*)"$/', $line,$match))
                    {
                        $clean=stripcslashes($match[1]);
                        $msgstr=array($clean);
                        $state=2;
                    }
                    elseif (preg_match('/^"(.*)"$/', $line,$match))
                    {
                        $msgid[]=stripcslashes($match[1]);
                    }
                    break;
                case 2: //reading msgstr, waiting for blank
                    
                    if (preg_match('/^"(.*)"$/', $line,$match))
                    {
                        $msgstr[]=stripcslashes($match[1]);
                    }
                    elseif (empty($line))
                    {
                        //we have a complete entry
                        $this->processEntry($msgid, $msgstr);
                        $count++;
                        if ($this->max_entries && ($count>$this->max_entries))
                        {
                            break 2;
                        }
                        
                        $state=0;
                    }
                    
                    break;

            }
            
            //comment or blank line?
            if (empty($line) || preg_match('/^#/',$line) || ($header == true))
            {
                $this->output($line."\n");
            }
            
        }
        fclose($in);
    }

        
    /**
     * Called whenever the parser recognizes a msgid/msgstr pair in the
     * po file. It is passed an array of strings for the msgid and msgstr
     * which correspond to multiple lines in the input file, allowing you
     * to preserve this if desired.
     * 
     * Default implementation simply outputs the msgid and msgstr without
     * any further processing
     */
    protected function processEntry($msgid, $msgstr)
    {
        $this->output("msgid ");
        foreach($msgid as $part)
        {
            $part=addcslashes($part,"\r\n\"");
            $this->output("\"{$part}\"\n");
        }
        $this->output("msgstr ");
        foreach($msgstr as $part)
        {
            $part=addcslashes($part,"\r\n\"");
            $this->output("\"{$part}\"\n");
        }
    }


    
    /**
     * Internal method to call the progress callback if set
     */
    protected function showProgress($percentComplete, $remainingTime)
    {
        if (is_array($this->progressCallback))
        {
            $obj=$this->progressCallback[0];
            $method=$this->progressCallback[1];
            
            $obj->$method($percentComplete,$remainingTime);
        }
        elseif (is_string($this->progressCallback))
        {
            $func=$this->progressCallback;
            $func($percentComplete,$remainingTime);
        }
    }
    
    /**
     * Called to emit parsed lines of the file - override this
     * to provide customised output
     */
    protected function output($str)
    {
        global $output;
        $output.=$str;
    }

}

/**
 * Derivation of POProcessor which passes untranslated entries through the Google Translate
 * API and writes the transformed PO to another file
 * 
 */
class POTranslator extends POProcessor
{
    /**
     * Google API requires a referrer - constructor will build a suitable default
     */
    public $referrer;
    
    /**
     * How many seconds should we wait between Google API calls to be nice
     * to google and the server running Pepipopum? Can use a floating point
     * value for sub-second delays
     */
    public $delay=GOOGLE_DELAY;
    
    public function __construct()
    {
        parent::__construct();
        
        //Google API needs to be passed a referrer
        $this->referrer="http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
    
        
    /**
     * Translates a PO file storing output in desired location
     */
    public function translate($inFile, $outFile, $srcLanguage, $targetLanguage)
    {
        $ok=true;
        $this->srcLanguage=$srcLanguage;    
        $this->targetLanguage=$targetLanguage;    
        
        $this->fOut = "";
        $this->process($inFile);
//        $this->fOut=fopen($outFile, 'w');
//        if ($this->fOut)
//        {
//            $this->process($inFile);
//            fclose($this->fOut);
//            
//        }
//        else
//        {
//            trigger_error("POProcessor::translate unable to open $outfile for writing", E_USER_ERROR);
//            $ok=false;
//        }
        
        
        return $ok;
    }
    
    
    
    
    /**
     * Overriden output method writes to output file
     */
    protected function output($str)
    {
//        if ($this->fOut)
//        {
//            fwrite($this->fOut, $str);
//            flush();
//        } 
        $this->fOut .= $str;
    }
    
    /**
     * Overriden processEntry method performs the Google Translate API call
     */
    protected function processEntry($msgid, $msgstr)
    {
        $input=implode('', $msgid);
        $output=implode('', $msgstr);
//        print "msid: ";
//        print_r($input);
//        print " msgstr: ";
//        print_r($output);
//        print "<br>";
        
        if(!empty($input) && empty($output) && (strcasecmp($this->srcLanguage,$this->targetLanguage)==0))
        {
          $msgstr=array($input);
        }
        elseif (!empty($input) && empty($output))
        {
            $q=urlencode($input);
            $langpair=urlencode("{$this->srcLanguage}|{$this->targetLanguage}");
            $url="http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q={$q}&langpair={$langpair}";
            $cmd="curl -e ".escapeshellarg($this->referrer).' '.escapeshellarg($url);
            
            $result=`$cmd`;
            $data=json_decode($result);
            if (is_object($data) && is_object($data->responseData) && isset($data->responseData->translatedText))
            {
                $output=$data->responseData->translatedText;    
                
                //Google translate mangles placeholders, lets restore them
                $output=preg_replace('/%\ss/', '%s', $output);
                $output=preg_replace('/% (\d+) \$ s/', ' %$1\$s', $output);
                $output=preg_replace('/^ %/', '%', $output);
            
                //have seen %1 get flipped to 1%
                if (preg_match('/%\d/', $input) && preg_match('/\d%/', $output))
                {
                    $output=preg_replace('/(\d)%/', '%$1', $output);
            
                }
            
                //we also get entities for some chars
                $output=html_entity_decode($output);
                
                $msgstr=array($output);
            }
            
            //play nice with google
            usleep($this->delay * 1000000);
            
        }
        
        //output entry
        parent::processEntry($msgid, $msgstr);
    }

    
    
}


//simple progress callback which emits some JS to update the
//page with a progress count
function showProgress($percent,$remainingTime)
{
    $time='';
    if (!empty($remainingTime))
    {
        if ($remainingTime<120)
        {
            $time=sprintf("(%d seconds remaining)",$remainingTime);
        }
        elseif ($remainingTime<60*120)
        {
            $time=sprintf("(%d minutes remaining)",round($remainingTime/60));
        }
        else
        {
            $time=sprintf("(%d hours remaining)",round($remainingTime/3600));
        }
    }
    echo '<script language="Javascript">';
    echo "document.getElementById('info').innerHTML='$percent% complete $time';";
    echo "</script>\n";
    flush();
}

function processForm()
{
    set_time_limit(86400);
    
    $translator=new POTranslator();
    
    //we output to a temporary file to allow later download
//    echo '<h1>Processing PO file...</h1>';
//    echo '<div id="info"></div>';
//    $translator->setProgressCallback('showProgress');
    $outfile = tempnam(sys_get_temp_dir(), 'tmppo'); 
    
    
    $translator->translate($_FILES['pofile']['tmp_name'], $outfile, 'fr', $_POST['language']);
    
    $name=$_FILES['pofile']['name'];
    
    header("Content-Type:text/plain");
    header("Content-Length:".strlen($translator->fOut));
    header("Content-Disposition: attachment; filename=\"{$name}\"");
    echo $translator->fOut;
    
}

processForm();


?>