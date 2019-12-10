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
                    <h2 style="text-align:center;">Incident Detail (ID - {{ $item->id }})</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <table class="sm-table" cellspacing="0" width="100%">
						<tr>
							<td class="width_1">Date</td>
							<td class="width_2">{{ Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
						</tr>

						<tr>
							<td class="width_1">Time</td>
							<td class="width_2">{{ Carbon\Carbon::parse($item->time)->format('h:m A') }}</td>
						</tr>

						<tr>
							<td class="width_1">Details</td>
							<td class="width_2">{{ $item->details }}</td>
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
							<td class="width_1">Photo</td>
							<td class="width_2">
								<div>
									@foreach($photos as $photo)
										<img width="100" height="100" src="{{ ($photo->path) }}" />
									@endforeach
								</div>
							</td>
						</tr>

						<tr>
							<td class="width_1">Video</td>
							<td class="width_2">
								<div>
									@foreach($videos as $key => $video)
										<a href="{{ $video->path }}"><span class="aud_vid" >Video {{ ++$key }}</span></a>
										<video width="320" height="240" controls>
										  <source src="{{ $video->path }}">
										</video><br>
									@endforeach
								</div>
							</td>
						</tr>

						<tr>
							<td class="width_1">Audio</td>
							<td class="width_2">
								<div>
									@foreach($audios as $key => $audio)
										<a href="{{ $audio->path }}"><span class="aud_vid">Audio {{ ++$key }}</span></a>
										<audio controls>
										  <source src="{{ $audio->path }}">
										</audio><br>
									@endforeach
								</div>
							</td>
						</tr>

					</table>

    </div>
  </div>
