<?php

namespace app\Core;

use Exception;

class Logs
{

    private string $Path;
    private string $Message;

    private string $Action = '';
    private string $Props = '';
    private string $Response = '';
    private string $Method = '';

    /**
     * Define file
     *
     * @param string $path
     *
     * @return $this
     */
    static public function file( string $path ):static{
        $Instance = new static();
        $Instance->Path = $path;
        return $Instance;
    }

    /**
     * Define message
     *
     * @param string $message
     *
     * @return $this
     */
    public function message( string $message ):static{
        $this->Message = $message;
        return $this;
    }

    /**
     * Define method request
     *
     * @param int $method
     *
     * @return $this
     */
    public function method( mixed $method = 0 ):static{

        $this->Method = match ($method) {
            default => 'GET',
            2,'POST' => 'POST',
            3,'OPTIONS' => 'OPTIONS',
            4,'PUT' => 'PUT',
            5, 'TEXT' => 'TEXT'
        };

        return $this;
    }

    /**
     * Define action request
     *
     * @param mixed $action
     * @param string $custom
     *
     * @return $this
     */
    public function action( mixed $action = 0, string $custom = ''):static{

        $Action = match ($action) {
            default => 'Generic',
            1 => 'Create',
            2 => 'Update',
            3 => 'Webhook'
        };

        $this->Action = $Action . ' ' . $custom;

        return $this;
    }

    /**
     * Define props
     *
     * @param mixed $props
     *
     * @return $this
     */
    public function props( mixed $props):static{
        $this->Props = is_array($props) ? json_encode($props) : $props;
        return $this;
    }

    /**
     * Define response
     *
     * @param mixed $response
     *
     * @return $this
     */
    public function response( mixed $response ):static{
        $this->Response = is_array($response) ? json_encode($response) : $response;
        return $this;
    }

    /**
     * Save or Update file
     * @return void
     */
    public function save():void{

        try {

            if ( is_dir( APP_PATH . "Logs/". $this->Path ) === false) {
                mkdir(APP_PATH . "Logs/". $this->Path, recursive: true);
            }

            $Name = $this->Path . '/' . date('d-m-Y');

            $File = fopen( APP_PATH . "Logs/".$Name.".log", 'a' );

            $Message = @date('d/m/Y H:i:s');

            if( !empty($this->Action) ){
                $Message .= "\t Action:" . $this->Action;
            }

            if( !empty($this->Method) ){
                $Message .= "\t Method:" . $this->Method;
            }

            if( !empty($this->Message) ){
                $Message .= "\t Message:" . $this->Message;
            }

            if( !empty($this->Props) ){
                $Message .= "\nParameters:" . $this->Props;
            }

            if( !empty($this->Response) ){
                $Message .= "\nReturn:" . $this->Response;
            }

            fwrite($File, $Message . "\n\n");

            fclose($File);

        }catch (Exception){ }

    }

}
