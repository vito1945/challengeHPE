<p>Rock paper scissors</p>

<html lang="en">
    
<head>
    <meta charset="utf-8">
    <?php echo $this->Html->script('jquery-1.11.0.min.js');?>
    <title>Rock paper scissors</title>
</head>
<body>
    <div id="page-wrapper">
        <h1>Rock paper scissors</h1>
            <div>
                Select a text file: 
		<input type="file" id="fileInput">
            </div>
            <pre id="fileDisplayArea"><pre>
    </div>
    
    <div id="page-wrapper">
        <div>
            Tournament file: 
            <input type="file" id="tournamentFile">
        </div>
    </div>
    
    
    <button id="showGame">Show game</button>
    <a href="<?php echo $this->Url->build(["controller" => "RPS","action" => "clearDatabase"]);?>">Clear database</a>
    <!--<button id="clearDatabase" >Clear database</button>-->
    
</body>

<script type="text/javascript">
    
    var fileContents = "";
    var fileInput = document.getElementById('fileInput');
    var fileDisplayArea = document.getElementById('fileDisplayArea');
    
    fileInput.addEventListener('change', function(e) {
        var file = fileInput.files[0];
        var textType = /text.*/;
        if (file.type.match(textType)) {
            var reader = new FileReader();
            reader.onload = function(e) {
                fileDisplayArea.innerText = reader.result;
                fileContents = reader.result;
                console.log(fileContents);
            }
            reader.readAsText(file);	
	} else {
            fileDisplayArea.innerText = "File not supported!";
        }           
    });
       
    
 $(document).ready(function() {
     
     
    $('#showGame').click(function(event){
        //var checkGame = "<?php /*echo $this->Url->build(["controller" => "RPS","action" => "evaluateGame"]);*/?>/"+fileContents;
        var checkGame = "<?php echo $this->Url->build(["controller" => "RPS","action" => "processTournament"]);?>/"+fileContents;
        $.post(checkGame, function (data) {
            console.log(data);
            //var json = JSON.parse(data);
            //console.log(json);
        });
        //console.log("PUM"+fileContents);    
    }); 
});

</script>
</html>
