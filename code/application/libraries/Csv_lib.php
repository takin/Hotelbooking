<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Csv_lib {

  protected $CI;

  protected $columns = array();

  protected $csv_name;
  protected $csv_path;
  protected $csv_handle;

  protected $header = false;

  protected $field_enclosure = '"';
  protected $field_delimiter = ";";
  protected $escape_character = "\\";  //only PHP 5.3

  public function __construct()
  {
    $this->CI = & get_instance();

    log_message('debug', 'CSV Class Initialized');
  }

  public function format($field_delimiter, $field_enclosure)
  {
    $this->field_delimiter = $field_delimiter;
    $this->field_enclosure = $field_enclosure;
  }

  public function init($path, $name, $header_detect = true)
  {
    if (($this->csv_handle = fopen($path.$name, "r")) !== false)
    {
      if($header_detect === true)
      {
        $this->header_detect();
      }
    }
    else
    {
      return FALSE;
    }
  }

  public function set_columns_def($columns_def)
  {
    $this->columns = $columns_def;
  }

  public function header_detect()
  {
    if($this->csv_handle === false) return FALSe;

    $this->columns = fgetcsv($this->csv_handle, 0, $this->field_delimiter,$this->field_enclosure);

    return $this->columns;
  }

  public function line()
  {
    $line = fgetcsv($this->csv_handle, 0, $this->field_delimiter,$this->field_enclosure);
    if($line === false)
    {
      //log message error
      //or throw exception?
      return array_fill_keys($this->columns,NULL);
    }
    return array_combine($this->columns,$line);
  }
}
?>