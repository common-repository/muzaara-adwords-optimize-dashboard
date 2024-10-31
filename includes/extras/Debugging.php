<?php 
namespace Muzaara;
use \Psr\Log\LogLevel;

class Debugging implements \Psr\Log\LoggerInterface {
	private $files = array(
		"error" => MUZAARA["logs_dir"] . "error.log",
		"debug" => MUZAARA["logs_dir"] . "debug.log"
	);

	public function __construct()
	{
		$this->fps = array();

		foreach( $this->files as $key => $file ) {
			$this->fps[ $key ] = fopen( $file, "a+" );
		}
	}

	private function write( $content, $title, $pointer )
	{
		$c = sprintf( "[%s]%s\t\t-\t\t%s\n", strtoupper($title), date( "Y-m-d H:i:s" ), $content );
		fwrite( $pointer, $content . "\n", 1000000);

		return $this;
	}

	public function __destruct()
	{
		foreach( $this->fps as $fp ) {
			fclose($fp);
		}
	}

	public function debug( $message, array $context = array() )
	{
		$this->write($message, "debug", $this->fps["debug"]);
	}

	public function info( $message, array $context = array() )
	{
		$this->write($message, "info", $this->fps["debug"]);
	}

	public function notice($message, array $context = array()) 
	{
		$this->write($message, "notice", $this->fps["debug"]);
	}

	public function warning($message, array $context = array())
	{
		$this->write($message, "warning", $this->fps["error"]);
	}

	public function error($message, array $context = array())
	{
		$this->write($message, "error", $this->fps["error"]);
	}

	public function critical($message, array $context = array())
	{
		$this->write($message, "critical", $this->fps["error"]);
	}

	public function alert($message, array $context = array())
	{
		$this->write($message, "alert", $this->fps["error"]);
	}

	public function emergency($message, array $context = array())
	{
		$this->write($message, "emergency", $this->fps["error"]);
	}

	public function log($level, $message, array $context = array()) 
	{
		// print_r( $message );
		file_put_contents( MUZAARA["logs_dir"] . "file.txt", $message);
		// switch( $level ) {
		// 	case LogLevel::INFO:
		// 		$this->info( $message, $context );
		// 		break;
		// 	case LogLevel::DEBUG:
		// 		$this->debug( $message, $context );
		// 		break;
		// 	default:
		// 		null;
		// }
	}
}