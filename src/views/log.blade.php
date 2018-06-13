<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Laravel log viewer</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">


  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style>
    /** body {
      padding: 25px;
    } */

    h1 {
      font-size: 1.5em;
      margin-top: 0;
    }

    #table-log {
        font-size: 0.85rem;
    }

    .sidebar {
        font-size: 0.85rem;
        line-height: 1;
    }

    .btn {
        font-size: 0.7rem;
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
    .navbar-nav li {
        margin-right: 10px;
    }
    .navbar-nav li a {
        color: #ccc;
    }
    .navbar-nav li.active a {
        color: #fff;
    }

    .log-contents {
        padding-top: 15px;
    }

    .list-group-item {
      word-wrap: break-word;
    }
  </style>
</head>
<body>

<nav class="bg-primary text-light navbar-dark navbar navbar-expand-lg">
    <a class="navbar-brand text-light" href="https://github.com/rap2hpoutre/laravel-log-viewer">Laravel Log Viewer</a>
    <button class="border-light navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            @foreach($files as $file)
                <li class="nav-item @if ($current_file == $file) active @endif">
                    <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}" class="nav-link">
                        {{$file}}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
<div class="container-fluid log-contents">
  <div class="row">
    <div class="col-12 table-container">
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
          <tbody>

          @foreach($logs as $key => $log)
            <tr data-display="stack{{{$key}}}">
              <td class="text-{{{$log['level_class']}}}"><span class="fa fa-{{{$log['level_img']}}}"
                                                               aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
              <td class="text">{{$log['context']}}</td>
              <td class="date">{{{$log['date']}}}</td>
              <td class="text">
                @if ($log['stack']) <button type="button" class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
                                       data-display="stack{{{$key}}}"><span
                      class="fa fa-search"></span></button>@endif
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
      <div class="p-3">
        @if($current_file)
          <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}"><span class="fa fa-download"></span>
            Download file</a>
          -
          <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}"><span
                class="fa fa-trash"></span> Delete file</a>
          @if(count($files) > 1)
            -
            <a id="delete-all-log" href="?delall=true"><span class="fa fa-trash"></span> Delete all files</a>
          @endif
        @endif
      </div>
    </div>
  </div>
    <footer class="pt-3 my-md-5 pt-md-3 border-top">
            <div class="row">
                <div class="col-12 col-md">
                    <small class="d-block mb-3 text-muted">Laravel Log Viewer by Rap2h | <a href="https://github.com/rap2hpoutre/laravel-log-viewer">Github</a> | <a href="https://github.com/rap2hpoutre/laravel-log-viewer/issues">Issues</a></small>
                </div>
            </div>
    </footer>
</div>
<!-- jQuery for Bootstrap -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<!-- FontAwesome -->
<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
<!-- Datatables -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function () {
    $('.table-container tr').on('click', function () {
      $('#' + $(this).data('display')).toggle();
    });
    $('#lognav').change(function () {
        window.location.replace(window.location.pathname + $(this).val());
    });
    $('#table-log').DataTable({
      "order": [2, 'desc'],
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
  });
</script>
</body>
</html>
