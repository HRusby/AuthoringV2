<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Test</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="./JS/slipTreeTokenField/dist/bootstrap-tokenfield.min.js"></script>
		<link rel="stylesheet" href="./JS/slipTreeTokenField/dist/css/bootstrap-tokenfield.css"/>

  </head>
  <body>
    <input type="text" class="form-control" id="test" value="blue,red" />
    <script>
        $().ready(function(){
          $('#test').tokenfield('setTokens', ['blue','red','white']);
        });
    </script>
  </body>
</html>
