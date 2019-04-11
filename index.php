<!DOCTYPE html>

<html>

	<head>
    <title>Edit Distance Visualizer</title>
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
    include("$toHome/com/nav-min.php");
  ?>

  <body>

    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <h3>EDIT DISTANCE VISUALIZER</h3>
      </div>
    </div>


    <body>

			<div class="t-container">
				<span id="form1c">
		      <form id="form1">
		        <p>First string:</p>
		        <input type="text" name="s1" value="firststring"><br>
						<p>Second string:</p>
		        <input type="text" name="s2" value="secondstring"><br><br>
		        <button id="submit"> Compute </button>
		      </form>
				</span>

				<span id="form2c">
					<form id="form2">
		        <button id="back"> Try Another! </button>
		      </form>
				</span>
			</div>


			<div class="t-container">
				<p id="distance"><p>
			</div>

			<div id="dpTable" class="t-container">
			</div>

    </body>

    <script type="text/javascript">
			var s1;
			var s2;
			var diagCostsTable;
			var dpTable;
			var dirTable;

			document.getElementById('form1c').style.display= 'inline' ;
			document.getElementById('form2c').style.display= 'none' ;

			$("#back").click(function(){
				/* true to clear cache */
				location.reload(true);
			});

			$("#submit").click(function(e){
				e.preventDefault();
				/* change visibilities */

				document.getElementById('form1c').style.display= 'none' ;
				document.getElementById('form2c').style.display= 'inline' ;

				var input = $("#form1").serializeArray();

				s1 = String(input[0].value).toUpperCase();
				s2 = String(input[1].value).toUpperCase();

				diagCostsTable = new Array(s1.length).fill(null).map(()=>new Array(s2.length).fill(null));

				dpTable = new Array(s1.length+2).fill(null).map(()=>new Array(s2.length+2).fill(null));

				dirTable = new Array(s1.length+2).fill(null).map(()=>new Array(s2.length+2).fill(""));


				/* initialize 2d arrays */
				initObjects(s1, s2);

				/* run dynamic programming function */
				cheepestCost(s1.length+1, s2.length+1);

				var t = makeTableHTML();
				document.getElementById("distance").innerHTML = "Edit distance = " + String(t[1]);
				document.getElementById("dpTable").innerHTML = t[0];
			});


			/* returns instead of creating */
			function makeTableHTML() {
			    var result = "<div align='center'><table>";
			    for(var i=0; i<dpTable.length; i++) {
			        result += "<tr>";
			        for(var j=0; j<dpTable[i].length; j++){
									if (dpTable[i][0] != null){
											/* set class */
											var cls = "";
											if (i >= 2 && j >=2){
													if (diagCostsTable[i-2][j-2] == 0){
														cls += "diag-cost ";
													}
											}
											if (i == 0 || j == 0){
												cls += "strings ";
											}
											if (i == s1.length+1 && j == s2.length+1){
												cls += "final ";
												var distance = dpTable[i][j];
											}
											result += "<td class = '"+cls+"'>"+dirTable[i][j]+" "+dpTable[i][j]+"</td>";
									}
			        }
			        result += "</tr>";
			    }
			    result += "</div></table>";
			    return [result, distance];
			}


			/* Initializes objects */
			function initObjects(s1, s2){
				/* make diagCostsTable have 0s on matching, and 1s on non matching */
				/* for each row */
				for (var i =0; i < s1.length; i++) {
					/* for each column */
					for (var j = 0; j < s2.length; j++){
						if (s1[i] == s2[j]){
							diagCostsTable[i][j] = 0;
						} else {
							diagCostsTable[i][j] = 1;
						}
					}
				}
				/* first row / column of dpTable have the strings */
				dpTable[0][0] = "-";
				dpTable[1][0] = "-";
				dpTable[0][1] = "-";
				for (var i = 0; i < s1.length; i++) {
					dpTable[i+2][0] = s1[i];
				}
				for (var i = 0; i < s2.length; i++) {
					dpTable[0][i+2] = s2[i];
				}
			}


			/////////////////////////
			// Dynamic Programming //
			/////////////////////////


			/* Diagonal cost */
			function dCost(i,j){
				return diagCostsTable[i][j];
			}


			/* Only called once */
			function cheepestCost(n, m){
				return cost(n,m);
			}


			/* Recursive call */
			function cost(i,j){

				if (i==1 && j==1){
					/* makes (1, 1) == 0 */
					dpTable[i][j] = 0;
					return 0;
				}
				/* the next ones form the "cushions" */
				if (i==1){
					dpTable[i][j] = j-1;
					return j-1;
				}
				if (j==1){
					dpTable[i][j] = i-1;
					return i-1;
				}

				/* set values */
				if (typeof dpTable[i-1][j-1] == 'object') {
    			var costNW = cost(i-1, j-1) + diagCostsTable[i-2][j-2];
				} else {
					var costNW = dpTable[i-1][j-1] + diagCostsTable[i-2][j-2];
				}

				if (typeof dpTable[i-1][j] == 'object') {
    			var costN = cost(i-1, j) + 1;
				} else {
					var costN = dpTable[i-1][j] + 1;
				}

				if (typeof dpTable[i][j-1] == 'object') {
    			var costW = cost(i, j-1) + 1;
				} else {
					var costW = dpTable[i][j-1] + 1;
				}

				var dir = ""
				/* keep track of which was the minimum (not using Math.min because need to store arrows!) */
				if (costN <= costNW && costN <= costW){
					val = costN;
					dir += "↓";
				} else if (costW <= costN && costW <= costNW) {
					val = costW;
					dir += "→";
				} else {
					val = costNW;
					dir += "➘";
				}

				// var val = Math.min(costN, costW, costNW);

				dirTable[i][j] = dir;
				dpTable[i][j] = val;
				return val;

			}

    </script>

  </body>


  <?php
    // FOOTER
    include("$toHome/com/footer-min.php");
  ?>

</html>
