<!DOCTYPE html>
<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>Matrix Chain Multiplication</title>
<style>
	form
	{
		margin:auto;
	}
	
	input
	{
		display:inline-block;
		text-align:center;
		margin:auto;
	}
	
	.attr
	{
		text-align:center;
		margin:auto;
		display:block;
	}
	
	#title
	{
		text-align:center;
		display:block;
		margin:auto;
		padding-top:2.5%;
		padding-bottom:1%;
	}
	
	table
	{
		display:inline;
		margin:auto;
		padding-left:2.5%;
		padding-right:2.5%;
	}
	
	th
	{
		font-weight:bold;	
	}	
	
	td, th
	{
		text-align:center;	
		font-size:1.5em;
		width:50px;
	}

</style>

<?php
	if(isset($_GET['reset']))
	{
		header("Location: matrixchain.php");
	}
	
	//find least required multiplications
	function matrixChain($dim)
	{
		//reformat dimensions for easier calculations
		$d = array();
		$c = count($dim);
		for($i = 0; $i < $c; $i++)
		{array_push($d, $dim[$i][0]);}
		array_push($d, $dim[$c-1][1]);
		
		//initialize arrays with 0s
		$splitIndex = array();
		$cost = array();
		for($i = 0; $i < $c; $i++)
		{
			$insert = array();
			for($j = 0; $j < $c; $j++)
			{
				array_push($insert, 0);
			}
			array_push($cost, $insert);
			array_push($splitIndex, $insert);
		}
		
		$n = count($d);
		
		for($len = 2; $len < $n; $len++)
		{
			for($i = 1; $i < $n - $len + 1; $i++)
			{
				$j = $i + $len - 1;
				$cost[$i][$j] = PHP_INT_MAX;
				
				for($k = $i; $k < $j; $k++)
				{
					$result = $cost[$i][$k] + $cost[$k+1][$j] + ($d[$i-1] * $d[$k] * $d[$j]);
					if($result < $cost[$i][$j]) //if this path is < already-existing one
					{
						$cost[$i][$j] = $result; //insert new minimum
						$splitIndex[$i][$j] = $k; //record index
					}
				}
			}
		}
		
		$final = $cost[1][$n-1]; //final result ***SHOULD*** be in top-right cell of matrix
		
		echo("<div>");
		printMatrix($cost, $n, "Cost Matrix");
		printMatrix($splitIndex, $n, "Split-Index Matrix");
		echo("</div>");
		
		return $final;
	}
	
	
	//output matrix to table
	function printMatrix($arr, $n, $title)
	{		
		$colspan = $n-1;
		echo("<table>");
		echo("<tr><th colspan = '$colspan'>$title</th></tr>");
		
		for($i = 1; $i < $n; $i++)
		{
			echo("<tr>");
			for($j = 1; $j < $n; $j++)
			{
				echo("<td>");
				
				if($arr[$i][$j] == 0)//check for blanks in array (PHP sometimes changes 0s to blanks during output)
				{echo("0");}
				else
				{echo($arr[$i][$j]);}
		
				echo("</td>");
			}
			echo("</tr>");
		}
		echo("</table>");
	}
	
	

?>

</head>

<body>
	<div id="title">
		<h2>Matrix Chain Multiplication</h2>
	</div>
	
	<div style="margin:auto; text-align:center;">	
	
		<?php
			$self = htmlspecialchars($_SERVER["PHP_SELF"]);
		
			if(!isset($_GET['mCount']))
			{
				echo("<form name = 'getcount' method = 'get' action = '$self'>");
				echo("<input name = 'mCount' type = 'number' min = '1' placeholder = 'Enter Matrix Count' required = 'required'/>");
				echo("<input type = 'submit' value = 'Submit'/>");
				echo("</form>");
			}
			
			else if(!isset($_GET['result']))
			{
				$count = $_GET['mCount'];
				
				echo("<form name = 'getsize' method = 'get' action = '$self'>");
				echo("<input type = 'hidden' name = 'result' value = 'valid'>");
				echo("<input type = 'hidden' name = 'mCount' value = '$count'/>");
				
				for($i = 0; $i < $count; $i++)
				{
					$n = $i+1;
					echo("<div class = 'attr'>");
					echo("<input type = 'number' name = 'R$i' min = '1' placeholder = 'Matrix $n Rows' required = 'required'/>");
					echo("<input type = 'number' name = 'C$i' min = '1' placeholder = 'Matrix $n Columns' required = 'required'/>");
					echo("</div>");
				}
				echo("<div class = 'attr'>");
				echo("<input type='reset' value='Reset' name='reset'/>");
				echo("<input type = 'submit' value = 'Enter'/>");
				echo("</div>");
				echo("</form>");
			}
			
			else
			{	
				$count = $_GET['mCount'];
				$dimensions = array();
				
				for($i = 0; $i < $count; $i++)
				{
					array_push($dimensions, array($_GET["R$i"], $_GET["C$i"]));
				}
				
				
				$c = count($dimensions);
				$valid = true;
				for($i = 1; $i < $c; $i++)
				{				
					if($dimensions[$i][0] != $dimensions[$i-1][1])
					{
						echo("<br/>Invalid Matrix Dimensions. Refreshing...");
						header("Refresh:2; url=matrixchain.php");
						$valid = false;
					}
				}
				
				if($valid == true)
				{
					$min = matrixChain($dimensions);
					
					echo("<h2>Minimum Multiplications: <strong>$min</strong></h2>");
				
					echo("<div class = 'attr'>");
					echo("<form name = 'return' method = 'get' action = '$self'>");
					echo("<input type='submit' value='Restart' name='reset'/>");
					echo("</form>");
					echo("</div>");
				}
			}
		?>
	</div>


</body>

</html>
