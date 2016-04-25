<p>Rock paper scissors</p>

<html lang="en">
    
<head>
    <meta charset="utf-8">
    <?php echo $this->Html->script('jquery-1.11.0.min.js');?>
    <?php echo $this->Html->css('bootstrap.css');?>
    
    <title>Rock paper scissors</title>
</head>
<body>
    
    
    <div id="page-wrapper">
        <h1>Rock paper scissors</h1>
            <div>
                <h4>Select tournament file</h4>
		<span class="btn btn-primary">
                    <input type="file" id="fileInput">
                </span>
            </div>
            <pre id="fileDisplayArea">
                <h4>Tournament players and strategies</h4>
            <pre>
    </div>
    
    <button id="showGame" class="btn btn-primary">Solve tournament</button>
    <button id="clearDatabase" class="btn btn-primary">Clear database</button>
    
    <pre id="result">
        <h4>Tournament result will be displayed here</h4>
    <pre>
    
</body>

<script type="text/javascript">
    
    var fileContents = "";
    var fileInput = document.getElementById('fileInput');
    var fileDisplayArea = document.getElementById('fileDisplayArea');
    var buttonPressed = 0;
    
    fileInput.addEventListener('change', function(e) {
        var file = fileInput.files[0];
        var textType = /text.*/;
        if (file.type.match(textType)) {
            var reader = new FileReader();
            reader.onload = function(e) {
                fileDisplayArea.innerText = reader.result;
                fileContents = reader.result;
                console.log(fileContents);
                buttonPressed = 1;
            }
            reader.readAsText(file);	
	} else {
            fileDisplayArea.innerText = "File not supported!";
        }           
    });
       
    
 $(document).ready(function() {
     
     
    $('#showGame').click(function(event){
        if (buttonPressed==1)
        {
            var checkGame = "<?php echo $this->Url->build(["controller" => "RPS","action" => "processTournament"]);?>/"+fileContents;
            $.post(checkGame, function (data) {
                console.log(data);
                $( "#result" ).html( data );
            });
            buttonPressed = 0;
        }
        else
        {
            alert("Please select tournamnet file");
            
        }
        
        
    });
    
    $('#clearDatabase').click(function(event){
        var clearDB = "<?php echo $this->Url->build(["controller" => "RPS","action" => "clearDatabase"]);?>";
        $.post(clearDB, function (data) {
            console.log(data);
            $( "#result" ).html( data );
        });
    });
});

</script>
</html>
