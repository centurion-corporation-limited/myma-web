<p>Dear <b>{{ $name }}</b>,<p>

<p>A new maintenance request created with below details - </p>
<p>CaseID - {{ $case_id }}</p>
<p>Fin NO - {{ $fin_no or '' }}</p>
<p>Dorm - {{ $dorm }}</p>
<p>Location - {{ $location }}</p>
<p>Comments - {{ $comments }}</p>
@if($photo_1)
<p>
<img src="{{ public_url($photo_1) }}" height="100" width="100">
</p>
@endif
@if($photo_2)
<p>
<img src="{{ public_url($photo_2) }}" height="100" width="100">
</P>
@endif
@if($photo_3)
<p>
<img src="{{ public_url($photo_3) }}" height="100" width="100">
</P>
@endif
@if($photo_4)
<p>
<img src="{{ public_url($photo_4) }}" height="100" width="100">
</P>
@endif
@if($photo_5)
<p>
<img src="{{ public_url($photo_5) }}" height="100" width="100">
</P>
@endif
<p>
Regards,<br>
Customer Service Officer<br>
WLC
</p>
