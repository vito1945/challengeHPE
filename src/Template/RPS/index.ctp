<p>Rock paper scissors</p>

<html lang="en">
    
<head>
    <meta charset="utf-8">
    <?php echo $this->Html->script('jquery-1.11.0.min.js');?>
    <?php echo $this->Html->script('jquery-ui.min.js');?>
    <?php echo $this->Html->css('bootstrap.css');?>
    <?php echo $this->Html->css('jquery-ui.css');?>
    
    <title>Rock paper scissors</title>
</head>
<body>
    
    
    <div id="page-wrapper">
        <h1>Rock paper scissors</h1>
        
        <button id="about" class="btn btn-primary">About the creator of this challenge</button>
        
            <div>
                <h4>Select tournament file</h4>
		<span class="btn btn-primary">
                    <input type="file" id="fileInput">
                </span>
                <a href="http://challengehpe-evitoria.fastcomet.host/challengehpe-evitoria.fastcomet.host/vito/TournamentFiles/">Download tournament files</a>
            </div>
            <br>
            <button id="twoPlayerButton" class="btn btn-primary">Open two player mode</button>
            <br>
            
            <div id="twoPlayerPanel">
                <p>Two player mode</p>
                <label>Player 1</label>
                <input type="text" id="namePlayer1">
                <select id="strategyPlayer1">
                    <option value="R">Rock</option>
                    <option value="P">Paper</option>
                    <option value="S">Scissors</option>
                </select>
                <br>
                
                <label>Player 2</label>
                <input type="text" id="namePlayer2">
                <select id="strategyPlayer2">
                    <option value="R">Rock</option>
                    <option value="P">Paper</option>
                    <option value="S">Scissors</option>
                </select>
                <br>
                <button id="resolveTwoPlayerMatch" class="btn btn-primary">Play</button>
                <button id="hideTwoPlayerMatch" class="btn btn-primary">Hide two player mode</button>
            </div>
            
            <pre id="fileDisplayArea">
                <h4>Tournament players and strategies</h4>
            <pre>
    </div>
    
    
    
    <button id="showGame" class="btn btn-primary">Solve tournament</button>
    <button id="clearDatabase" class="btn btn-primary">Clear database</button>
    
    <pre id="result">
        <h4>Tournament result will be displayed here</h4>
    </pre>
    
<div id="dialog" title="About this challenge">
    <p>
        This challenge for HPE was created by Eduardo Vitoria on April 2016.<br><br>
        
        The backend of this program was created using the PHP programming language (Version 5.5.12)
        with the CakePHP 3 framework. The language was selected because its widely used, have lot's of documentation,
        a huge support community and it's free. 
        The CakePHP3 framework was used to help the program having an
        structure (MVC in this case), and to take advantage of the elements provided by the framework
        such as ORM and helpers for various tasks. This framework was choosen also because its quick
        to start working with it, other PHP frameworks as Laravel or Symfony require lots of configurations
        before they can be used.
        <br><br>
        
        The frontend was created with HTML 5, CakePHP 3 templating system ,Javascript (with the JQuery framework) and Twitter bootstrap'
        framework for the look and feel of the site. AJAX call's were used to present all the data in a single page.
        
        <br><br>
        
        The used database was MySQL, due to its easy integration with the PHP programming language.
        
        <br><br>
        
        REST Web services were created using the CakePHP 3 Restful API
        
        <br><br>
        
        The used IDE was NetBeans 8.0.2 with the CakePHP plugin
        
        
    </p>
</div>

</body>

<script type="text/javascript">
    
    $(function() {
      $( "#dialog" ).dialog({
        width : 1000, 
        height: 500,
        autoOpen: false,
        show: {
          effect: "blind",
          duration: 500
        },
        hide: {
          effect: "explode",
          duration: 500
        }
      });

      $( "#about" ).click(function() {
        $( "#dialog" ).dialog( "open" );
      });
    });
    
    var fileContents = "";
    var fileInput = document.getElementById('fileInput');
    var fileDisplayArea = document.getElementById('fileDisplayArea');
    var buttonPressed = 0;
    
    fileInput.addEventListener('change', function(e) {
        buttonPressed = 1;
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
    
    $('#twoPlayerPanel').hide();
     
     
     $('#twoPlayerButton').click(function(event){
        $('#twoPlayerPanel').show();
     });
    
    $('#hideTwoPlayerMatch').click(function(event){
        $('#twoPlayerPanel').hide();
     });
     
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
            alert("Please select tournament file");
            
        }
        
        
    });
    
    $('#clearDatabase').click(function(event){
        var clearDB = "<?php echo $this->Url->build(["controller" => "RPS","action" => "clearDatabase"]);?>";
        $.post(clearDB, function (data) {
            console.log(data);
            $( "#result" ).html( data );
        });
    });
    
    $('#resolveTwoPlayerMatch').click(function(event){
        var namePlayer1 = $("#namePlayer1").val();
        var strategyPlayer1 = $('#strategyPlayer1 :selected').val();
        var namePlayer2 = $("#namePlayer2").val();
        var strategyPlayer2 = $('#strategyPlayer2 :selected').val();
        
        var solveMatch = "<?php echo $this->Url->build(["controller" => "RPS","action" => "evaluateTwoPlayerMatch"]);?>/"+namePlayer1+"/"+strategyPlayer1+"/"+namePlayer2+"/"+strategyPlayer2;
        $.post(solveMatch, function (data) {
            //console.log(data);
            $( "#result" ).html( data );
        });
    });
    
    
});

</script>
</html>
