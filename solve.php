<html><body>
<h1>Results:</h1>
<?
$letterArr = $_SERVER['QUERY_STRING'];
?>
<link rel='stylesheet' href='ugly.css' type='text/css'/>
<center>
 <table background='Board.png' style='border=2px;'>
			<tbody>
				<tr>
					<td class='upper right' id='td00'><? echo $letterArr[0]; ?></td>
					<td class='upper' id='td01'><? echo $letterArr[1]; ?></td>
					<td class='upper mid' id='td02'><? echo $letterArr[2]; ?></td>
					<td class='upper left' id='td03'><? echo $letterArr[3]; ?></td>
				</tr>
				<tr>
					<td class='right' id='td10'><? echo $letterArr[4]; ?></td>
					<td id='td11'><? echo $letterArr[5]; ?></td>
					<td class='mid' id='td12'><? echo $letterArr[6]; ?></td>
					<td class='left' id='td13'><? echo $letterArr[7]; ?></td>
				</tr>
				<tr>
					<td class='right' id='td20'><? echo $letterArr[8]; ?></td>
					<td id='td21'><? echo $letterArr[9]; ?></td>
					<td class='mid' id='td22'><? echo $letterArr[10]; ?></td>
					<td class='left' id='td23'><? echo $letterArr[11]; ?></td>
				</tr>
				<tr>
					<td class='lower right' id='td30'><? echo $letterArr[12]; ?></td>
					<td class='lower' id='td31'><? echo $letterArr[13]; ?></td>
					<td class='lower mid' id='td32'><? echo $letterArr[14]; ?></td>
					<td class='lower left' id='td33'><? echo $letterArr[15]; ?></td>
				</tr>
			</tbody>
		</table>

<?
$words = array();
$stems = array();

$dict = file("dict.txt");

foreach($dict as $line)
{
  $word = trim($line);
  $words[$word] = true;

  for($i=1; $i<=strlen($word); $i++)
  {
    $sub = substr($word,0,$i);
    $stems[$sub] = true;
  }
}

$letters = strtolower($_SERVER['QUERY_STRING']);

$H = 4;
$W = 4;

$k=0;
$board = array();
$used = array();
for($i=0;$i<$H;$i++)
{
    $board[$i] = array();
    $used[$i] = array();
    for($j=0;$j<$W;$j++)
    {
        $board[$i][$j] = $letters[$k];
        $k+=1;
        
        $used[$i][$j] = false;
    }
}

$found = array();

function solve($h, $w, $word = '')
{
  global $board, $used, $stems, $words, $found;
  
  $used[$h][$w] = true;
  $word .= $board[$h][$w];

  if (strlen($word) > 2 && $words[$word])
  {
    $found[$word] = true;
  }
  if ($stems[$word])
  {
    for($i=-1; $i<=1; $i++)
    {
        for($j=-1; $j<=1; $j++)
        {
          //echo "$word $i $j";

          $y = $h+$i;
          $x = $w+$j;
          //echo "<br>$word $x $y";
          if($y < 0 || $y >= 4) continue;
          if($x < 0 || $x >= 4) continue;
          if($used[$y][$x]) continue;
          solve($y, $x, $word);
        }
    }
  }

  $used[$h][$w] = false;
}

for($i=0;$i<$H;$i++)
{
    for($j=0;$j<$W;$j++)
    {
        solve($i,$j, '');
    }
}


function lensort($a,$b){
    if(strlen($b)==strlen($a))
    {
        return strcmp($a,$b);
    }
    return strlen($b)-strlen($a);
}
$found = array_keys($found);
usort($found,'lensort');
?>

<textarea id='results' rows='7' cols='40'>
<? echo implode(" ",$found); ?>
</textarea><br/>
<a href='index.html'>New Game</a>
</center>
</body></html>