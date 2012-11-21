<?php
class CSV
{
  private $filepath = "";
  private $handle = FALSE;

  public function CSV($filepath)
  {
    $this->filepath = $filepath;
    $this->handle = fopen($this->filepath, "r");
  }

  public function rows()
  {
    if($this->handle !== FALSE)
    {
      return fgetcsv($this->handle, 0, ",");
    }
  }

  public function __destruct()
  {
    if($this->handle !== FALSE)
    {
      fclose($this->handle);
    }
  }

}
?>