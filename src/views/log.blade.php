<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel log viewer</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.css">



    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
      body {
        padding: 25px;
      }
      h1 {
        font-size: 1.5em;
        margin-top: 0px;
      }
    </style>
  </head>
  <body>
    <div class="container-fluid"">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <h1><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Laravel Log Viewer</h1>
          <p class="text-muted"><i>by Rap2h</i></p>
        </div>
        <div class="col-sm-9 col-md-10">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Level</th>
                <th>Date</th>
                <th>Content</th>
              </tr>
            </thead>
            <tbody>
              @foreach($logs as $log)
                <tr>
                  <td class="text-{{{$log['level_class']}}}"><span class="glyphicon glyphicon-{{{$log['level_img']}}}-sign" aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
                  <td>{{{$log['date']}}}</td>
                  <td>
                    {{{$log['text']}}}
                    @if (isset($log['in_file']))
                      <br />{{{$log['in_file']}}}
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.js"></script>
    <script>
      $(document).ready(function(){
        $('table').DataTable({
          "order": [ 1, 'desc' ]
        });
      });
    </script>
  </body>
</html>