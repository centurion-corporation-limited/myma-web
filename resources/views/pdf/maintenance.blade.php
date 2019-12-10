<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<style>
body{ color:#73879c;}
.width_1{
	width:40%;
}
.x_title {
    border-bottom:1px solid #e6e9ed;
    padding: 1px 5px 6px;
}
.x_title h2 { font-size:18px;
}
.width_2{
	width:60%;
}
.width_2 div{
	//display:inline-block;
}
.width_2 img{
	padding-right: 10px;
}
.incident-table th, .incident-table td {border: 1px solid #ddd;
    text-align: center;
}
.incident-table { border: 1px solid #ddd;}

.incident-table tr:nth-of-type(2) {
    background: #f9f9f9;
}
.incident-table th {
    padding: 15px 0;
}
.sm-table{ width:50%;}
.sm-table td{ padding:15px 0;}
</style>
                <div class="x_panel">
                  <div class="x_title">
                    <h2 style="text-align:center;">Maintenance Detail (ID - {{ $item->id }})</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <table class="sm-table" cellspacing="0" width="100%">

                        <tr>
							<td class="width_1">User</td>
							<td class="width_2">{{ $item->user->name or '--'}}</td>
						</tr>
                        <tr>
							<td class="width_1">Dormitory</td>
							<td class="width_2">{{ $item->dormitory->name or '--' }}</td>
						</tr>
                        <tr>
							<td class="width_1">Location</td>
							<td class="width_2">{{ $item->location }}</td>
						</tr>
                        <tr>
							<td class="width_1">Reported at</td>
							<td class="width_2">{{ $item->created_at->format('d/m/Y H:i:A') }}</td>
						</tr>
						<tr>
							<td class="width_1">Status</td>
							<td class="width_2">{{ $item->status->name or '--' }}</td>
						</tr>

						<tr>
							<td class="width_1">Comments</td>
							<td class="width_2">{{ $item->comments }}</td>
						</tr>

						<tr>
							<td class="width_1">Picture</td>
							<td class="width_2">
                                @if($item->photo_1)
                                    <img src="{{ public_url($item->photo_1) }}" height="100" width="100">
                                @endif
                                @if($item->photo_2)
                                    <img src="{{ public_url($item->photo_2) }}" height="100" width="100">
                                @endif
                                @if($item->photo_3)
                                    <img src="{{ public_url($item->photo_3) }}" height="100" width="100">
                                @endif
                                @if($item->photo_4)
                                    <img src="{{ public_url($item->photo_4) }}" height="100" width="100">
                                @endif
                                @if($item->photo_5)
                                    <img src="{{ public_url($item->photo_5) }}" height="100" width="100">
                                @endif
                            </td>
						</tr>

                        @if($item->status_id == 3)
						<tr>
							<td class="width_1">Completed at</td>
							<td class="width_2">{{ isset($item->completed_at)?$item->completed_at->format('d/m/Y H:i:A'):'' }}</td>
						</tr>

						<tr>
							<td class="width_1">Remarks</td>
							<td class="width_2">{{ $item->remarks }}</td>
						</tr>

                        <tr>
							<td class="width_1">Uploaded after inspection</td>
							<td class="width_2">
                                @foreach($item->files as $file)
                                    @if($file->path)
                                        <img src="{{ $file->path }}" height="100" width="100">
                                    @endif
                                @endforeach
                            </td>
						</tr>
                        @endif
					</table>
    </div>
  </div>
</html>
