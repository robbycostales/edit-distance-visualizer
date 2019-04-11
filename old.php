<!DOCTYPE html>

<!-- THIS FILE IS WHEN I WENT CRAZY AND DECIDED TO DO IT IN PHP INSTEAD -->

<html>

	<head>
    <title>RC - Edit Distance</title>
    <meta name="description" content="Robby Costales">

    <?php
      // HEAD STUFF
      $toHome = "../..";
      include("$toHome/com/sub-head.php");
    ?>
  </head>

  <?php
    // NAVBAR
    $navParam = "none";
    include("$toHome/com/nav.php");
  ?>

  <body>

    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <h3>EDIT DISTANCE</h3>
        <p class="lead"></p>
      </div>
    </div>



		<div id="initial-input" class="container">
      <form id="form" method="post" action="index.php">
        First string:<br>
        <input type="text" name="s1" value=""><br>
        Second string:<br>
        <input type="text" name="s2" value=""><br><br>
        <input type="submit" value="do" name="submit">
      </form>
		</div>


		<div id="another" class="container">
      <input type="submit" value="redo" name="try another!">
		</div>


		<div class = "container">

		<?php
			$GLOBALS["s1"] = "";
			$GLOBALS["s2"] = "";

			if(isset($_POST['submit'])){
				if($_POST['submit']=="do"){
					unset($_POST);
				  onDo();
					unset($GLOBALS);
				}
				else if($_POST['submit']=="redo"){
					unset($_POST);
					unset($GLOBALS);
					/* hide initial input */

					/* redirect to page */
				} else {
					echo "something went wrong!";
				}
			} else {
				unset($GLOBALS);
				unset($_POST);
				/* make  initial input visible */


				/* hide another */
			}

			// BULK OF CODE
			function onDo(){
				$GLOBALS["s1"] = $_POST["s1"];
				$GLOBALS["s2"] = $_POST["s2"];

				/* initialize 2d arrays */
				initObjects();
				/* run dynamic programming function */
				cheepestCost(strlen($GLOBALS["s1"])+1, strlen($GLOBALS["s2"])+1);

				/* create html table from dynamic programming results */
				$t1 = makeHTMLTable($GLOBALS["dpTable"], "dpTable");
				$t2 = makeHTMLTable($GLOBALS["diagCostsTable"], "diagCostsTable");

				echo $t1;
				echo "<br>";
				echo $t2;
			}


			/* Initializes objects */
			function initObjects(){
				/* table that uses strings to mark matching characters */
				$GLOBALS["diagCostsTable"] = array();
				/* table used in dynamic programming */
				$GLOBALS["dpTable"] = array();

				/* make diagCostsTable have 0s on matching, and 1s on non matching */
				/* for each row */
				for ($i = 0; $i < strlen($GLOBALS["s1"]); $i++) {
					/* for each column */
					for ($j = 0; $j < strlen($GLOBALS["s2"]); $j++){
						if ($GLOBALS["s1"][$i] == $GLOBALS["s2"][$j]){
							$GLOBALS["diagCostsTable"][$i][$j] = 0;
						} else {
							$GLOBALS["diagCostsTable"][$i][$j] = 1;
						}
					}
				}
				/* first row / column of dpTable have the strings */
				$GLOBALS["dpTable"][0][0] = "-";
				$GLOBALS["dpTable"][1][0] = "-";
				$GLOBALS["dpTable"][0][1] = "-";
				for ($i = 0; $i < strlen($GLOBALS["s1"]); $i++) {
					$GLOBALS["dpTable"][$i+2][0] = $GLOBALS["s1"][$i];
				}
				for ($i = 0; $i < strlen($GLOBALS["s2"]); $i++) {
					$GLOBALS["dpTable"][0][$i+2] = $GLOBALS["s2"][$i];
				}
			}


			/* build table from php array */
			function makeHTMLTable($array){
		    // start table
		    $html = '<table>';
		    // header row
		    $html .= '<tr>';
		    // foreach($array[0] as $key=>$value){
		    //         $html .= '<th>' . htmlspecialchars($key) . '</th>';
		    //     }
		    // $html .= '</tr>';
		    // data rows
		    foreach( $array as $key=>$value){
		        $html .= '<tr>';
		        foreach($value as $key2=>$value2){
		            $html .= '<td>' . htmlspecialchars($value2) . '</td>';
		        }
		        $html .= '</tr>';
		    }
		    // finish table and return it
		    $html .= '</table>';
		    return $html;
			}


			/////////////////////////
			// Dynamic Programming //
			/////////////////////////

			/* Diagonal cost */
			function dCost($i,$j){
				return $GLOBALS["diagCostsTable"][$i][$j];
			}


			/* Only called once */
			function cheepestCost($n, $m){
				return cost($n,$m);
			}

			/* Recursive call */
			function cost($i,$j){
				$val = 0;

				if ($i==1 and $j==1){
					/* makes (1, 1) == 0 */
					$GLOBALS["dpTable"][$i][$j] = 0;
					return 0;
				}

				/* the next ones form the "cushions" */
				if ($i==1){
					$GLOBALS["dpTable"][$i][$j] = $j-1;
					return $j-1;
				}
				if ($j==1){
					$GLOBALS["dpTable"][$i][$j] = $i-1;
					return $i-1;
				}

				if (isset($GLOBALS["dpTable"][$i-1][$j-1])){
					$costNW = $GLOBALS["dpTable"][$i-1][$j-1] +  $GLOBALS["diagCostsTable"][$i-2][$j-2];
				} else {
					$costNW = cost($i-1, $j-1) + $GLOBALS["diagCostsTable"][$i-2][$j-2];
				}
				if (isset($GLOBALS["dpTable"][$i-1][$j])){
					$costN = $GLOBALS["dpTable"][$i-1][$j] + 1;
				} else {
					$costN = cost($i-1,$j)+1;
				}
				if (isset($GLOBALS["dpTable"][$i][$j-1])){
					$costW = $GLOBALS["dpTable"][$i][$j-1] + 1;
				} else {
					$costW = cost($i,$j-1)+1;
				}

				$val = min($costN, $costW, $costNW);

				$GLOBALS["dpTable"][$i][$j] = $val;
				return $val;
			}
			?>

		</div>

  </body>


  <?php
    // FOOTER
    include("$toHome/com/footer.php");
  ?>

</html>
