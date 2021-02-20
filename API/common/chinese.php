<?php
require_once(dirname(__FILE__) . '/fpdf.php');
$Big5_widths = array(
	' ' => 250, '!' => 250, '"' => 408, '#' => 668, '$' => 490, '%' => 875, '&' => 698, '\'' => 250,
	'(' => 240, ')' => 240, '*' => 417, '+' => 667, ',' => 250, '-' => 313, '.' => 250, '/' => 520, '0' => 500, '1' => 500,
	'2' => 500, '3' => 500, '4' => 500, '5' => 500, '6' => 500, '7' => 500, '8' => 500, '9' => 500, ':' => 250, ';' => 250,
	'<' => 667, '=' => 667, '>' => 667, '?' => 396, '@' => 921, 'A' => 677, 'B' => 615, 'C' => 719, 'D' => 760, 'E' => 625,
	'F' => 552, 'G' => 771, 'H' => 802, 'I' => 354, 'J' => 354, 'K' => 781, 'L' => 604, 'M' => 927, 'N' => 750, 'O' => 823,
	'P' => 563, 'Q' => 823, 'R' => 729, 'S' => 542, 'T' => 698, 'U' => 771, 'V' => 729, 'W' => 948, 'X' => 771, 'Y' => 677,
	'Z' => 635, '[' => 344, '\\' => 520, ']' => 344, '^' => 469, '_' => 500, '`' => 250, 'a' => 500, 'b' => 521, 'c' => 500,
	'd' => 521, 'e' => 550, 'f' => 450, 'g' => 469, 'h' => 531, 'i' => 450, 'j' => 250, 'k' => 458, 'l' => 240, 'm' => 582,
	'n' => 531, 'o' => 500, 'p' => 521, 'q' => 521, 'r' => 365, 's' => 580, 't' => 450, 'u' => 521, 'v' => 458, 'w' => 500,
	'x' => 490, 'y' => 458, 'z' => 427, '{' => 480, '|' => 496, '}' => 480, '~' => 667
);

$GB_widths = array(
	' ' => 207, '!' => 270, '"' => 342, '#' => 467, '$' => 462, '%' => 797, '&' => 710, '\'' => 239,
	'(' => 374, ')' => 374, '*' => 423, '+' => 605, ',' => 238, '-' => 375, '.' => 238, '/' => 334, '0' => 462, '1' => 462,
	'2' => 462, '3' => 462, '4' => 462, '5' => 462, '6' => 462, '7' => 462, '8' => 462, '9' => 462, ':' => 238, ';' => 238,
	'<' => 605, '=' => 605, '>' => 605, '?' => 344, '@' => 748, 'A' => 684, 'B' => 560, 'C' => 695, 'D' => 739, 'E' => 563,
	'F' => 511, 'G' => 729, 'H' => 793, 'I' => 318, 'J' => 312, 'K' => 666, 'L' => 526, 'M' => 896, 'N' => 758, 'O' => 772,
	'P' => 544, 'Q' => 772, 'R' => 628, 'S' => 465, 'T' => 607, 'U' => 753, 'V' => 711, 'W' => 972, 'X' => 647, 'Y' => 620,
	'Z' => 607, '[' => 374, '\\' => 333, ']' => 374, '^' => 606, '_' => 500, '`' => 239, 'a' => 417, 'b' => 503, 'c' => 427,
	'd' => 529, 'e' => 415, 'f' => 264, 'g' => 444, 'h' => 518, 'i' => 241, 'j' => 230, 'k' => 495, 'l' => 228, 'm' => 793,
	'n' => 527, 'o' => 524, 'p' => 524, 'q' => 504, 'r' => 338, 's' => 336, 't' => 277, 'u' => 517, 'v' => 450, 'w' => 652,
	'x' => 466, 'y' => 452, 'z' => 407, '{' => 370, '|' => 258, '}' => 370, '~' => 605
);

class PDF_Chinese extends FPDF
{
	var $javascript;
	var $n_js;

	function IncludeJS($script)
	{
		$this->javascript = $script;
	}

	function _putjavascript()
	{
		$this->_newobj();
		$this->n_js = $this->n;
		$this->_out('<<');
		$this->_out('/Names [(EmbeddedJS) ' . ($this->n + 1) . ' 0 R ]');
		$this->_out('>>');
		$this->_out('endobj');
		$this->_newobj();
		$this->_out('<<');
		$this->_out('/S /JavaScript');
		$this->_out('/JS ' . $this->_textstring($this->javascript));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putresources()
	{
		parent::_putresources();
		if (!empty($this->javascript)) {
			$this->_putjavascript();
		}
	}

	function _putcatalog()
	{
		parent::_putcatalog();
		if (isset($this->javascript)) {
			$this->_out('/Names <</JavaScript ' . ($this->n_js) . ' 0 R>>');
		}
	}
	function AddCIDFont($family, $style, $name, $cw, $CMap, $registry)
	{
		$fontkey = strtolower($family) . strtoupper($style);
		if (isset($this->fonts[$fontkey]))
			$this->Error("Font already added: $family $style");
		$i = count($this->fonts) + 1;
		$name = str_replace(' ', '', $name);
		$this->fonts[$fontkey] = array('i' => $i, 'type' => 'Type0', 'name' => $name, 'up' => -130, 'ut' => 40, 'cw' => $cw, 'CMap' => $CMap, 'registry' => $registry);
	}

	function AddCIDFonts($family, $name, $cw, $CMap, $registry)
	{
		$this->AddCIDFont($family, '', $name, $cw, $CMap, $registry);
		$this->AddCIDFont($family, 'B', $name . ',Bold', $cw, $CMap, $registry);
		$this->AddCIDFont($family, 'I', $name . ',Italic', $cw, $CMap, $registry);
		$this->AddCIDFont($family, 'BI', $name . ',BoldItalic', $cw, $CMap, $registry);
	}

	function AddBig5Font($family = 'Big5', $name = 'MSungStd-Light-Acro')
	{
		//Add Big5 font with proportional Latin
		$cw = $GLOBALS['Big5_widths'];
		$CMap = 'ETenms-B5-H';
		$registry = array('ordering' => 'CNS1', 'supplement' => 0);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	function AddBig5hwFont($family = 'Big5-hw', $name = 'MSungStd-Light-Acro')
	{
		//Add Big5 font with half-witdh Latin
		for ($i = 32; $i <= 126; $i++)
			$cw[chr($i)] = 500;
		$CMap = 'ETen-B5-H';
		$registry = array('ordering' => 'CNS1', 'supplement' => 0);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	function AddGBFont($family = 'GB', $name = 'STSongStd-Light-Acro')
	{
		//Add GB font with proportional Latin
		$cw = $GLOBALS['GB_widths'];
		$CMap = 'GBKp-EUC-H';
		$registry = array('ordering' => 'GB1', 'supplement' => 2);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	function AddGBhwFont($family = 'GB-hw', $name = 'STSongStd-Light-Acro')
	{
		//Add GB font with half-width Latin
		for ($i = 32; $i <= 126; $i++)
			$cw[chr($i)] = 500;
		$CMap = 'GBK-EUC-H';
		$registry = array('ordering' => 'GB1', 'supplement' => 2);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	function GetStringWidth($s)
	{
		if ($this->CurrentFont['type'] == 'Type0')
			return $this->GetMBStringWidth($s);
		else
			return parent::GetStringWidth($s);
	}

	function GetMBStringWidth($s)
	{
		//Multi-byte version of GetStringWidth()
		$l = 0;
		$cw = &$this->CurrentFont['cw'];
		$nb = strlen($s);
		$i = 0;
		while ($i < $nb) {
			$c = $s[$i];
			if (ord($c) < 128) {
				$l += $cw[$c];
				$i++;
			} else {
				$l += 1000;
				$i += 2;
			}
		}
		return $l * $this->FontSize / 1000;
	}

	function MultiCell($w, $h, $txt, $border = 0, $align = 'L', $fill = 0)
	{
		if ($this->CurrentFont['type'] == 'Type0')
			$this->MBMultiCell($w, $h, $txt, $border, $align, $fill);
		else
			parent::MultiCell($w, $h, $txt, $border, $align, $fill);
	}

	function MBMultiCell($w, $h, $txt, $border = 0, $align = 'L', $fill = 0)
	{
		//Multi-byte version of MultiCell()
		$cw = &$this->CurrentFont['cw'];
		if ($w == 0)
			$w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		if ($nb > 0 and $s[$nb - 1] == "\n")
			$nb--;
		$b = 0;
		if ($border) {
			if ($border == 1) {
				$border = 'LTRB';
				$b = 'LRT';
				$b2 = 'LR';
			} else {
				$b2 = '';
				if (is_int(strpos($border, 'L')))
					$b2 .= 'L';
				if (is_int(strpos($border, 'R')))
					$b2 .= 'R';
				$b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
			}
		}
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while ($i < $nb) {
			//Get next character
			$c = $s[$i];
			//Check if ASCII or MB
			$ascii = (ord($c) < 128);
			if ($c == "\n") {
				//Explicit line break
				$this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				if ($border and $nl == 2)
					$b = $b2;
				continue;
			}
			if (!$ascii) {
				$sep = $i;
				$ls = $l;
			} elseif ($c == ' ') {
				$sep = $i;
				$ls = $l;
			}
			$l += $ascii ? $cw[$c] : 1000;
			if ($l > $wmax) {
				//Automatic line break
				if ($sep == -1 or $i == $j) {
					if ($i == $j)
						$i += $ascii ? 1 : 2;
					$this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
				} else {
					$this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
					$i = ($s[$sep] == ' ') ? $sep + 1 : $sep;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				if ($border and $nl == 2)
					$b = $b2;
			} else
				$i += $ascii ? 1 : 2;
		}
		//Last chunk
		if ($border and is_int(strpos($border, 'B')))
			$b .= 'B';
		$this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
		$this->x = $this->lMargin;
	}

	function Write($h, $txt, $link = '')
	{
		if ($this->CurrentFont['type'] == 'Type0')
			$this->MBWrite($h, $txt, $link);
		else
			parent::Write($h, $txt, $link);
	}

	function MBWrite($h, $txt, $link)
	{
		//Multi-byte version of Write()
		$cw = &$this->CurrentFont['cw'];
		$w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while ($i < $nb) {
			//Get next character
			$c = $s[$i];
			//Check if ASCII or MB
			$ascii = (ord($c) < 128);
			if ($c == "\n") {
				//Explicit line break
				$this->Cell($w, $h, substr($s, $j, $i - $j), 0, 2, '', 0, $link);
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				if ($nl == 1) {
					$this->x = $this->lMargin;
					$w = $this->w - $this->rMargin - $this->x;
					$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
				}
				$nl++;
				continue;
			}
			if (!$ascii or $c == ' ')
				$sep = $i;
			$l += $ascii ? $cw[$c] : 1000;
			if ($l > $wmax) {
				//Automatic line break
				if ($sep == -1 or $i == $j) {
					if ($this->x > $this->lMargin) {
						//Move to next line
						$this->x = $this->lMargin;
						$this->y += $h;
						$w = $this->w - $this->rMargin - $this->x;
						$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
						$i++;
						$nl++;
						continue;
					}
					if ($i == $j)
						$i += $ascii ? 1 : 2;
					$this->Cell($w, $h, substr($s, $j, $i - $j), 0, 2, '', 0, $link);
				} else {
					$this->Cell($w, $h, substr($s, $j, $sep - $j), 0, 2, '', 0, $link);
					$i = ($s[$sep] == ' ') ? $sep + 1 : $sep;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				if ($nl == 1) {
					$this->x = $this->lMargin;
					$w = $this->w - $this->rMargin - $this->x;
					$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
				}
				$nl++;
			} else
				$i += $ascii ? 1 : 2;
		}
		//Last chunk
		if ($i != $j)
			$this->Cell($l / 1000 * $this->FontSize, $h, substr($s, $j, $i - $j), 0, 0, '', 0, $link);
	}

	function _putfonts()
	{
		$nf = $this->n;
		foreach ($this->diffs as $diff) {
			//Encodings
			$this->_newobj();
			$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $diff . ']>>');
			$this->_out('endobj');
		}
		// $mqr = get_magic_quotes_runtime();
		//set_magic_quotes_runtime(0);
		foreach ($this->FontFiles as $file => $info) {
			//Font file embedding
			$this->_newobj();
			$this->FontFiles[$file]['n'] = $this->n;
			if (defined('FPDF_FONTPATH'))
				$file = FPDF_FONTPATH . $file;
			$size = filesize($file);
			if (!$size)
				$this->Error('Font file not found');
			$this->_out('<</Length ' . $size);
			if (substr($file, -2) == '.z')
				$this->_out('/Filter /FlateDecode');
			$this->_out('/Length1 ' . $info['length1']);
			if (isset($info['length2']))
				$this->_out('/Length2 ' . $info['length2'] . ' /Length3 0');
			$this->_out('>>');
			$f = fopen($file, 'rb');
			$this->_putstream(fread($f, $size));
			fclose($f);
			$this->_out('endobj');
		}
		//set_magic_quotes_runtime($mqr);
		foreach ($this->fonts as $k => $font) {
			//Font objects
			$this->_newobj();
			$this->fonts[$k]['n'] = $this->n;
			$this->_out('<</Type /Font');
			if ($font['type'] == 'Type0')
				$this->_putType0($font);
			else {
				$name = $font['name'];
				$this->_out('/BaseFont /' . $name);
				if ($font['type'] == 'core') {
					//Standard font
					$this->_out('/Subtype /Type1');
					if ($name != 'Symbol' and $name != 'ZapfDingbats')
						$this->_out('/Encoding /WinAnsiEncoding');
				} else {
					//Additional font
					$this->_out('/Subtype /' . $font['type']);
					$this->_out('/FirstChar 32');
					$this->_out('/LastChar 255');
					$this->_out('/Widths ' . ($this->n + 1) . ' 0 R');
					$this->_out('/FontDescriptor ' . ($this->n + 2) . ' 0 R');
					if ($font['enc']) {
						if (isset($font['diff']))
							$this->_out('/Encoding ' . ($nf + $font['diff']) . ' 0 R');
						else
							$this->_out('/Encoding /WinAnsiEncoding');
					}
				}
				$this->_out('>>');
				$this->_out('endobj');
				if ($font['type'] != 'core') {
					//Widths
					$this->_newobj();
					$cw = &$font['cw'];
					$s = '[';
					for ($i = 32; $i <= 255; $i++)
						$s .= $cw[chr($i)] . ' ';
					$this->_out($s . ']');
					$this->_out('endobj');
					//Descriptor
					$this->_newobj();
					$s = '<</Type /FontDescriptor /FontName /' . $name;
					foreach ($font['desc'] as $k => $v)
						$s .= ' /' . $k . ' ' . $v;
					$file = $font['file'];
					if ($file)
						$s .= ' /FontFile' . ($font['type'] == 'Type1' ? '' : '2') . ' ' . $this->FontFiles[$file]['n'] . ' 0 R';
					$this->_out($s . '>>');
					$this->_out('endobj');
				}
			}
		}
	}

	function _putType0($font)
	{
		//Type0
		$this->_out('/Subtype /Type0');
		$this->_out('/BaseFont /' . $font['name'] . '-' . $font['CMap']);
		$this->_out('/Encoding /' . $font['CMap']);
		$this->_out('/DescendantFonts [' . ($this->n + 1) . ' 0 R]');
		$this->_out('>>');
		$this->_out('endobj');
		//CIDFont
		$this->_newobj();
		$this->_out('<</Type /Font');
		$this->_out('/Subtype /CIDFontType0');
		$this->_out('/BaseFont /' . $font['name']);
		$this->_out('/CIDSystemInfo <</Registry ' . $this->_textstring('Adobe') . ' /Ordering ' . $this->_textstring($font['registry']['ordering']) . ' /Supplement ' . $font['registry']['supplement'] . '>>');
		$this->_out('/FontDescriptor ' . ($this->n + 1) . ' 0 R');
		if ($font['CMap'] == 'ETen-B5-H')
			$W = '13648 13742 500';
		elseif ($font['CMap'] == 'GBK-EUC-H')
			$W = '814 907 500 7716 [500]';
		else
			$W = '1 [' . implode(' ', $font['cw']) . ']';
		$this->_out('/W [' . $W . ']>>');
		$this->_out('endobj');
		//Font descriptor
		$this->_newobj();
		$this->_out('<</Type /FontDescriptor');
		$this->_out('/FontName /' . $font['name']);
		$this->_out('/Flags 6');
		$this->_out('/FontBBox [0 -200 1000 900]');
		$this->_out('/ItalicAngle 0');
		$this->_out('/Ascent 800');
		$this->_out('/Descent -200');
		$this->_out('/CapHeight 800');
		$this->_out('/StemV 50');
		$this->_out('>>');
		$this->_out('endobj');
	}
}

define('FPDF_UNICODE_ENCODING', 'UCS-2BE');
class PDF_Unicode extends PDF_Chinese
{
	var $charset;     // input charset. User must add proper fonts by add font functions like AddUniCNShwFont
	var $isUnicode;   // whether charset belongs to Unicode

	function __construct($charset = 'UTF-8')
	{
		$this->FPDF('P', 'mm', 'A4');
		$this->charset = strtoupper(str_replace('-', '', $charset));
		$this->isUnicode = in_array($this->charset, array('UTF8', 'UTF16', 'UCS2'));
	}

	function AddUniCNShwFont($family = 'Uni', $name = 'PMingLiU')  // name for Kai font is DFKai-SB
	{
		//Add Unicode font with half-witdh Latin, character code must be utf16be
		for ($i = 32; $i <= 126; $i++)
			$cw[chr($i)] = 500;
		$CMap = 'UniCNS-UCS2-H';  // for compatible with PDF 1.3 (Adobe-CNS1-0), 1.4 (Adobe-CNS1-3), 1.5 (Adobe-CNS1-3)
		//$CMap='UniCNS-UTF16-H';  // for compatible with 1.5 (Adobe-CNS1-4)
		$registry = array('ordering' => 'CNS1', 'supplement' => 0);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	function AddUniCNSFont($family = 'Uni', $name = 'PMingLiU')
	{
		//Add Unicode font with propotional Latin, character code must be utf16be
		$cw = $GLOBALS['Big5_widths'];
		$CMap = 'UniCNS-UCS2-H';
		$registry = array('ordering' => 'CNS1', 'supplement' => 0);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	function AddUniGBhwFont($family = 'uGB', $name = 'AdobeSongStd-Light')
	{
		//Add Unicode font with half-witdh Latin, character code must be utf16be
		for ($i = 32; $i <= 126; $i++)
			$cw[chr($i)] = 500;
		$CMap = 'UniGB-UCS2-H';
		$registry = array('ordering' => 'GB1', 'supplement' => 4);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	function AddUniGBFont($family = 'uGB', $name = 'AdobeSongStd-Light')
	{
		//Add Unicode font with propotional Latin, character code must be utf16be
		$cw = $GLOBALS['GB_widths'];
		$CMap = 'UniGB-UCS2-H';
		$registry = array('ordering' => 'GB1', 'supplement' => 4);
		$this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
	}

	// redefinition of FPDF functions

	function GetStringWidth($s)
	{
		//Get width of a string in the current font
		if ($this->isUnicode) {
			$txt = mb_convert_encoding($s, FPDF_UNICODE_ENCODING, $this->charset);
			$oEnc = mb_internal_encoding();
			mb_internal_encoding(FPDF_UNICODE_ENCODING);
			$w = $this->GetUniStringWidth($txt);
			mb_internal_encoding($oEnc);
			return $w;
		} else
			return parent::GetStringWidth($s);
	}

	function Text($x, $y, $txt)
	{
		if ($this->isUnicode) {
			$txt = mb_convert_encoding($txt, FPDF_UNICODE_ENCODING, $this->charset);
			$oEnc = mb_internal_encoding();
			mb_internal_encoding(FPDF_UNICODE_ENCODING);
			$this->UniText($x, $y, $txt);
			mb_internal_encoding($oEnc);
		} else
			parent::Text($x, $y, $txt);
	}

	function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '')
	{
		if ($this->isUnicode) {
			$txt = mb_convert_encoding($txt, FPDF_UNICODE_ENCODING, $this->charset);
			$oEnc = mb_internal_encoding();
			mb_internal_encoding(FPDF_UNICODE_ENCODING);
			$this->UniCell($w, $h, $txt, $border, $ln, $align, $fill, $link);
			mb_internal_encoding($oEnc);
		} else
			parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
	}

	function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = 0)
	{
		if ($this->isUnicode) {
			$txt = mb_convert_encoding($txt, FPDF_UNICODE_ENCODING, $this->charset);
			$oEnc = mb_internal_encoding();
			mb_internal_encoding(FPDF_UNICODE_ENCODING);
			$this->UniMultiCell($w, $h, $txt, $border, $align, $fill);
			mb_internal_encoding($oEnc);
		} else {
			parent::MultiCell($w, $h, $txt, $border, $align, $fill);
		}
	}

	function Write($h, $txt, $link = '')
	{
		if ($this->isUnicode) {
			$txt = mb_convert_encoding($txt, FPDF_UNICODE_ENCODING, $this->charset);
			$oEnc = mb_internal_encoding();
			mb_internal_encoding(FPDF_UNICODE_ENCODING);
			$this->UniWrite($h, $txt, $link);
			mb_internal_encoding($oEnc);
		} else {
			parent::Write($h, $txt, $link);
		}
	}

	// implementation in Unicode version 

	function GetUniStringWidth($s)
	{
		//Unicode version of GetStringWidth()
		$l = 0;
		$cw = &$this->CurrentFont['cw'];
		$nb = mb_strlen($s);
		$i = 0;
		while ($i < $nb) {
			$c = mb_substr($s, $i, 1);
			$ord = hexdec(bin2hex($c));
			if ($ord < 128) {
				$l += $cw[chr($ord)];
			} else {
				$l += 1000;
			}
			$i++;
		}
		return $l * $this->FontSize / 1000;
	}

	function UniText($x, $y, $txt)
	{
		// copied from parent::Text but just modify the line below
		$s = sprintf('BT %.2f %.2f Td <%s> Tj ET', $x * $this->k, ($this->h - $y) * $this->k, bin2hex($txt));

		if ($this->underline && $txt != '')
			$s .= ' ' . $this->_dounderline($x, $y, $txt);
		if ($this->ColorFlag)
			$s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
		$this->_out($s);
	}

	function UniCell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '')
	{
		// copied from parent::Text but just modify the line with an output "BT %.2f %.2f Td <%s> Tj ET" ...
		$k = $this->k;
		if ($this->y + $h > $this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak()) {
			//Automatic page break
			$x = $this->x;
			$ws = $this->ws;
			if ($ws > 0) {
				$this->ws = 0;
				$this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			$this->x = $x;
			if ($ws > 0) {
				$this->ws = $ws;
				$this->_out(sprintf('%.3f Tw', $ws * $k));
			}
		}
		if ($w == 0)
			$w = $this->w - $this->rMargin - $this->x;
		$s = '';
		if ($fill == 1 || $border == 1) {
			if ($fill == 1)
				$op = ($border == 1) ? 'B' : 'f';
			else
				$op = 'S';
			$s = sprintf('%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
		}
		if (is_string($border)) {
			$x = $this->x;
			$y = $this->y;
			if (strpos($border, 'L') !== false)
				$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y + $h)) * $k);
			if (strpos($border, 'T') !== false)
				$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);
			if (strpos($border, 'R') !== false)
				$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
			if (strpos($border, 'B') !== false)
				$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
		}
		if ($txt !== '') {
			if ($align == 'R')
				$dx = $w - $this->cMargin - $this->GetUniStringWidth($txt);
			elseif ($align == 'C')
				$dx = ($w - $this->GetUniStringWidth($txt)) / 2;
			else
				$dx = $this->cMargin;
			if ($this->ColorFlag)
				$s .= 'q ' . $this->TextColor . ' ';
			$s .= sprintf(
				'BT %.2f %.2f Td <%s> Tj ET',
				($this->x + $dx) * $k,
				($this->h - ($this->y + .5 * $h + .3 * $this->FontSize)) * $k,
				bin2hex($txt)
			);
			if ($this->underline)
				$s .= ' ' . $this->_dounderline($this->x + $dx, $this->y + .5 * $h + .3 * $this->FontSize, $txt);
			if ($this->ColorFlag)
				$s .= ' Q';
			if ($link)
				$this->Link($this->x + $dx, $this->y + .5 * $h - .5 * $this->FontSize, $this->GetUniStringWidth($txt), $this->FontSize, $link);
		}
		if ($s)
			$this->_out($s);
		$this->lasth = $h;
		if ($ln > 0) {
			//Go to next line
			$this->y += $h;
			if ($ln == 1)
				$this->x = $this->lMargin;
		} else
			$this->x += $w;
	}

	function UniMultiCell($w, $h, $txt, $border = 0, $align = 'L', $fill = 0)
	{
		//Unicode version of MultiCell()

		$enc = mb_internal_encoding();

		$cw = &$this->CurrentFont['cw'];
		if ($w == 0)
			$w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = $txt;
		$nb = mb_strlen($s);
		if ($nb > 0 && mb_substr($s, -1) == mb_convert_encoding("\n", $enc, $this->charset))
			$nb--;
		$b = 0;
		if ($border) {
			if ($border == 1) {
				$border = 'LTRB';
				$b = 'LRT';
				$b2 = 'LR';
			} else {
				$b2 = '';
				if (is_int(strpos($border, 'L')))
					$b2 .= 'L';
				if (is_int(strpos($border, 'R')))
					$b2 .= 'R';
				$b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
			}
		}
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while ($i < $nb) {
			//Get next character
			$c = mb_substr($s, $i, 1);
			$ord = hexdec(bin2hex($c));
			$ascii = ($ord < 128);
			if ($c == mb_convert_encoding("\n", $enc, $this->charset)) {
				//Explicit line break
				$this->UniCell($w, $h, mb_substr($s, $j, $i - $j), $b, 2, $align, $fill);
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				if ($border && $nl == 2)
					$b = $b2;
				continue;
			}
			if (!$ascii || $c == mb_convert_encoding(' ', $enc, $this->charset)) {
				$sep = $i;
				$ls = $l;
			}
			$l += $ascii ? $cw[chr($ord)] : 1000;
			if ($l > $wmax) {
				//Automatic line break
				if ($sep == -1 || $i == $j) {
					if ($i == $j)
						$i++; //=$ascii ? 1 : 2;
					$this->UniCell($w, $h, mb_substr($s, $j, $i - $j), $b, 2, $align, $fill);
				} else {
					$this->UniCell($w, $h, mb_substr($s, $j, $sep - $j), $b, 2, $align, $fill);
					$i = (mb_substr($s, $sep, 1) == mb_convert_encoding(' ', $enc, $this->charset)) ? $sep + 1 : $sep;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				if ($border && $nl == 2)
					$b = $b2;
			} else
				$i++; //=$ascii ? 1 : 2;
		}
		//Last chunk
		if ($border && is_int(strpos($border, 'B')))
			$b .= 'B';
		$this->UniCell($w, $h, mb_substr($s, $j, $i - $j), $b, 2, $align, $fill);
		$this->x = $this->lMargin;
	}

	function UniWrite($h, $txt, $link = '')
	{
		//Unicode version of Write()
		$enc = mb_internal_encoding();
		$cw = &$this->CurrentFont['cw'];
		$w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = $txt;

		$nb = mb_strlen($s);
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while ($i < $nb) {
			//Get next character
			$c = mb_substr($s, $i, 1);
			//Check if ASCII or MB
			$ord = hexdec(bin2hex($c));
			$ascii = ($ord < 128);
			if ($c == mb_convert_encoding("\n", $enc, $this->charset)) {
				//Explicit line break
				$this->UniCell($w, $h, mb_substr($s, $j, $i - $j), 0, 2, '', 0, $link);
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				if ($nl == 1) {
					$this->x = $this->lMargin;
					$w = $this->w - $this->rMargin - $this->x;
					$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
				}
				$nl++;
				continue;
			}
			if (!$ascii || $c == mb_convert_encoding(' ', $enc, $this->charset))
				$sep = $i;
			$l += $ascii ? $cw[chr($ord)] : 1000;
			if ($l > $wmax) {
				//Automatic line break
				if ($sep == -1 || $i == $j) {
					if ($this->x > $this->lMargin) {
						//Move to next line
						$this->x = $this->lMargin;
						$this->y += $h;
						$w = $this->w - $this->rMargin - $this->x;
						$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
						$i++;
						$nl++;
						continue;
					}
					if ($i == $j)
						$i++; //=$ascii ? 1 : 2;
					$this->UniCell($w, $h, mb_substr($s, $j, $i - $j), 0, 2, '', 0, $link);
				} else {
					$this->UniCell($w, $h, mb_substr($s, $j, $sep - $j), 0, 2, '', 0, $link);
					$i = (mb_substr($s, $sep, 1) == mb_convert_encoding(' ', $enc, $this->charset)) ? $sep + 1 : $sep;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				if ($nl == 1) {
					$this->x = $this->lMargin;
					$w = $this->w - $this->rMargin - $this->x;
					$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
				}
				$nl++;
			} else
				$i++; //=$ascii ? 1 : 2;
		}
		//Last chunk
		if ($i != $j)
			$this->UniCell($l / 1000 * $this->FontSize, $h, mb_substr($s, $j, $i - $j), 0, 0, '', 0, $link);
	}
}
