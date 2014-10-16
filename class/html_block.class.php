<?
/**
 * Generates various HTML blocks.
 * @version 0.0.12
 */
class HtmlBlock {

	/**
	 * Converts associative array to string.
	 * @todo Extract method to the LinkBuilder class.
	 * @param array a
	 */
	private static function a2a(array $a) {
		$rets = array();
		foreach($a as $k => $v) {
			$s = is_numeric($k) ? $v : "$k=$v";
			$rets[] = $s;
		}
		return join('&', $rets);
	}

	/**
	 * Object data as table rows.
	 * @param mixed $o Object
	 * @param Field[]
	 * @return string
	 */
	public static function oastr($o, array $fields = array()) {
		$ret = '';
		foreach($fields as $field) {
			$name  = $field->name();
			$value = $o->$name;
			$ret .= sprintf('<tr><td>%s<td>'.$field->format(), so(Language::beautify($name)), so($value));
		}
		return $ret;
	}
	/**
	 * Object data as table.
	 * @param mixed $o Object
	 * @return string|NULL
	 */
	public static function oastb($o, array $fields = array()) {
		$cl = Language::beautify(c2u(get_class($o)));
		$trs = self::oastr($o, $fields);
		$ret = <<<EOD
<table>
	<caption>Item: $cl</caption>
	<thead>
		<tr><th>Property<th>Value
	<thead>
	<tbody>
		$trs
	</tbody>
</table>
EOD;
		return $ret;
	}

	/**
	 * Items as list.
	 * @param array $items Items.
	 * @param string $hf (optional) Link function.
	 * @param callable $cf (optional) Content function.
	 * @return string
	 */
	public static function ial(array $items, $hf = NULL, $cf = NULL) {
		$ret  = '';
		$strs = array();
		foreach($items as $item) {
			if(is_callable($hf)) {
				$href = call_user_func($hf, $item);
				$str  = Html::a($href, so($item->to_s()));
			} else if(is_callable($cf)) {
				$str  = call_user_func($cf, $item);
			} else {
				$str  = so($item->to_s());
			}
			$strs[] = '<li>'.$str;
			if(!empty($strs)) {
				$ret = '<ul>'.join('', $strs).'</ul>';
			}
		}
		return $ret;
	}

	/**
	  * Cluster options for.
	  * @param string $cl Class name.
	  * @return array
	  */
	public static function cluster_options_for($cl) {
		$cos = Cluster::options_for($cl, Login::user());
		$cluster_options = array();
		foreach($cos as $co) {
			$cluster_options[$co->id] = $co->name;
		}
		return $cluster_options;
	}
}

