<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="noindex, nofollow">
  <title>Laravel log viewer</title>
  <link rel="stylesheet"
  href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
  integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
  crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
  <style>
    html {
      scroll-behavior: smooth;
    }
    
    body {
      padding: 25px 0;
    }
    
    h1 {
      font-size: 1.5em;
      margin-top: 0;
    }
    
    #table-log {
      font-size: 0.9rem;
    }
    
    .sidebar {
      font-size: 0.85rem;
      line-height: 1;
      position: sticky;
      position: -webkit-sticky;
      top: 25px;
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
      background-color: #8959df;
      border-color: #777;
      color: #fff;
    }
    
    .list-group-item {
      word-wrap: break-word;
      background-color: #f5f5f5;
    }
    
    .folder {
      padding-top: 15px;
    }
    
    .div-scroll {
      height: 60vh;
      overflow: hidden auto;
    }
    .nowrap {
      white-space: nowrap;
    }
    
  </style>
</head>
<body class="bg-light">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 mb-3">
        <div class="sidebar">
          <h5 class="text-secondary font-weight-normal text-monospace" style="fontweight: 400;"><i class="far fa-calendar-alt text-info" aria-hidden="true"></i> Laravel Log Viewer</h5>
          <p class="text-muted">- by Rap2h</p>
          <div class="pb-3">
            <a class="btn btn-outline-primary btn-lg btn-block rounded-0" href=""><i class="fas fa-redo mr-3"></i>Refresh</a>
          </div>
          <div class="list-group div-scroll">
            @foreach($folders as $folder)
            <div class="list-group-item">
              <a href="?f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}">
                <span class="fa fa-folder"></span> {{$folder}}
              </a>
              @if ($current_folder == $folder)
              <div class="list-group folder">
                @foreach($folder_files as $file)
                <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}&f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}" class="list-group-item rounded-0 @if ($current_file == $file) llv-active @endif">
                  {{$file}}
                </a>
                @endforeach
              </div>
              @endif
            </div>
            @endforeach
            @foreach($files as $file)
            <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
            class="list-group-item rounded-0 @if ($current_file == $file) llv-active @endif">
            {{$file}}
          </a>
          @endforeach
        </div>
      </div>
    </div>
    <div class="col-md-10 table-container bg-white py-3">
      @if ($logs === null)
      <div>
        Log file >50M, please download it.
      </div>
      @else
      <table id="table-log" class="table table-bordered table-hover table-responsive-sm dt-responsive" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
        <thead class="thead-light">
          <tr>
            @if ($standardFormat)
            <th>Level</th>
            <th>Context</th>
            <th>Date</th>
            @else
            <th>Line number</th>
            @endif
            <th>Content</th>
          </tr>
        </thead>
        <tbody>
          
          @foreach($logs as $key => $log)
          <tr data-display="stack{{{$key}}}">
            @if ($standardFormat)
            <td class="nowrap text-{{{$log['level_class']}}}">
              <span class="fa fa-{{{$log['level_img']}}}" aria-hidden="true"></span>&nbsp;&nbsp;{{$log['level']}}
            </td>
            <td class="text">{{$log['context']}}</td>
            @endif
            <td class="date">{{{$log['date']}}}</td>
            <td class="text">
              @if ($log['stack'])
              <button type="button"
              class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
              data-display="stack{{{$key}}}">
              <span class="fa fa-search"></span>
            </button>
            @endif
            {{{$log['text']}}}
            @if (isset($log['in_file']))
            <br/>{{{$log['in_file']}}}
            @endif
            @if ($log['stack'])
            <div class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">
              {{{ trim($log['stack']) }}}
            </div>
            @endif
          </td>
        </tr>
        @endforeach
        
      </tbody>
    </table>
    @endif
    <div class="p-3">
      @if($current_file)
      <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="fa fa-cloud-download-alt"></span> Download file
      </a>
      <span class="mx-3">|</span>
      <a id="clean-log" href="?clean={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="fa fa-sync"></span> Clean file
      </a>
      <span class="mx-3">|</span>
      <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="far fa-trash-alt"></span> Delete file
      </a>
      @if(count($files) > 1)
      <span class="mx-3">|</span>
      <a id="delete-all-log" href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
        <span class="fas fa-trash-alt"></span> Delete all files
      </a>
      @endif
      @endif
    </div>
  </div>
</div>
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
    $('#table-log').DataTable({
      "order": [$('#table-log').data('orderingIndex'), 'desc'],
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
    $('#delete-log, #clean-log, #delete-all-log').click(function () {
      return confirm('Are you sure?');
    });
  });
</script>
</body>
</html>
