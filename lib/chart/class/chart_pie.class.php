<?
/**
 * Pie Chart.
 *
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @copyright 2013 Marius Žilėnas 
 *
 * @version 0.0.1
 */
class ChartPie {
	public  $imx;
	public  $imy;
	public  $data  ;
	public  $ratios;
	public  $image;
	private $im;
	public  $colors;
	private $m_title;

	/**
	 * Tells if chart has no data.
	 * @return boolean
	 */
	public function is_empty() {
		return !is_array($this->data) && !isset($this->data[0]);
	}

	/**
	 * Getter for title.
	 * @return string|NULL
	 */
	public function title() {
		return $this->m_title;
	}
	/**
	 * Setter for title.
	 * @param string $value
	 */
	public function set_title($value) {
		$this->m_title = $value;
	}

	/**
	 * Constructor.
	 */
	public function __construct($imx, $imy, array $data) {
		$this->imx = $imx;
		$this->imy = $imy;
		$this->set_data($data);
	}

	/**
	 * Calculates ratios.
	 * @return void
	 */
	public function calc_ratios() {
		$this->ratios = array();
		if(is_array($this->data)) {
			$sum = array_sum( array_values($this->data) );
			foreach($this->data as $name => $value) {
				$this->ratios[$name] = $value / $sum;
			}
		}
	}

	/** 
	 * Set data item 
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function set_data_item($name, $value) {
		if(!is_array($this->data)) {
			$this->data = array();
		}
		$this->data[$name] = $value;
	}

	/**
	 * Set data.
	 * @param array $data array( 'name' => float, 'name' => float);
	 * @return void
	 */
	public function set_data(array $data) {
		foreach($data as $name => $value) {
			$this->set_data_item($name, $value);
		}
		$this->calc_ratios();
	}

	/**
	 * Gets image resource
	 * @return mixed
	 */
	private function im() {
		if(!$this->im) {
			$this->im = imagecreatetruecolor($this->imx, $this->imy);
		}
		return $this->im;
	}

	/**
	 * Allocates color.
	 * @return mixed
	 */
	public function next_color() {
		if(!is_array($this->colors) || empty($this->colors)) {
			$this->colors = array(array(0x00,0x00,0x00));
		}
		$last_color = end($this->colors); reset($this->colors);
		$last_color = array_map( function($el) {
			return str_pad($el, 2, "0", STR_PAD_LEFT);
		}, $last_color);
		$numb = hexdec($last_color[0].$last_color[1].$last_color[2]);
		$numb += 255*50;
		$hex_new_color = str_pad(dechex($numb), 6, "0", STR_PAD_LEFT);
		list($r, $g, $b) = str_split($hex_new_color, 2);
		$im = $this->im();

		$color = imagecolorallocate($im, hexdec($r), hexdec($g), hexdec($b));
		$this->colors[] = array(hexdec($r), hexdec($g), hexdec($b));
		return $color;
	}

	/**
	 * Outputs image.
	 * @param mixed $im Image.
	 * @param string $file
	 * @return string
	 */
	public static function out($im) {
		ob_start();
		imagepng($im);
		$image_data = ob_get_contents();
		ob_end_clean();
		imagedestroy($im);
		$ret = base64_encode($image_data); 
		return $ret;
	}

	/**
	 * Returns image as base64 string.
	 * @return string
	 */
	public function draw() {
		$im    = $this->im();
		$white = imagecolorallocate($im,255,255,255);
		$mins  = min($this->imx, $this->imy);
		/** Pie rectangle size */
		$prx   = 0.8*$mins;
		$pry   = 0.95*$mins;

		/** Pie left upper coord */
		$psx = 0;
		$psy = $this->imy - $pry; 

		/** Pie size */
		$px = $prx/2;
		$py = $psy + $pry/2;
		$d     = 2 * min($px, $py);
		$i     = 0;
		$start = 0;
		$end   = 0;
		$legend = array();
		foreach($this->ratios as $name => $ratio) {
			$start = $end;
			$end   = $start+$ratio*360;
			$color = $this->next_color();
			$legend[$name] = $color;
			imagefilledarc($im, $px, $py, $d, $d, $start, $end, $color, IMG_ARC_PIE);
			imagearc($im, $px, $py, $d, $d, $start, $end, $white);
		}
		/** legend takes 20 percent of height and 100 percent of width */
		$qs = 10; //rectangle size
		$sx = $prx; 
		$sy = 0;
		$fs = 4; //font
		$fh = 5*$fs; //(approx)font height
		foreach($legend as $name => $color) {
			$sy = $sy + $fh;
			imagerectangle($im, $sx-1, $sy-1, $sx+$qs+1, $sy+$qs+1, $white);
			imagefilledrectangle($im, $sx, $sy, $sx+$qs, $sy+$qs, $color);
			imagestring($this->im, $fs, $sx+$qs+$fs, $sy, $name, $white);
		}

		/** Title */
		if(!$title = $this->title()) {
			$title = "No title";
		}
		imagestring($this->im, $fs, 20, 20, $title, $white);

		return $this->out($im);
	}
}

