<?php 

class EAN13 {

	private static $prity=array(
		array(1,1,1,1,1,1),
		array(1,1,0,1,0,0),
		array(1,1,0,0,1,0),
		array(1,1,0,0,0,1),
		array(1,0,1,1,0,0),
		array(1,0,0,1,1,0),
		array(1,0,0,0,1,1),
		array(1,0,1,0,1,0),
		array(1,0,1,0,0,1),
		array(1,0,0,1,0,1)
	);
	//Left has white bar at 1st.
	//Right has black bar at 1st (event only).
	private static $bartable=array(
		array('3211','1123'),
		array('2221','1222'),
		array('2122','2212'),
		array('1411','1141'),
		array('1132','2311'),
		array('1231','1321'),
		array('1114','4111'),
		array('1312','2131'),
		array('1213','3121'),
		array('3112','2113')
	);
	private static $guard = '101';
	private static $center ='01010';
	//configure
	private static $unit = 'px';
	private static $bw = 3;//bar width
	private static $width = 0;
	private static $height = 0;
	private static $fs = 0;//Font size
	private static $yt = 0;
	private static $dx = 0;//lengh between bar and text
	private static $x = 0;
	private static $y = 0;
	private static $sb = 0;
	private static $lb = 0;

    private static $width = $this->bw * 106;
    private static $height = $this->bw * 50;
    private static $fs= 8 * $this->bw;//Font size
    private static $yt= 45 * $this->bw;
    private static $dx= 2 * $this->bw;
    private static $x= 7 * $this->bw;
    private static $y= 2.5 * $this->bw;
    private static $sb= 35 * $this->bw;
    private static $lb= 45 * $this->bw;


	public static function check($str){
		$sum = 0;
		$code = str_split($str);
		$sum = ($code[1] + $code[3] + $code[5] + $code[7] + $code[9] + $code[11]) *3;
		$sum += $code[0] + $code[2] + $code[4] + $code[6] + $code[8] + $code[10];
		$sum = 10-($sum %10);
		return $sum;
	}
	public static function draw($num){
		// global  $unit,$prity,$bartable,$guard,$center,$width, $bw,$fs, $yt,$dx, $height, $x,$y, $sb, $lb;
		$num = preg_replace('/\D/','',$num);
		$char = $num.self::check($num);
		$first = substr($num,0,1);
        $first = (int)$first;
        $oe = $this->prity[$first];//Old event array for first number
        $char = str_split($char);

        $img='';
        $img.= "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">\n";
        $img.= "<svg width='$this->width$this->unit' height='$this->height$this->unit' version='1.1' xmlns='http://www.w3.org/2000/svg'>\n";
        $xt = $this->x + $this->dx - 8 * $this->bw;//Start point of text drawing
        $img.= "<text x='$xt$this->unit' y='$this->yt$this->unit' font-family='Arial' font-size='$this->fs'>$char[0]</text>\n";

        //Draw Guard bar.
        $val = $this->guard;
        $img .= "<desc>First Guard</desc>\n";
        $val =str_split($val);
        foreach ($val as $bar){
            if ((int)$bar === 1){
                $img.= "<rect x='$this->x$this->unit' y='$this->y$this->unit' width='$this->bw$this->unit' height='$this->lb$this->unit' fill='black' stroke-width='0' />\n";
            }
            $this->x = $this->x + $this->bw;
        }

        //Draw Left Bar.
        for ($i = 1;$i < 7; $i++){
            $id = $i - 1;//id for Old-event array
            $oev =! $oe[$id];//Old-event value
            $val = $this->bartable[$char[$i]][$oev];
            $img .= '<desc>'.htmlspecialchars($char[$i])."</desc>\n";
            $xt = $this->x + $this->dx;
            $img.= "<text x='$xt$this->unit' y='$this->yt$this->unit' font-family='Arial' font-size='$this->fs'>$char[$i]</text>\n";
            $val =str_split($val);
            for ($j = 0 ; $j < 4 ; $j++) {
                $num = (int)$val[$j];
                $w = $this->bw * $num;
                if ($j%2) {
                    $img.= "<rect x='$this->x$this->unit' y='$this->y$this->unit' width='$w$this->unit' height='$this->sb$this->unit' fill='black' stroke-width='0' />\n";
                }
                $this->x = $this->x + $w;
            }

        }
        
        //Draw Center Bar.
        $val = $this->center;
        $img.= "<desc>Center</desc>\n";
        $val = str_split($val);
        foreach ($val as $bar){
            if ((int)$bar===1){
                $img.= "<rect x='$this->x$this->unit' y='$this->y$this->unit' width='$this->bw$this->unit' height='$this->lb$this->unit' fill='black' stroke-width='0' />\n";
            }
            $this->x= $this->x + $this->bw;
        }
  
        //Draw Right Bar always in first column.
        for ($i = 7;$i < 13; $i++){
            $val = $this->bartable[$char[$i]][0];
            $img.= '<desc>'.htmlspecialchars($char[$i])."</desc>\n";
            $xt = $this->x + $this->dx;
            $img.= "<text x='$xt$this->unit' y='$this->yt$this->unit' font-family='Arial' font-size='$this->fs'>$char[$i]</text>\n";
            $val =str_split($val);
            for ($j = 0 ; $j < 4 ; $j++) {
                $num = (int)$val[$j];
                $w = $this->bw * $num;
                if (!($j%2)) {
                    $img.= "<rect x='$this->x$this->unit' y='$this->y$this->unit' width='$w$this->unit' height='$this->sb$this->unit' fill='black' stroke-width='0' />\n";
                }
                $this->x = $this->x + $w;
            }
        }

        //Draw End Guard Bar.
        $val = $this->guard;
        $img .= "<desc>End Guard</desc>\n";
        $val = str_split($val);
        foreach ($val as $bar){
            if ((int)$bar === 1){
                $img.= "<rect x='$this->x$this->unit' y='$this->y$this->unit' width='$this->bw$this->unit' height='$this->lb$this->unit' fill='black' stroke-width='0' />\n";
            }
            $this->x = $this->x + $this->bw;
        }
        $img.= '</svg>';
        return $img;
	}
}