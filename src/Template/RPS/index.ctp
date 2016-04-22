<p>Rock paper scissors</p>

<input type="file" id="file" name="file" enctype="multipart/form-data" />

<script type="javascript">
       document.getElementById('file').addEventListener('change', readFile, false);

       function readFile (evt) {
           var files = evt.target.files;
           var file = files[0];           
           var reader = new FileReader();
           reader.onload = function(e) {
             console.log(e);            
           }
           reader.readAsText(file);
        }
        
        
        /*var fr = new FileReader();
fr.onload = function(e) {
    // e.target.result should contain the text
};
fr.readAsText(file);*/
 </script>