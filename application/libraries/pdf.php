<?php

class PDF extends Fpdf {

	//variables of html parser
	public $B;
	public $I;
	public $U;
	public $HREF;
	public $fontList;
	public $issetfont;
	public $issetcolor;
	private $newLine;

	// Page header
	public function Header() {
		// Logo
		$this->Image(asset('img/logo.png'),10,8,40);
		$this->Ln(20);
	}

	// Page footer
	public function Footer() {
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('helvetica','I',8);
		// Page number
		$this->Cell(0,10,''.$this->PageNo(),0,0,'R'); // .'/{nb}' numero total de pag.
	}

	/** Cell UTF8
	* @author Quentin JANON
	* @param multi CF FPDF->CELL
	*/
	public function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='') {
		parent::Cell($w,$h, utf8_decode($txt), $border,$ln,$align,$fill,$link);
	}

	// override default constructor - we prefer US Letter as our page format
	public function __construct($orientation='P', $unit='mm', $format='Letter')
	{
		//Call parent constructor
		parent::__construct($orientation,$unit,$format);
		//Initialization
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
		$this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
		$this->issetfont=false;
		$this->issetcolor=false;
		$this->SetAuthor="CVTeam Chile";
	}

	public function WriteHTML($h=0,$html='') {
		//HTML parser
		$this->newLine=$h;
		$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
		$html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write($h,$this->txtentities($e));
			}
			else
			{
				//Tag
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else {
					//Extract attributes
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v) {
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	public function OpenTag($tag, $attr) {
		//Opening tag
		switch($tag){
			case 'STRONG':
				$this->SetStyle('B',true);
				break;
			case 'EM':
				$this->SetStyle('I',true);
				break;
			case 'B':
			case 'I':
			case 'U':
				$this->SetStyle($tag,true);
				break;
			case 'A':
				$this->HREF=$attr['HREF'];
				break;
			case 'IMG':
				if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
					if(!isset($attr['WIDTH']))
						$attr['WIDTH'] = 0;
					if(!isset($attr['HEIGHT']))
						$attr['HEIGHT'] = 0;
					$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), $this->px2mm($attr['WIDTH']), $this->px2mm($attr['HEIGHT']));
				}
				break;
			case 'TR':
			case 'BLOCKQUOTE':
			case 'BR':
				$this->Ln($this->newLine+1);
				break;
			case 'P':
				$this->Ln(10);
				break;
			case 'FONT':
				if (isset($attr['COLOR']) && $attr['COLOR']!='') {
					$coul=$this->hex2dec($attr['COLOR']);
					$this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
					$this->issetcolor=true;
				}
				if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
					$this->SetFont(strtolower($attr['FACE']));
					$this->issetfont=true;
				}
				break;
		}
	}

	public function CloseTag($tag) {
		//Closing tag
		if($tag=='STRONG')
			$tag='B';
		if($tag=='EM')
			$tag='I';
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF='';
		if($tag=='FONT'){
			if ($this->issetcolor==true) {
				$this->SetTextColor(0);
			}
			if ($this->issetfont) {
				$this->SetFont('arial');
				$this->issetfont=false;
			}
		}
	}

	public function SetStyle($tag, $enable) {
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s) {
			if($this->$s>0)
				$style.=$s;
		}
		$this->SetFont('',$style);
	}

	public function PutLink($URL, $txt) {
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}

//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
	public function hex2dec($couleur = "#000000") {
		$R = substr($couleur, 1, 2);
		$rouge = hexdec($R);
		$V = substr($couleur, 3, 2);
		$vert = hexdec($V);
		$B = substr($couleur, 5, 2);
		$bleu = hexdec($B);
		$tbl_couleur = array();
		$tbl_couleur['R']=$rouge;
		$tbl_couleur['V']=$vert;
		$tbl_couleur['B']=$bleu;
		return $tbl_couleur;
	}

	//conversion pixel -> millimeter at 72 dpi
	public function px2mm($px){
		return $px*25.4/72;
	}

	public function txtentities($html){
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans = array_flip($trans);
		return strtr($html, $trans);
	}

}
