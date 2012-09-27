<?php

$letterArr = $_SERVER['QUERY_STRING'];

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
    return strlen($a)-strlen($b);
}
$found = array_keys($found);
usort($found,'lensort');
?>

<html>
<head>
<title>Kindoggle</title>
<script type='text/javascript'>
found_words = [ <? echo "'".implode("','", $found)."'"; ?> ];

var diceValues = new Array("PCHOAS", "OATTOW", "LRYTTE", "VTHRWE", 
                           "EGHWNE", "SEOTIS", "ANAEEG", "IDSYTT", 
                           "MTOICU", "AFPKFS", "XLDERI", "ENSIEU", 
                           "YLDEVR", "ZNRNHL", "NMIQHU", "OBBAOJ");

var gameBoard;
var boardStr= "";
var prevWord = "";

function Dice(values) {
    this.values = values;
    var random = Math.floor(Math.random() * 6);
    this.value = values.substring(random, random+1);
}
function Board(diceArray) {

    boardStr = "";

    this.dice = new Array(
        new Array(diceArray[0], diceArray[1], diceArray[2], diceArray[3]),
        new Array(diceArray[4], diceArray[5], diceArray[6], diceArray[7]),
        new Array(diceArray[8], diceArray[9], diceArray[10], diceArray[11]),
        new Array(diceArray[12], diceArray[13], diceArray[14], diceArray[15])
    );
    
    for(i = 0; i < 100; i++) {
        var fromX = Math.floor(Math.random() * 4);
        var fromY = Math.floor(Math.random() * 4);
        var toX = Math.floor(Math.random() * 4);
        var toY = Math.floor(Math.random() * 4);
        var temp = this.dice[fromX][fromY];
        this.dice[fromX][fromY] = this.dice[toX][toY];
        this.dice[toX][toY] = temp; 
    }
    
    for (i = 0; i < 4; i++) 
        for (j = 0; j < 4; j++) 
            boardStr += this.dice[i][j].value;
};

function CreateBoard() {
    var diceArray = new Array();
    for(i = 0; i < diceValues.length; i++)
        diceArray[i] = new Dice(diceValues[i]);
    gameBoard = new Board(diceArray);
}

function playGame() 
{  
    CreateBoard();
    window.location = "solitaire.php?" + boardStr;
}

function checkword()
{
  wordIn = document.getElementById('guessIn').value;
  if(wordIn == "")
    return;
	
  if(wordIn.indexOf(".") != -1)
  {
    document.getElementById('guessIn').value = prevWord;
    return;
  }
	
  wordIn = wordIn.toLowerCase();
  
  //draw words
  answer_text = "";
    for(var x = 0; x < found_words.length; x++) {
      var word = found_words[x];
	  if(word == wordIn && !found_words[word])
	  {
		found_words[word] = true;
		prevWord = wordIn;
		document.getElementById('guessIn').value = "";
	  }

	  if(found_words[word])
		answer_text += " " + word + ", ";
	  else
	  {
		answer_text += "";
		var i = 0;
		for(i = 0; i < word.length; i++)
		  answer_text += "=";
		answer_text += ", ";
	  }
	  document.getElementById("results").innerHTML = answer_text;
	}
}

function SeeAnswers() {
  answer_text = "";
  for(var x = 0; x < found_words.length; x++) {
    var word = found_words[x];
    found_words[word] = true;
    if(found_words[word])
      answer_text += " " + word + ", ";

    document.getElementById("results").innerHTML = answer_text;
  }
}

</script>
<link rel='stylesheet' href='ugly.css' type='text/css'/>
</head>
<body>
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


<div class="smallerText">
Enter a word: <input class="smallerText" type='text' size='16' id='guessIn' onkeyup='checkword()' />
</div>


<div id='results' class='smallerText' width='580px'>
<script>
for(var x = 0; x < found_words.length; x++) {
    var word = found_words[x];
    for(i = 0; i < word.length; i++)
        document.write("=");
    document.write(" ");
}
</script>
</div>
<a href='#' onclick='playGame();'>New Game</a><br/>
<input type='button' class='smallerText' onclick='SeeAnswers()' value='See Answers'/>
</center>
<script>
    if(found_words.length <= 1)
        playGame();
</script>
</body>
</html>