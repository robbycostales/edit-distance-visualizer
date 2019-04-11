<!DOCTYPE html>

<html>

	<head>
    <title>Edit Distance Visualizer</title>
    <meta name="description" content="Robby Costales">


    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script
      src="https://code.jquery.com/jquery-3.3.1.js"
      integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <!-- font -->
    <link href="https://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet">

    <!-- Add icon library -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/all.css" integrity="sha384-5SOiIsAziJl6AWe0HWRKTXlfcSHKmYV4RBF18PPJ173Kzn7jzMyFuTtk8JA7QQG1" crossorigin="anonymous">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- specific css sheet -->
    <link rel="stylesheet" type="text/css" href="style.css">

  </head>

  <body>

    <div class="jumbotron">
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

</html>
