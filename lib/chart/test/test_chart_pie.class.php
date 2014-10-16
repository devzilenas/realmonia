<?

class TestChartPie extends Test {
	public static function test_draw() {
		$data = ( array(
			's2'  => 2,
			's5'  => 5,
			's10' => 10,
			's1'  => 1));
		$chart = new ChartPie(500, 500, $data);
		$chart->set_title("Expenses");
		echo '<img src="data:image/png;base64,'.$chart->draw().'" />';
	}
}

