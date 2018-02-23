<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel log viewer</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
  <link rel="stylesheet"
        href="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.css">


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
      margin-top: 0;
    }

    .stack {
      font-size: 0.85em;
    }

    .date {
      min-width: 75px;
    }

    .text {
      word-break: break-all;
    }

    a.llv-active {
      z-index: 2;
      background-color: #f5f5f5;
      border-color: #777;
    }

    .list-group-item {
      word-wrap: break-word;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-3 col-md-2 sidebar">
      <h1><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Laravel Log Viewer</h1>
      <p class="text-muted"><i>by Rap2h</i></p>
      <div class="list-group">
        @foreach($files as $file)
          <a href="?l={{ base64_encode($file) }}"
             class="list-group-item @if ($current_file == $file) llv-active @endif">
            {{$file}}
          </a>
        @endforeach
      </div>
    </div>
    <div class="col-sm-9 col-md-10 table-container">
      @if ($logs === null)
        <div>
          Log file >50M, please download it.
        </div>
      @else
        <table id="table-log" class="table table-striped">
          <thead>
          <tr>
            <th>Level</th>
            <th>Context</th>
            <th>Date</th>
            <th>Content</th>
          </tr>
          </thead>
          
          <tfoot>
          <tr>
              <th>Level</th>
              <th>Context</th>
              <th>Date</th>
              <th>&nbsp;</th>
          </tr>
          </tfoot>
          
          <tbody>

          @foreach($logs as $key => $log)
            <tr data-display="stack{{{$key}}}">
              <td class="text-{{{$log['level_class']}}}"><span class="glyphicon glyphicon-{{{$log['level_img']}}}-sign"
                                                               aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
              <td class="text">{{$log['context']}}</td>
              <td class="date">{{{$log['date']}}}</td>
              <td class="text">
                @if ($log['stack']) <a class="pull-right expand btn btn-default btn-xs"
                                       data-display="stack{{{$key}}}"><span
                      class="glyphicon glyphicon-search"></span></a>@endif
                {{{$log['text']}}}
                @if (isset($log['in_file'])) <br/>{{{$log['in_file']}}}@endif
                @if ($log['stack'])
                  <div class="stack" id="stack{{{$key}}}"
                       style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}
                  </div>@endif
              </td>
            </tr>
          @endforeach

          </tbody>
        </table>
      @endif
      <div>
        @if($current_file)
          <a href="?dl={{ base64_encode($current_file) }}"><span class="glyphicon glyphicon-download-alt"></span>
            Download file</a>
          -
          <a id="delete-log" href="?del={{ base64_encode($current_file) }}"><span
                class="glyphicon glyphicon-trash"></span> Delete file</a>
          @if(count($files) > 1)
            -
            <a id="delete-all-log" href="?delall=true"><span class="glyphicon glyphicon-trash"></span> Delete all files</a>
          @endif
        @endif
      </div>
    </div>
  </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script>
  $(document).ready(function () {
    $('.table-container tr').on('click', function () {
      $('#' + $(this).data('display')).toggle();
    });
    var table = $('#table-log').DataTable({
      "order": [1, 'desc'],
      "stateSave": true,
      "stateSaveCallback": function (settings, data) {
        window.localStorage.setItem("datatable", JSON.stringify(data));
      },
      "stateLoadCallback": function (settings) {
        var data = JSON.parse(window.localStorage.getItem("datatable"));
        if (data) data.start = 0;
        return data;
      }
    });
    $('#delete-log, #delete-all-log').click(function () {
      return confirm('Are you sure?');
    });
    
    ///////////////////////////////////////////////////////////////
    // filter columns
    ///////////////////////////////////////////////////////////////
    var dates = [];
    $("#table-log tfoot th:not(:last)").each(function (i) {
        var select = $('<select style="width: 100%;"><option value=""></option></select>')
            .appendTo($(this).empty())
            .on('change', function () {
                table.column(i)
                    .search($(this).val(), true, false)
                    .draw();
            });
        table.column(i).data().unique().sort().each(function (d, j) {
            var val = d;
            // remove html in case of first/type column
            if (i === 0) {
                val = $.trim($('<div>').html(d).text());
            }
            // remove time in case of date column
            else if (i === 2) {
                val = d.split(' ')[0];
                if (jQuery.inArray(val, dates) !== -1) {
                    // continue
                    return true;
                }
                dates.push(val);
                // we will populate date column later with dates in descending order
                return true;
            }
            select.append('<option value="' + val + '">' + val + '</option>')
        });
    });
    
    // populate dates select box
    $(dates).sort(function (a, b) {
        return a > b ? -1 : a < b ? 1 : 0;
    }).each(function (i, v) {
        $('#table-log tfoot select:eq(2)').append('<option value="' + v + '">' + v + '</option>')
    });
    
    // put filters on header
    $('#table-log tfoot').css('display', 'table-header-group');
    ///////////////////////////////////////////////////////////////
        
  });
</script>
</body>
</html>
